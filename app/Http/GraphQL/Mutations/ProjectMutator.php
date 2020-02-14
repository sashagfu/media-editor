<?php

namespace App\Http\GraphQL\Mutations;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Jobs\FireProjectExport;
use App\Jobs\UploadProjectFiles;
use App\Models\Asset;
use App\Models\Audio;
use App\Models\Image;
use App\Models\Project;
use App\Models\ProjectInput;
use App\Models\ProjectProcess;
use App\Models\Tag;
use App\Models\User;
use App\Models\Video;
use App\Models\ProjectView;
use App\Services\Studio\StudioException;
use Carbon\Carbon;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\Storage;
use Nuwave\Lighthouse\Execution\Utils\Subscription;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use File;
use Symfony\Component\Process\Process;

class ProjectMutator
{
    const FPS = 1;
    const THUMB_WIDTH = 160;
    const THUMB_HEIGHT = 90;
    const FRAME_WIDTH = 480;
    const FRAME_HEIGHT = 270;
    const THUMBS_PREFIX = 'video';
    const THUMBS_EXTENSION = 'jpg';
    const THUMB_NAME = 'thumb.jpg';
    const SPRITE_NAME = 'sprite.png';
    const SPRITE_CONVERT_WRAPPER = 'bash -c ';

    /**
     * How many seconds allowed for thumbnail generator process
     */
    const THUMB_GENERATE_TIMEOUT = 3600;

    public function create($root, $args)
    {
        $data = $args['project'];
        $user = Auth::user();

        $project = new Project();

        $project->title = $data['title'];
        $project->author_id = $user->id;
        $project->description = $data['description'];
        $project->status = Project::STATUS_DRAFT;
        $project->thumb_time = $data['thumbTime'] ?? null;
        $project->version = 1;

        $project->save();

        $tags = (isset($args['project']['tags'])) ? $args['project']['tags'] : null;

        if ($tags) {
            foreach ($tags as $new_tag) {
                $tag = Tag::where('name', $new_tag['name'])->first();

                if (!$tag) { // if tag doesn't exists
                    $tag = Tag::create([
                        'name' => $new_tag['name'],
                    ]);
                }

                $project->tags()->attach($tag->id);
            }
        }

        return $project;
    }

    public function update($root, $args)
    {
        $project = Project::findOrFail($args['project']['id']);
        $user = Auth::user();
        $data = $args['project'];

        // If project not published, then do rename
        if ($project->status !== Project::STATUS_PUBLISHED) {
            $project->title = $data['title'] ?? $project->title;
        }
        $project->description = $data['description'] ?? $project->description;
        $project->thumb_time = $data['thumbTime'] ?? $project->thumb_time;
        $project->layers = $data['layers'] ?? $project->layers;

        $pinned = $data['pinned'] ?? null;
        // unpin old pined project
        if (!$project->pinned && $pinned) {
            $pinned_project = $user->projects->where('pinned', true)->first();
            if ($pinned_project) {
                $pinned_project->pinned = false;
                $pinned_project->save();
            }
        }
        $project->pinned = $pinned ?? $project->pinned;
        $project->save();

        if (isset($args['project']['value']) && count($args['project']['value'])) {
            \DB::transaction(
                function () use ($project, $args) {
                    foreach ($args['project']['value'] as $value) {
                        $input = $project->inputs()->where('uuid', $value['uuid'])->first();

                        if (!$input) {
                            if (isset($value['unlinked'])) {
                                $unlinked_file = $this->unlinkVideo($value);
                            } elseif (isset($value['showAs'])) {
                                $unlinked_file = $this->unlinkAudio($value);
                            }

                            if (isset($unlinked_file)) {
                                $class = (new \ReflectionClass($unlinked_file))->getShortName();
                                switch ($class) {
                                    case 'Video':
                                        $value['type'] = Video::MORPH_TYPE;
                                        break;
                                    case 'Audio':
                                        $value['type'] = Audio::MORPH_TYPE;
                                        break;
                                    case 'Asset':
                                        $value['type'] = Asset::MORPH_TYPE;
                                        break;
                                }
                                $value['object']['id'] = $unlinked_file->id;
                                $value['file']['id'] = $unlinked_file->id;
                            }

                            $project
                                ->inputs()
                                ->create(
                                    [
                                        'uuid' => $value['uuid'],
                                        'object_id' => $value['object']['id'] ?? $value['file']['id'],
                                        'type' => $value['type'] ?? $value['file']['fileType'],
                                        'length' => $value['length'] * .001,
                                        'layer_id' => $value['layerId'],
                                        'project_id' => $project->id,
                                        'position' => $value['position'] * .001,
                                        'start_from' => $value['startFrom'] * .001,
                                        'transform' => $value['transform'],
                                        'volume_levels' => $value['volumeControl'] ?? [],
                                        'effects' => $value['effects'] ?? ProjectInput::DEFAULT_EFFECTS,
                                    ]
                                );
                        } else {
                            // If it is unlinked video
                            if (isset($value['unlinked'])) {
                                $unlinked_file = $this->unlinkVideo($value);
                                $value['object']['id'] = $unlinked_file->id;
                                $value['file']['id'] = $unlinked_file->id;
                            }

                            $input->length = $value['length'] * .001;
                            $input->object_id = $value['object']['id'] ?? $value['file']['id'];
                            $input->layer_id = $value['layerId'];
                            $input->position = $value['position'] * .001;
                            $input->start_from = $value['startFrom'] * .001;
                            $input->transform = $value['transform'];
                            $input->volume_levels = $value['volumeControl'] ?? [];
                            $input->effects = $value['effects'] ?? ProjectInput::DEFAULT_EFFECTS;
                            $input->save();
                        }

                        // Delete inputs
                        $actual_inputs = collect($args['project']['value'])->pluck('uuid');
                        $project->inputs()->whereNotIn('uuid', $actual_inputs)->delete();
                    }
                }
            );
        } else {
            if (count($project->inputs)) {
                $project->inputs()->delete();
            }
        }

        if (isset($args['project']['tags'])) {
            $tags = collect($args['project']['tags']);

            $old_tags = $project->tags;
            $new_tags = [];
            $existed_tags = [];

            foreach ($tags as $tag) {
                if (isset($tag['id'])) {
                    $existed_tag = Tag::findOrFail($tag['id']);
                } else {
                    $existed_tag = Tag::where('name', $tag['name'])->first();
                }

                if ($existed_tag) { // check if tag already exists
                    $existed_tags[] = $existed_tag;
                } else {
                    $new_tag = Tag::create([
                        'name' => $tag['name'],
                    ]);
                    $new_tags[] = $new_tag;
                }
            }

            $tags = collect(array_merge($existed_tags, $new_tags));

            $deleted_tags = $old_tags->diff($tags); // get tags which was deleted from project

            $tags = $tags->pluck('id')
                ->toArray();

            $project->tags()->sync($tags);

            $project = $project->fresh();

            foreach ($deleted_tags as $tag) {  // remove unnecessary tags
                if ($tag->projects->count() <= 1) {
                    $tag->delete();
                }
            }
        }

        return $project;
    }

    public function delete($root, $args)
    {
        $project = Project::findOrFail($args['id']);

        $project->delete();

        return $project;
    }

    public function render($root, $args)
    {
        $project = Project::findOrFail($args['id']);

        // NOTE: Job id is not actual when you you use `sync` driver
        /** @var int $job_id */
//        $job_id = dispatch_now(new FireProjectExport($project));
        $job_id = app(Dispatcher::class)
            ->dispatch(new FireProjectExport($project, Project::STATUS_RENDERED));

        // Set status WAITING for the project with job ID (Job ID is useful for canceling job in future)
        $project->processes()->create(
            [
                'job_id' => $job_id,
                'status' => ProjectProcess::STATUS_WAITING,
            ]
        );

        return $project->fresh();
    }

    public function publish($root, $args)
    {
        $project = Project::findOrFail($args['id']);

        if ($project->status != Project::STATUS_RENDERED) {
            throw new \Exception(trans('projects.unable_to_publish'));
        }

        $project->status = Project::STATUS_PUBLISHED;
        $project->save();

        return $project;
    }

    public function renderPublish($root, $args)
    {
        $project = Project::findOrFail($args['id']);

        // NOTE: Job id is not actual when you you use `sync` driver
        /** @var int $job_id */
//        $job_id = dispatch_now(new FireProjectExport($project));
        $job_id = app(Dispatcher::class)
            ->dispatch(new FireProjectExport($project, Project::STATUS_PUBLISHED));

        // Set status WAITING for the project with job ID (Job ID is useful for canceling job in future)
        $project->processes()->create([
            'job_id' => $job_id,
            'status' => ProjectProcess::STATUS_WAITING
        ]);

        return $project->fresh();
    }

    public function cloneProject($root, $args)
    {
        $user = Auth::user();
        $project = Project::findOrFail($args['id']);

        if ($user->id !== $project->author->id) {
            throw new \Exception(trans('projects.wrong_access_rights'), 403);
        }

        $project_copy = $project->replicate();
        $project_copy->uuid = Uuid::uuid4()->toString();
        $project_copy->title = $project->title . ' - (' . trans('projects.copy') . ')';
        $project_copy->status = Project::STATUS_DRAFT;
        $project_copy->push();

        // Clone s3 folder
        $this->createStorageFolder($project_copy, $project);

        // Clone relations
        $this->cloneProjectRelations($project_copy, $project);

        return $project_copy;
    }

    public static function changeThumbnail($root, $args)
    {
        $project = Project::findOrFail($args['projectId']);
        $thumb_time = $args['thumbTime'];

        $user = Auth::user();

        // TODO make job

        set_time_limit(0);

        $project_dir = config('filesystems.disks.uploads.tmp_path')
            . "project_{$project->id}/";

        $tmp_path = config('filesystems.disks.uploads.root') . $project_dir;

        // Create project directory
        Storage::disk('uploads')->makeDirectory("/tmp/project_{$project->id}");

        try {
            $assets_path = config('filesystems.disks.s3.assets_path');

            $thumb_path = $tmp_path . self::THUMB_NAME;

            $remote_source_path = 'users/' . $user->id . '/projects/' . $project->id . '/assets/'
                . $project->version . '/FULL.mp4';

            $make_thumb_cmd = sprintf(
                "%s -y -i \"%s\" -ss %s -vframes 1 \"%s\"",
                env('FFMPEG'),
                Storage::disk('s3')->url($remote_source_path),
                $thumb_time,
                $thumb_path
            );

            // Execute ffmpeg command
            $process = new Process($make_thumb_cmd);
            $process->setTimeout(0);
            $process->run();

            $thumb_name = 'thumb-' . md5(time());
            $thumb = $project->path . $assets_path . $project->version . "/{$thumb_name}.jpg";

            Storage::disk('s3')->put('/' . $thumb, file_get_contents($thumb_path));

            Storage::disk('s3')->setVisibility($thumb, 'public');

            // Add thumb path to the project model
            $storage_base = config('filesystems.disks.s3.storage_base');

            $thumb_path = $storage_base . $thumb;

            $project->update([
                'thumb_time' => $thumb_time,
                'thumb_path' => $thumb_path
            ]);

            Subscription::broadcast('projectUpdated', $project->fresh());

            return $project;
        } catch (\Exception $e) {
            logger($e->getMessage());
            throw new StudioException(
                'Thumb has not been created',
                StudioException::THUMBS_HAS_NOT_BEEN_CREATED,
                $e
            );
        }
    }

    private function cloneProjectRelations($project_copy, $project)
    {
        // Copy project videos
        foreach ($project->projectVideos as $video) {
            $new_video = $video->replicate();
            $new_video->uuid = Uuid::uuid4()->toString();
            $new_video = $this->updateFilePaths(
                $new_video,
                $video,
                $project_copy,
                $project
            );
            $new_video->videoable_id = $project_copy->id;
            $new_video->push();

            $related_input = $project->videoInputs
                ->where('object_id', $video->id)
                ->first();

            if ($related_input) {
                $new_input = $related_input->replicate();
                $new_input->uuid = Uuid::uuid4()->toString();
                $new_input->project_id = $project_copy->id;
                $new_input->object_id = $new_video->id;
                $new_input->push();
            }
        }

        // Copy project audios
        foreach ($project->projectAudio as $audio) {
            $new_audio = $audio->replicate();
            $new_audio->uuid = Uuid::uuid4()->toString();
            $new_audio = $this->updateFilePaths(
                $new_audio,
                $audio,
                $project_copy,
                $project
            );
            $new_audio->audioable_id = $project_copy->id;
            $new_audio->push();

            $related_input = $project->audioInputs
                ->where('object_id', $audio->id)
                ->first();

            if ($related_input) {
                $new_input = $related_input->replicate();
                $new_input->uuid = Uuid::uuid4()->toString();
                $new_input->project_id = $project_copy->id;
                $new_input->object_id = $new_audio->id;
                $new_input->push();
            }
        }

        // Copy project images
        foreach ($project->projectImages as $image) {
            $new_image = $image->replicate();
            $new_image->uuid = Uuid::uuid4()->toString();
            $new_image = $this->updateFilePaths(
                $new_image,
                $image,
                $project_copy,
                $project
            );
            $new_image->imageable_id = $project_copy->id;
            $new_image->push();

            $related_input = $project->imageInputs
                ->where('object_id', $image->id)
                ->first();

            if ($related_input) {
                $new_input = $related_input->replicate();
                $new_input->uuid = Uuid::uuid4()->toString();
                $new_input->project_id = $project_copy->id;
                $new_input->object_id = $new_image->id;
                $new_input->push();
            }
        }

        // Copy assets inputs
        $asset_inputs = $project->inputs->where('type', Asset::MORPH_TYPE);

        foreach ($asset_inputs as $input) {
            $new_input = $input->replicate();
            $new_input->uuid = Uuid::uuid4()->toString();
            $new_input->project_id = $project_copy->id;
            $new_input->push();
        }
    }

    private function updateFilePaths($new_file, $old_file, $project_copy, $project)
    {
        $new_file->file_path = str_replace(
            "/$project->id/uploads",
            "/$project_copy->id/uploads",
            $old_file->file_path
        );
        if ($old_file->thumbnail_path) {
            $new_file->thumbnail_path = str_replace(
                "/$project->id/uploads",
                "/$project_copy->id/uploads",
                $old_file->thumbnail_path
            );
        }
        if ($old_file->sprite_path) {
            $new_file->sprite_path = str_replace(
                "/$project->id/uploads",
                "/$project_copy->id/uploads",
                $old_file->sprite_path
            );
        }
        if ($old_file->waveform) {
            $new_file->waveform = str_replace(
                "/$project->id/uploads",
                "/$project_copy->id/uploads",
                $old_file->waveform
            );
        }
        if ($old_file->thumb_path) {
            $new_file->thumb_path = str_replace(
                "/$project->id/uploads",
                "/$project_copy->id/uploads",
                $old_file->thumb_path
            );
        }
        if ($old_file->sprite) {
            $new_file->sprite = str_replace(
                "/$project->id/uploads",
                "/$project_copy->id/uploads",
                $old_file->sprite
            );
        }

        return $new_file;
    }

    private function createStorageFolder($project_copy, $project)
    {
        $user = Auth::user();
        $projects_dir = "/users/{$user->id}/projects";

        $project_dir = "$projects_dir/$project->id/uploads";

        $project_copy_dir = "$projects_dir/$project_copy->id/uploads";

        $project_files = Storage::disk('s3')->allFiles($project_dir);

        foreach ($project_files as $file) {
            $new_loc = str_replace("/$project->id/", "/$project_copy->id/", $file);
            Storage::disk('s3')->copy($file, $new_loc);
        }

        Storage::disk('s3')->makeDirectory($project_copy_dir);
    }

    public function uploadFile($root, $args)
    {
        if (empty($args['projectId'])) {
            $data = $args['project'];

            $project = new Project();
            $project->title = $data['title'];
            $project->description = $data['description'];
            $project->status = Project::STATUS_DRAFT;
            $project->author_id = AuthHelper::myId();
            $project->save();
        } else {
            $project = Project::find($args['projectId']);
        }

        $project_id = $args['projectId'] ?? $project->id;
        $file = $args['url'];
        $mime_type = $this->getMimeType($file)['type'];
        $ext = $this->getMimeType($file)['ext'];
//        $filename = $this->getMimeType($file)['filename'];
        $filename = md5(time() . $this->getMimeType($file)['filename']);
        // Create Project Project Path
        $storage_base = 'https://' . env('AWS_ACCESS_MEDIA_BUCKET') . '.s3.amazonaws.com/';
        $base_dir = 'users/' . AuthHelper::myId() . '/projects/' . $project_id . '/uploads';

        Storage::disk('s3')->makeDirectory($base_dir);

        // Get URL of a stored file, i. e. "https://bucket.s3.amazonaws.com/user%20video.mp4"
        $file_url = $file;

        // Get file name only, i. e. "user video.mp4"
        $file_name_orig = urldecode(str_replace($storage_base, '', $file_url));

        $image = strpos($mime_type, 'image') !== false;
        $audio = strpos($mime_type, 'audio') !== false;
        $video = strpos($mime_type, 'video') !== false;

        if ($image) {
            // Create images path
            $images_dir = $base_dir . '/images';
            $thumbs_dir = $images_dir . '/thumbs';

            Storage::disk('s3')->makeDirectory($images_dir);
            Storage::disk('s3')->makeDirectory($thumbs_dir);

            return Image::mediaEditorImageUplaod(
                $file,
                $images_dir,
                $ext,
                $project_id,
                $storage_base
            );
        }
        if ($audio) {
            // Create audio folder in project
            $audio_dir = $base_dir . '/audios';
            $audio_local_dir = $audio_dir;
            Storage::disk('s3')->makeDirectory($audio_dir);
            Storage::disk('uploads')->makeDirectory($audio_local_dir);

            // Generate new file name, i.e. "source-real_file-name.mp3"
            $source_name = 'source-' . $filename . '.' . $ext;

            // Full file path, i. e. "users/1/projects/1/source-real_file-name.mp3"
            $full_path = $audio_local_dir . '/' . $source_name;

            // Set also local path
            $local_path = storage_path('uploads/') . $full_path;

            // Connect then to s3 service
            /**
             * @var \League\Flysystem\AwsS3v3\AwsS3Adapter $s3s
             */
            $s3s = Storage::disk('s3')->getDriver()->getAdapter();
            $s3s->getClient()->getObject(
                [
                    'Bucket' => env('AWS_ACCESS_MEDIA_BUCKET'),
                    'Key' => $file_name_orig,
                    'SaveAs' => $local_path,
                ]
            );

            // Move file
            if (!Storage::disk('s3')->exists($full_path)) {
                Storage::disk('s3')->move($file_name_orig, $local_path);
            }

            $audio = Audio::mediaEditorAudioUpload(
                $file_name_orig,
                $filename,
                $full_path,
                $ext,
                $audio_local_dir,
                $project_id,
                $storage_base
            );

            if (isset($args['isReady']) && $args['isReady']) {
                $this->addProjectInput($project, $audio);
            }

            return $audio;
        }
        if ($video) {
            // Create video path
            $video_dir = $base_dir . '/videos/' . $filename;
            $video_local_dir = $video_dir;
            Storage::disk('s3')->makeDirectory($video_dir);
            Storage::disk('uploads')->makeDirectory($video_local_dir);

            // Create thumbs path
            $thumbs_dir = $video_dir;
            $thumbs_local_dir = $thumbs_dir;
            Storage::disk('s3')->makeDirectory($thumbs_dir);
            Storage::disk('uploads')->makeDirectory($thumbs_local_dir);

            // Generate new file name, i.e. "source-real_file-name.mp3"
            $source_name = 'source-' . $filename . '.' . $ext;

            // Full file path, i. e. "users/1/projects/1/source-real_file-name.avi"
            $full_path = $video_local_dir . '/' . $source_name;

            // Set also local path
            $local_path = storage_path('uploads/') . $full_path;

            // Connect then to s3 service
            /**
             * @var \League\Flysystem\AwsS3v3\AwsS3Adapter $s3s
             */
            $s3s = Storage::disk('s3')->getDriver()->getAdapter();
            $s3s->getClient()->getObject(
                [
                    'Bucket' => env('AWS_ACCESS_MEDIA_BUCKET'),
                    'Key' => $file_name_orig,
                    'SaveAs' => $local_path,
                ]
            );

            // Convert file to mp4
            if ($ext !== 'mp4') {
                $new_name = $filename . '.mp4';
                FFMpeg::fromDisk('uploads')
                    ->open($full_path)
                    ->export()
                    ->toDisk('uploads')
                    ->inFormat(new \FFMpeg\Format\Video\X264('aac'))
                    ->save($video_local_dir . '/' . $new_name);

                // Redifine values
                File::delete($local_path);
                $source_name = $new_name;
                $full_path = $video_local_dir . '/' . $source_name;
                $local_path = storage_path('uploads/') . $full_path;
            }

            // Prepare Video wave form
            // First convert video to wav file
            $video_wav_name = 'video-wav.wav';
            $media = FFMpeg::fromDisk('uploads')
                ->open($full_path);

            $streams = $media
                ->getFFProbe()
                ->streams($local_path);

            $wav_json_file = null;

            if (0 < count($streams->audios())) {
                $media->export()
                    ->toDisk('uploads')
                    ->inFormat(new \FFMpeg\Format\Audio\Wav())
                    ->save($video_local_dir . '/' . $video_wav_name);

                $wav_video_local_file = storage_path('uploads/') . $video_local_dir . '/' . $video_wav_name;
                $wav_json_name = Audio::generateJsonWav($wav_video_local_file, $video_local_dir, $filename);

                // Set separated audio name
                $audio_name = 'source-' . $filename . '.mp3';

                // Upload the separated mp3 file to S3
                Storage::disk('s3')->putFileAs(
                    $video_dir,
                    new \Illuminate\Http\File($wav_video_local_file),
                    $audio_name,
                    'public'
                );

                // Set muted video name
                $muted_video_name = 'source-' . $filename . '_muted.mp4';

                // Render muted video
                $muted_video_file = Video::muteVideo($video_local_dir, $muted_video_name, $local_path);

                // Upload the muted video to S3
                Storage::disk('s3')->putFileAs(
                    $video_dir,
                    new \Illuminate\Http\File($muted_video_file),
                    $muted_video_name,
                    'public'
                );

                // Define pathes
                $wav_json_file = $video_local_dir . '/' . $wav_json_name;
                $wav_json_local_file = storage_path('uploads/') . $video_local_dir . '/' . $wav_json_name;

                // Upload the wavJson to S3
                Storage::disk('s3')->putFileAs(
                    $video_dir,
                    new \Illuminate\Http\File($wav_json_local_file),
                    $wav_json_name,
                    'public'
                );
            }


            // Move file
            if (!Storage::disk('s3')->exists($full_path)) {
                Storage::disk('s3')->move($file_name_orig, $full_path);
            }

            $video = $this->processVideo(
                $local_path,
                $thumbs_dir,
                $thumbs_local_dir,
                $storage_base,
                $full_path,
                $file_name_orig,
                $wav_json_file,
                $video_local_dir,
                $project_id
            );

            // If uploaded ready project
            if (isset($args['isReady']) && $args['isReady']) {
                $this->addProjectInput($project, $video);
            }

            return $video;
        }

        return trans('errors.error_reload');
    }

    public function uploadFiles($root, $args)
    {
        if (empty($args['projectId'])) {
            $data = $args['project'];

            $project = new Project();
            $project->title = $data['title'];
            $project->description = $data['description'];
            $project->status = Project::STATUS_DRAFT;
            $project->author_id = AuthHelper::myId();
            $project->save();
        } else {
            $project = Project::find($args['projectId']);
        }

        $job_id = app(Dispatcher::class)
            ->dispatch(new UploadProjectFiles($project, $args['urls'], $args['isReady'] ?? false));

        return [
            'message' => 'Uploading started',
            'statusCode' => 200,
        ];
    }

    public function processVideo(
        $local_path,
        $thumbs_dir,
        $thumbs_local_dir,
        $storage_base,
        $full_path,
        $file_name_orig,
        $wav_json_file,
        $video_local_dir,
        $project_id
    ) {
        // Set time limit to infinite
        set_time_limit(0);

        // Configure FFMpeg params, i. e. "-vf fps=1,scale=160x90"
        $generate_thumbs_opts = sprintf(
            ' -vf fps=%d,scale=%dx%d',
            self::FPS,
            self::FRAME_WIDTH,
            self::FRAME_HEIGHT
        );

        // Define generate thumbs command, i. e.
        // ffmpeg -i "https://bucket.s3.amazonaws.com/users/1/videos/20161116065847/source.mp4" -vf fps=1,scale=160x90
        //      "storage/uploads/users/1/videos/20161116065847/thumbs/video%d.jpg"

        $cmd_generate_thumbs = sprintf(
            '%s -i "%s" %s %s/%s%%d.%s',
            env('FFMPEG', '/bin/false'),
            $local_path,
            $generate_thumbs_opts,
            storage_path('uploads/' . $thumbs_local_dir),
            self::THUMBS_PREFIX,
            self::THUMBS_EXTENSION
        );

        // Execute ffmpeg command
        $process = new Process($cmd_generate_thumbs);
        $process->setTimeout(self::THUMB_GENERATE_TIMEOUT);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Gather all thumbs
        $thumbs = File::allFiles(storage_path('uploads/' . $thumbs_local_dir));

        /**
         * @var \Symfony\Component\Finder\SplFileInfo $thumb
         */
        $key = (int)round(count($thumbs) / 2);
        $thumb = $thumbs["$key"];

        // Upload the thumbs to S3
        Storage::disk('s3')->putFileAs(
            $thumbs_dir,
            new \Illuminate\Http\File($thumb),
            self::THUMB_NAME,
            'public'
        );

        // Define make sprite command, i. e,
        // gm convert "storage/uploads/users/1/videos/20161116065847/thumbs/video{1..123}.jpg" --append
        //      "storage/uploads/users/1/videos/20161116065847/thumbs/sprite.jpg"
        $cmd_make_sprite = sprintf(
            '%s "%s convert %s/%s{1..%d}.%s -append %s"',
            self::SPRITE_CONVERT_WRAPPER,
            env('GRAPHICKSMAGIC', '/bin/false'),
            storage_path('uploads/' . $thumbs_local_dir),
            self::THUMBS_PREFIX,
            count($thumbs),
            self::THUMBS_EXTENSION,
            storage_path('uploads/' . $thumbs_local_dir . '/' . self::SPRITE_NAME)
        );

        // Execute graphicsmagic command
        $process = new Process($cmd_make_sprite);
        $process->run();

        // Upload the sprite to S3
        $uploaded_sprite = Storage::disk('s3')->putFileAs(
            $thumbs_dir,
            new \Illuminate\Http\File(storage_path('uploads/' . $thumbs_local_dir) . '/' . self::SPRITE_NAME),
            self::SPRITE_NAME,
            'public'
        );

        // Get video information
        $media_info = FFMpeg::fromDisk('uploads')
            ->open($full_path)
            ->getStreams()
            ->first();
        $width = $media_info->get('width');
        $height = $media_info->get('height');
        $time = $media_info->get('duration') * 1000;

        // Create a new object in DB
        $video = new Video();
        $video->file_path = $storage_base . $full_path;
        $video->thumbnail_path = $storage_base . $thumbs_dir . '/' . self::THUMB_NAME;
        $video->sprite_path = $storage_base . $uploaded_sprite;
        $video->name = $file_name_orig;
        $video->time = $time;
        $video->height = $height;
        $video->width = $width;
        $video->frames = count($thumbs);
        $video->waveform = $storage_base . $wav_json_file;
        $video->author()->associate(AuthHelper::me());
        $video->videoable_id = $project_id;
        $video->videoable_type = DBHelper::getMapByModel(Project::class);
        $video->save();

        Storage::disk('uploads')->deleteDirectory($video_local_dir);
        return $video;
    }

    public function unlinkAudio($input)
    {
        if ($input['file']['fileType'] == Audio::MORPH_TYPE) {
            return $this->unlinkProjectAudio($input);
        }
        return $this->unlinkAssetAudio($input);
    }

    public function unlinkProjectAudio($input)
    {
        $storage_base = 'https://' . env('AWS_ACCESS_MEDIA_BUCKET') . '.s3.amazonaws.com/';

        $base_dir = 'users/' . AuthHelper::myId() . '/projects/' . $input['file']['projectId'] . '/uploads';

        $video_name = pathinfo($input['file']['filePath'], PATHINFO_FILENAME);

        // Remove "source-" from video folder name
        $video_remote_dir = $base_dir . '/videos/' . str_replace('source-', '', $video_name);

        $audio_remote_path = $video_remote_dir . "/{$video_name}.mp3";

        $audio = new Audio();
        $audio->file_path = $storage_base . $audio_remote_path;
        $audio->name = $input['file']['name'];
        $audio->sprite = $input['file']['waveformData'];
        $audio->time = $input['file']['time'];
        $audio->audioable_id = $input['file']['projectId'];
        $audio->save();

        return $audio;
    }

    public function unlinkAssetAudio($input)
    {
        $project = Project::findOrFail($input['file']['projectId']);

        $asset = $project->assets()->where('type', Asset::AUDIO_TYPE)->first();

        return $asset;
    }

    public function incrementViews($root, $args)
    {
        $project = Project::findOrFail($args['id']);

        $user = Auth::user();

        $view = $project->views()->where('user_id', $user->id)->first();

        if ($view) {
            $now = new Carbon();
            $last_view = new Carbon($view->last_view);

            if ($now->diffInSeconds($last_view) < 3) {
                $view->last_view = $now->addSeconds(60);
                $view->save();

                throw new \Exception('Too many attempts');
            }

            $view->views_count += 1;
            $view->save();

            return $project;
        }

        $view = new ProjectView();
        $view->user_id = $user->id;
        $view->project_id = $project->id;
        $view->views_count = 1;
        $view->last_view = new Carbon();
        $view->save();

        return $project;
    }

    public function togglePinProject($root, $args)
    {
        $project = Project::findOrFail($args['id']);
        $user = Auth::user();

        if ($user->id !== $project->author->id) {
            throw new \Exception(trans('projects.wrong_access_rights'), 403);
        }

        // Looking for pinned project
        $pinned_project = $user->projects()->wherePinned(true)
            ->where('id', '!=', $project->id)
            ->first();

        // Unpin pinned project
        if ($pinned_project) {
            $pinned_project->pinned = false;
            $pinned_project->save();
        }

        $project->pinned = !$project->pinned;
        $project->save();

        return $project;
    }

    public function unlinkVideo($input)
    {
        if ($input['file']['fileType'] == Video::MORPH_TYPE) {
            return $this->unlinkProjectVideo($input);
        }
        return $this->unlinkAssetVideo($input);
    }

    public function unlinkProjectVideo($input)
    {
        $storage_base = 'https://' . env('AWS_ACCESS_MEDIA_BUCKET') . '.s3.amazonaws.com/';

        $base_dir = 'users/' . AuthHelper::myId() . '/projects/' . $input['file']['projectId'] . '/uploads';

        $video_name = pathinfo($input['file']['filePath'], PATHINFO_FILENAME);

        // Remove "source-" from video folder name
        $video_remote_dir = $base_dir . '/videos/' . str_replace('source-', '', $video_name);

        // Create ink to muted video
        $muted_video_remote_path = $video_remote_dir . "/{$video_name}_muted.mp4";

        $video = new Video();
        $video->author_id = Auth::user()->id;
        $video->file_path = $storage_base . $muted_video_remote_path;
        $video->thumbnail_path = $input['file']['thumbPath'];
        $video->name = $input['file']['name'];
        $video->sprite_path = $input['file']['spritePath'];
        $video->waveform = $input['file']['waveformData'];
        $video->time = $input['file']['time'];
        $video->frames = $input['file']['frames'];
        $video->height = $input['file']['height'];
        $video->width = $input['file']['width'];
        $video->videoable_id = $input['file']['projectId'];
        $video->save();

        return $video;
    }

    public function unlinkAssetVideo($input)
    {
        $project = Project::findOrFail($input['file']['projectId']);

        $asset = $project->assets()->where('type', Asset::VIDEO_TYPE)->first();

        return $asset;
    }

    private function getMimeType($file)
    {
        // Return the array value
        $info = pathinfo($file);
        $ext = strtolower($info['extension']);
        $filename = $info['filename'];

        return [
            'type' => UploadProjectFiles::MIME_TYPES[$ext],
            'ext' => $ext,
            'filename' => $filename,
        ];
    }

    private function addProjectInput($project, $file)
    {
        $project->inputs()->create(
            [
                'object_id' => $file->id,
                'type' => $file::MORPH_TYPE,
                'length' => $file->time * .001,
                'layer_id' => 1,
                'position' => 0,
                'start_from' => 0,
                'transform' => [
                    'scale' => 1,
                    'position' => ['x' => 0, 'y' => 0],
                    'size' => ['width' => 1, 'height' => 1],
                ],
                'volume_levels' => [],
            ]
        );
    }
}
