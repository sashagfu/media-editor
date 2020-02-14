<?php

namespace App\Services\Studio;

use App\Models\Asset;
use App\Models\Audio;
use App\Models\Image;
use App\Models\Project;
use App\Models\ProjectCredit;
use App\Services\Studio\Entities\Font;
use App\Services\Studio\Entities\Inputs\InputContract;
use App\Services\Studio\Entities\Inputs\InputSlide;
use App\Services\Studio\Entities\OutputContract;
use App\Services\Studio\Entities\PresetContract;
use App\Services\Studio\Repositories\InputRepository;
use App\Services\Studio\Repositories\PresetRepository;
use App\Services\Studio\Tools\Media;
use Aws\Laravel\AwsFacade;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use App\Services\Sox\Sox;
use Mockery\Exception;
use Nuwave\Lighthouse\Execution\Utils\Subscription;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Services\Studio\Repositories\OutputRepository;
use File;

class Studio implements StudioContract
{
    const FPS = 1;
    const THUMB_WIDTH = 160;
    const THUMB_HEIGHT = 90;
    const FRAMES = 1;
    const FRAME_WIDTH = 480;
    const FRAME_HEIGHT = 270;
    const THUMBS_PREFIX = 'video';
    const THUMBS_EXTENSION = 'jpg';
    const THUMB_NAME = 'thumb.jpg';
    const COVER_NAME = 'cover.jpg';
    const WAVEFORM_NAME = 'waveform.json';
    const SPRITE_NAME = 'sprite.png';
    const VIDEO_EXT = 'mp4';
    const AUDIO_EXT = 'mp3';

    /**
     * How many seconds allowed for thumbnail generator process
     */
    const THUMB_GENERATE_TIMEOUT = 3600;

    /**
     *  Number of digits after comma
     */
    const ROUND_PLACES = 8;

    /** @var \App\Models\Project $project */
    protected $project;

    /**
     * @var \Aws\ElasticTranscoder\ElasticTranscoderClient $elastic
     */
    protected $elastic;

    /** @var \Illuminate\Support\Collection $presets Output presets */
    protected $presets;

    /** @var string $pipelineId Pipeline for adding job into */
    protected $pipelineId;

    /** @var \App\Services\Studio\Repositories\PresetRepositoryContract Presets repository */
    protected $presetRepository;

    /** @var \App\Services\Studio\Repositories\OutputRepositoryContract Output repository */
    protected $outputRepository;

    /** @var \App\Services\Studio\Repositories\InputRepositoryContract Input repository */
    protected $inputRepository;

    /**
     * @var bool Status of rendering
     */
    protected $processed = false;

    protected $tmp_path;

    protected $project_dir;

    protected $final_name;

    public function __construct(Project $project)
    {
        // Set edited project
        $this->project = $project;

        $this->project->version = 1;

        // Set project temp path
        $this->project_dir = config('filesystems.disks.uploads.tmp_path')
                             . "project_{$this->project->id}/";

        $this->tmp_path = config('filesystems.disks.uploads.root')
                          . $this->project_dir;

        $this->final_name = "final_{$this->project->id}.mp4";

        // Reset progress project
        $this->updateProject(15);


        Storage::disk('uploads')->makeDirectory($this->project_dir);

        // Process pipeline
        $this->pipelineId = config('aws.pipelines.common');

        // Default presets
        $this->presets = collect();

        // Instances
        $this->elastic = AwsFacade::createClient('ElasticTranscoder');
        $this->presetRepository = new PresetRepository;
        $this->outputRepository = new OutputRepository($project);
        $this->inputRepository = new InputRepository($project->inputs()->get());
    }

    /**
     *  Presets of output
     *
     * @param array $presets
     * @return $this
     */
    public function setPresets(array $presets)
    {
        $presets = collect($presets)
            ->unique()
            ->filter(function ($presetKey) {
                return ! $this->presets->filter(function (PresetContract $preset) use ($presetKey) {
                    return $preset->getKey() == $presetKey;
                })->count();
            })
            ->map(function ($presetKey) {
                /** @var \App\Services\Studio\Entities\PresetContract $preset */
                $preset = $this->presetRepository->findOrCreate($presetKey);
                $this->outputRepository->createFor($preset);
                return $preset;
            });

        $this->presets = $this->presets->merge($presets);

        return $this;
    }

    /**
     * @return bool
     * @throws StudioException
     */
    public function export()
    {
        try {
            // Delete old outputs if exists
            $this->log('Deleting old outputs...');

            $assets_path = config('filesystems.disks.s3.assets_path');

            $project_dir = $this->project->path . $assets_path . $this->project->version;

            if (Storage::disk('s3')->exists($project_dir)) {
                Storage::disk('s3')->deleteDirectory($project_dir);
            }

            /** @var array $outputs */
            $outputs = $this->getOutputs();

            /** @var \Aws\Result $result */
            $result = $this->elastic->createJob([
                'PipelineId' => $this->pipelineId,
                'Inputs' => $this->getInputs(),
                'Outputs' => $outputs
            ]);

            /** @var array $job */
            $job = $result->get('Job');

            $this->log('Elastic job is submitted');

            $dots = '...';
            while ($job['Status'] == "Submitted" || $job['Status'] == "Progressing") {
                sleep(10);
                $job = $this->elastic->readJob(['Id' => $job['Id']])->get('Job');

                $this->log("Job status: {$job['Status']} $dots");

                $dots .= '.';
            }

            if ($job['Status'] == "Complete") {
                $this->log("Elastic job is complete");

                $this->log('Update files permissions' . PHP_EOL);

                $this->createProjectCredits();

                foreach ($outputs as $output) {
                    Storage::disk('s3')->setVisibility($output['Key'], 'public');
                }

                $this->updateProject(15);
                $audio_channel = $this->makeAudioChannel('mp3');
                $this->updateProject(20);

                /** @var string $status */
                $status = $job['Status'];
                if ($this->soundHaveToBeReplaced()) {
                    $this->log('Starting edit audio...');
                    $this->replaceSound($audio_channel);
                    $this->updateProject(25);
                } else {
                    foreach ($outputs as $output) {
                        $this->createAsset(Asset::FULL_TYPE, $output['Key']);
                    }
                }

                /**
                 * @var string $tmp_path
                 *
                 * Path for temporary files $tmp_path
                 */
                $tmp_path = $this->tmp_path;

                $this->outputRepository
                    ->all()
                    ->each(
                        function (OutputContract $output) use ($tmp_path) {
                            // Render slides
                            $this->renderSlides($output, $tmp_path);
                            $this->updateProject(30);

                            // Render muted video
                            $this->makeMutedVideo($output, $tmp_path);
                            $this->updateProject(40);

                            // Render thumbs
                            $this->generateThumbs($output, $tmp_path);
                            $this->updateProject(50);

                            // If thumb time selected, render thumb
                            if ($this->project->thumb_time) {
                                $this->generateThumb($output, $tmp_path);
                                $this->updateProject(60);
                            }

                            // Render sprite
                            $this->makeSprite($tmp_path);
                            $this->updateProject(70);
                        }
                    );

                // Render audio
                $this->makeAudio($audio_channel);
                $this->updateProject(80);

                // Render wave form
                $this->makeWaveForm($audio_channel);
                $this->updateProject(90);

                $this->processed = true;
            } else {
                $status = $job['Status'];
            }

            if ($status == 'Error') {
                throw new StudioException('Elastic job error', StudioException::ELASTIC_JOB_EXCEPTION);
            }

            Storage::disk('uploads')->deleteDirectory($this->project_dir);

            return $status;
        } catch (\Exception $e) {
            $this->updateProject(null, Project::STATUS_FAILED);
            logger($e->getMessage());
            $this->log($e->getMessage());

            throw new StudioException('Processing failed', StudioException::ELASTIC_JOB_EXCEPTION, $e);
        }
    }

    /**
     *  Ready output files
     *
     * @return array
     */
    public function outputs()
    {
        return ! $this->processed
            ? []
            : $this->outputRepository
                ->all()
                ->mapWithKeys(function (OutputContract $output) {
                    return [$output->getPreset()->getKey() => $output->getFilePath()];
                })
                ->toArray();
    }

    public function getInputs()
    {
        return $this->inputRepository->getInputsCompositions();
    }

    protected function getOutputs()
    {
        return $this->outputRepository->all()
            ->map(function (OutputContract $output) {
                return [
                    'Key' => $output->getFilePath(),
                    'PresetId' => $output->getPreset()->getId()
                ];
            })
            ->toArray();
    }

    /**
     *  Replace audio channel in result video
     *
     * @throws StudioException
     */
    protected function replaceSound($audio_channel)
    {

        /** @var string $tmp_pathPath for temporary files $tmp_path */
        $tmp_path = $this->tmp_path;
        try {
            // replace audio channel in each input
            $this->outputRepository->all()
                ->each(function (OutputContract $output) use ($audio_channel, $tmp_path) {

                    $tmp_final = $tmp_path . $this->final_name;

                    $this->log('Adding audio channel to video' . PHP_EOL);

                    $replace_audio_cmd = sprintf(
                        "%s -y -i \"%s\" -i \"%s\" -c:v copy -map 0:v:0 -map 1:a:0 -strict -2 \"%s\"",
                        env('FFMPEG'),
                        Storage::disk('s3')->url($output->getFilePath()),
                        $audio_channel,
                        $tmp_final
                    );

                    $replace_command = new Process($replace_audio_cmd);

                    $replace_command->setTimeout(0);
                    $replace_command->run();

                    // Generate final video folder and file names
                    $final_video_folder = "{$this->project->path}assets/{$this->project->version}/";
                    $final_video_name = Asset::FULL_TYPE . '.' . Asset::VIDEO_EXT;
                    $final_video_path = $final_video_folder . $final_video_name;

                    // Creating file with replaced audio
                    if ($replace_command->isSuccessful()) {
                        $this->log("Creating new file with replaced audio: $final_video_path");

                        // Check if final video without replaced sound already exists
                        if (Storage::disk('s3')->exists($final_video_path)) {
                            Storage::disk('s3')->delete($final_video_path);
                        }

                        // Store final video (replace)
                        Storage::disk('s3')->putFileAs(
                            $final_video_folder,
                            new \Illuminate\Http\File($tmp_final),
                            $final_video_name,
                            'public'
                        );

                        $this->createAsset(Asset::FULL_TYPE, $output->getPreset()->getKey());

                        $tmpDir = config('filesystems.disks.s3.tmp_path');

                        // Deleting directory with tmp files
                        Storage::disk('s3')->deleteDirectory($this->project->path . $tmpDir);
                    } else {
                        throw new ProcessFailedException($replace_command);
                    }
                });
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->log($e->getMessage());

            throw new StudioException(
                'Sound has not been replaced',
                StudioException::SOUND_HAS_NOT_BEEN_REPLACED,
                $e
            );
        }
    }

    protected function makeMutedVideo($output, $tmp_path)
    {
        try {
            // Mute video process

            // Log to queue console about job run status
            $this->log('Creating muted video');

            $assets_path = config('filesystems.disks.s3.assets_path');
            $tmp_muted = "{$tmp_path}muted_{$this->project->id}.mp4";

            $mute_video_cmd = sprintf(
                "%s -f lavfi -i aevalsrc=0 -i \"%s\" -vcodec copy" .
                " -acodec aac -map 0:0 -map 1:0 -shortest -strict experimental -y \"%s\"",
                env('FFMPEG'),
                Storage::disk('s3')->url($output->getFilePath()),
                $tmp_muted
            );

            $mute_command = new Process($mute_video_cmd);

            $mute_command->setTimeout(0);
            $mute_command->run();

            $muted_video = $this->project->path . $assets_path . $this->project->version .
                           '/' . Asset::VIDEO_TYPE . '.' . Asset::VIDEO_EXT;

            Storage::disk('s3')->put($muted_video, file_get_contents($tmp_muted));

            // set public status
            Storage::disk('s3')->setVisibility($muted_video, 'public');

            $this->createAsset(Asset::VIDEO_TYPE, $output->getPreset()->getKey());

            // Remove temp files
            $tmp_muted = config('filesystems.disks.uploads.tmp_path') .
                         "muted_{$this->project->id}.mp4";
            Storage::disk('uploads')->delete($tmp_muted);
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->log($e->getMessage());

            throw new StudioException(
                'Muted video has not been created',
                StudioException::SOUND_HAS_NOT_BEEN_REPLACED,
                $e
            );
        }
    }

    protected function makeSprite($tmp_path)
    {
        try {
            $outputs = $this->getOutputs();

            foreach ($outputs as $output) {
                Storage::disk('s3')->setVisibility($output['Key'], 'public');
            }

            $this->log('Creating video sprite');

            $assets_path = config('filesystems.disks.s3.assets_path');

            $thumbs_dir = 'thumbs/';

            $tmp_sprite = $tmp_path . $thumbs_dir . self::SPRITE_NAME;

            $thumbs = File::allFiles($tmp_path . $thumbs_dir);

            // Define make sprite command, i. e,
            // gm convert "storage/uploads/users/1/videos/20161116065847/thumbs/video{1..123}.jpg" --append
            //      "storage/uploads/users/1/videos/20161116065847/thumbs/sprite.jpg"
            $thumbs_count = count($thumbs);
            $make_sprite_cmd = sprintf(
                "bash -c \" %s convert %s -append %s\"",
                env('GRAPHICKSMAGIC'),
                $tmp_path . $thumbs_dir . "thumb{1..{$thumbs_count}}.jpg",
                $tmp_sprite
            );

            $make_sprite_process = new Process($make_sprite_cmd);
            $make_sprite_process->setTimeout(0);
            $make_sprite_process->run();

            $sprite_final = $this->project->path . $assets_path . $this->project->version .
                           '/' . self::SPRITE_NAME;

            Storage::disk('s3')->put($sprite_final, file_get_contents($tmp_sprite));

            // set public status
            Storage::disk('s3')->setVisibility($sprite_final, 'public');

            // Remove temp files
            Storage::disk('uploads')->deleteDirectory(
                $this->project_dir . $thumbs_dir
            );
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->log($e->getMessage());

            throw new StudioException(
                'Sprite has not been created',
                StudioException::SOUND_HAS_NOT_BEEN_REPLACED,
                $e
            );
        }
    }

    public function makeWaveForm($audio_channel)
    {
        $tmp_path = $this->tmp_path;
        $wav_path = "{$tmp_path}audio_to_wave_form_{$this->project->id}.wav";

        $make_wav_cmd = sprintf(
            "sox %s -c 2 -t wav %s",
            $audio_channel,
            $wav_path
        );

        $make_wav_process = new Process($make_wav_cmd);
        $make_wav_process->setTimeout(0);
        $make_wav_process->run();

        $tmp_waveform = "{$tmp_path}waveform_{$this->project->id}.json";

        $make_waveform_cmd = sprintf(
            "wav2json %s --channels left right mid side min max -o %s",
            $wav_path,
            $tmp_waveform
        );

        $make_waveform_process = new Process($make_waveform_cmd);
        $make_waveform_process->setTimeout(0);
        $make_waveform_process->run();

        $assets_path = config('filesystems.disks.s3.assets_path');

        $waveform_path = $this->project->path . $assets_path . $this->project->version .
                       '/' . self::WAVEFORM_NAME;

        Storage::disk('s3')->put($waveform_path, file_get_contents($tmp_waveform));
        Storage::disk('s3')->setVisibility($waveform_path, 'public');

        $tmp_wav = config('filesystems.disks.uploads.tmp_path') .
                        "audio_to_wave_form_{$this->project->id}.wav";
        $tmp_waveform = config('filesystems.disks.uploads.tmp_path') .
                        "waveform_{$this->project->id}.json";

        // Remove temp files
        Storage::disk('uploads')->delete($tmp_wav);
        Storage::disk('uploads')->delete($tmp_waveform);
    }

    public function makeAudio($audio_channel)
    {
        $assets_path = config('filesystems.disks.s3.assets_path');
        $final_audio = $this->project->path . $assets_path . $this->project->version .
                       '/' . Asset::AUDIO_TYPE . '.' . Asset::AUDIO_EXT;

        Storage::disk('s3')->put($final_audio, file_get_contents($audio_channel));

        // set public status
        Storage::disk('s3')->setVisibility($final_audio, 'public');

        $this->createAsset(Asset::AUDIO_TYPE);
    }

    public function generateThumbs($output, $tmp_path)
    {
        // Set time limit to infinite
        set_time_limit(0);

        try {
            $assets_path = config('filesystems.disks.s3.assets_path');

            $thumbs_dir = 'thumbs/';

            $thumb_path = "{$thumbs_dir}thumb%d.jpg";

            Storage::disk('uploads')->makeDirectory($this->project_dir . $thumbs_dir);

            $tmp_thumb = $tmp_path . $thumb_path;

            // Define generate thumbs command, i. e.
            // ffmpeg -i
            // "https://bucket.s3.amazonaws.com/users/1/videos/20161116065847/source.mp4"
            // -vf fps=1,scale=160x90
            // "storage/uploads/users/1/videos/20161116065847/thumbs/video%d.jpg"
            $make_thumbs_cmd = sprintf(
                "%s -i \"%s\" -vf fps=1,scale=%dx%d \"%s\"",
                env('FFMPEG'),
                Storage::disk('s3')->url($output->getFilePath()),
                self::FRAME_WIDTH,
                self::FRAME_HEIGHT,
                $tmp_thumb
            );

            // Execute ffmpeg command
            $process = new Process($make_thumbs_cmd);
            $process->setTimeout(0);
            $process->run();

            $thumbs = File::allFiles($tmp_path . $thumbs_dir);

            $this->setAssetsFrames(count($thumbs));

            $key = (int)round(count($thumbs) / 2);
            $thumb = $thumbs["$key"];

            $thumb_dir = $this->project->path . $assets_path . $this->project->version . '/';

            Storage::disk('s3')->putFileAs(
                $thumb_dir,
                new \Illuminate\Http\File($thumb),
                self::THUMB_NAME,
                'public'
            );

            $storage_base = config('filesystems.disks.s3.storage_base');

            $thumb_path = $storage_base . $thumb_dir . self::THUMB_NAME;

            $this->project->update([
                    'thumb_path' => $thumb_path,
                ]);
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->log($e->getMessage());

            throw new StudioException(
                'Thumbs has not been created',
                StudioException::THUMBS_HAS_NOT_BEEN_CREATED,
                $e
            );
        }

        // reset time limit
        set_time_limit(config('app.time_limit'));
    }

    public function generateThumb($output, $tmp_path)
    {
        // Set time limit to infinite
        set_time_limit(0);

        try {
            $assets_path = config('filesystems.disks.s3.assets_path');

            $thumb_path = $tmp_path . self::THUMB_NAME;

            $make_thumb_cmd = sprintf(
                "%s -i \"%s\" -ss %s -vframes 1 \"%s\"",
                env('FFMPEG'),
                Storage::disk('s3')->url($output->getFilePath()),
                $this->project->thumb_time,
                $thumb_path
            );

            // Execute ffmpeg command
            $process = new Process($make_thumb_cmd);
            $process->setTimeout(0);
            $process->run();

            $thumb = $this->project->path . $assets_path . $this->project->version . '/' . self::THUMB_NAME;

            // Store final video (replace)
            Storage::disk('s3')->put($thumb, file_get_contents($thumb_path));

            Storage::disk('s3')->setVisibility($thumb, 'public');

            // Add thumb path to the project model
            $storage_base = config('filesystems.disks.s3.storage_base');

            $thumb_path = $storage_base . $thumb;

            $this->project->update([
                'thumb_path' => $thumb_path
            ]);
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->log($e->getMessage());

            throw new StudioException(
                'Thumb has not been created',
                StudioException::THUMBS_HAS_NOT_BEEN_CREATED,
                $e
            );
        }
    }

    public function renderSlides($output, $tmp_path)
    {
        if ($this->project->slideInputs()->count() < 1) {
            return;
        }

        try {
            Storage::disk('s3')->setVisibility($output->getFilePath(), 'public');

            // Log to queue console about job run status
            $this->log('Adding slides to video' . PHP_EOL);

            $tmp_with_slides_name = "tmp_with_slides_{$this->project->id}.mp4";
            $tmp_with_slides = $tmp_path . $tmp_with_slides_name;
            $final_video = $output->getFilePath();

            $this->project->slideInputs()
                ->each(
                    function ($slide) use (
                        $output,
                        $tmp_with_slides,
                        $tmp_with_slides_name
                    ) {
                        // Render slide in video
                        InputSlide::renderOneSlide(
                            $slide,
                            $output,
                            $tmp_with_slides,
                            $tmp_with_slides_name
                        );
                    }
                );

            if (Storage::disk('s3')->exists($final_video)) {
                Storage::disk('s3')->delete($final_video);
            }

            // Store final video (replace)
            Storage::disk('s3')->put($final_video, file_get_contents($tmp_with_slides));
            Storage::disk('s3')->setVisibility($output->getFilePath(), 'public');

            // Remove temp files
            $remove_tmp_with_slides = new Process("rm {$tmp_with_slides}");
            $remove_tmp_with_slides->run();
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->log($e->getMessage());

            throw new StudioException(
                'Slide has not been rendered',
                StudioException::SLIDE_HAS_NOT_BEEN_RENDERED,
                $e
            );
        }
    }

    /**
     *  Create layer audio track and return path to it
     *
     * @param int $layer_id
     * @return string
     * @throws StudioException
     */
    protected function getLayerSound(int $layer_id)
    {
        $this->log("Making layer $layer_id sound...");

        /** @var \Illuminate\Support\Collection $inputs */
        $inputs = $this->inputRepository
            ->all()
            ->filter(function (InputContract $input) use ($layer_id) {
                return $input->layer_id === $layer_id;
            })
            ->sortBy(function (InputContract $input) {
                return $input->position;
            });

        /** @var Sox $sox */
        $sox = new Sox();

        /** @var string $tmp_pathPath for temporary files $tmp_path */
        $tmp_path = $this->tmp_path;

        // To form audio track settings
        $last_end_point = $inputs->reduce(function ($prev_end_point, InputContract $input) use ($sox) {

            // Add silence on an empty place
            if ((float)$prev_end_point < $input->position) {
                $silence = Sox::input('-n')
                    ->pipe()
                    ->channels(config('sox.channels'))
                    ->rate(config('sox.rate'))
                    ->cut(0, $input->position - (float)$prev_end_point);
                $sox->addInput($silence);
            }

            $audio_input = Sox::input($input->getAudioChannel())
                ->pipe()
                ->channels(2)
                ->rate('44100')
                ->cut(0, $input->length);

            $sox->addInput($audio_input);

            return $input->end_point;
        });

        // Add silence at the end (if it's needed) same track duration as project length
        if ($last_end_point < $this->project->length) {
            $silence = Sox::input('-n')
                ->pipe()
                ->channels(2)
                ->rate('44100')
                ->cut(0, $this->project->length - $last_end_point);

            $sox->addInput($silence);
        }

        try {
            $this->log("Saving layer track");

            /** @var string $file_name Temporary file with layer sound */
            $file_name = "project_{$this->project->id}-layer_$layer_id.wav";

            // Create layer audio file
            $sox->saveAs($tmp_path . $file_name)
                ->process();

            $process = new Process("soxi -D $tmp_path$file_name");

            $process->run();

            if ($process->isSuccessful()) {
                $this->log("Layer track duration: {$process->getOutput()}");
            }

            $this->tmp_files = $tmp_path . $file_name;

            return $tmp_path . $file_name;
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->log($e->getMessage());

            throw new StudioException(
                'Sound channel has not been created',
                StudioException::LAYER_SOUND_HAS_NOT_BEEN_CREATED,
                $e
            );
        }
    }

    /**
     *  Check if project sound have to be replaced (after Elastic rendering)
     *
     * @return bool
     */
    protected function soundHaveToBeReplaced()
    {
        // True if:
        // Rule 1. There are audio inputs in project
        if (!!$this->project->inputs()->typeAudio()->count()) {
            return true;
        }
        // Rule 2. More than one layer has inputs with sound
        $second_rule_status = !! collect($this->project->layers)->filter(function ($layer) {
            return !! $this->project->inputs()
                ->withSound()
                ->where('layer_id', $layer['id'])
                ->count();
        })->count();

        // Rule 2 is positive
        if (!! $second_rule_status) {
            return true;
        }

        // Rule 3. There are layers which have inputs with sound and volume levels for some of them have been changed
        $third_rule_status = collect($this->project->layers)->filter(function ($layer) {
            return $layer['volume'] !== 1
                && !! $this->project->inputs()
                    ->withSound()
                    ->where('layer_id', $layer['id'])
                    ->count();
        })->count();

        // Rule 3 is positive
        if ($third_rule_status) {
            return true;
        }

        return false;
    }

    protected function makeAudioChannel($ext = 'wav')
    {
        /** @var Sox $sox */
        $sox = Sox::mix();

        collect($this->project->layers)
            ->filter(function ($layer) {
                /** @var \Illuminate\Support\Collection $layer_inputs */
                $layer_inputs = $this->inputRepository->all()
                    ->filter(function (InputContract $input) use ($layer) {
                        return $input->layer_id == $layer['id'];
                    });

                return $layer['volume'] > 0 && $layer_inputs->isNotEmpty();
            })
            ->each(function ($layer) use ($sox) {
                $audio = $this->getLayerSound($layer['id']);

                if (!file_exists($audio)) {
                    throw new Exception("Audio file $audio is not exists");
                }

                /** @var \App\Services\Sox\Classes\InputInterface $layer_input Sox input of layer sound */
                $layer_input = Sox::input($audio)
                    ->pipe()
                    ->channels(2)
                    ->rate('44100')
                    ->volume($layer['volume']);

                $sox->addInput($layer_input);
            });

        /** @var string $tmp_pathPath for temporary files $tmp_path */
        $tmp_path = $this->tmp_path;

        $audio_channel = $tmp_path ."project_{$this->project->id}_audio_channel." . $ext;

        try {
            $sox->saveAs($audio_channel)
                ->process();
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->log($e->getMessage());

            throw new StudioException('Audio channel has not been created');
        }
        return $audio_channel;
    }

    protected function createAsset($asset_type, $presetKey = null)
    {
        try {
            $asset_ext = ($asset_type == Asset::FULL_TYPE || $asset_type == Asset::VIDEO_TYPE)
                ? self::VIDEO_EXT
                : self::AUDIO_EXT;

            $assets_path = config('filesystems.disks.s3.assets_path');
            $storage_base = config('filesystems.disks.s3.storage_base');

            $relative_path = $this->project->path . $assets_path . $this->project->version
                             . '/' . $asset_type . '.' . $asset_ext;

            $file_path = $storage_base . $relative_path;

            $time = FFMpeg::fromDisk('s3')
                          ->open($relative_path)
                          ->getDurationInSeconds() * 1000;

            switch ($presetKey) {
                case '720p':
                    $height = 720;
                    $width  = 1280;
                    break;
            }

            $asset_data = [
                'project_id' => $this->project->id,
                'type'       => $asset_type,
                'version'    => $this->project->version,
                'file_path'  => $file_path,
                'time'       => $time,
                'width'      => $width ?? null,
                'height'     => $height ?? null,
            ];

            // Create Asset
            Asset::create($asset_data);
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->log($e->getMessage());

            throw new StudioException("Asset{$asset_type}, has not been created");
        }
    }

    protected function updateProject($progress, $status = null)
    {
        $project_data = [];
        if ($progress) {
            $project_data['progress'] = $progress;
        }
        if ($status) {
            $project_data['status'] = $status;
        }
        $project = Project::find($this->project->id);
        $project->update($project_data);

        try {
            Subscription::broadcast('projectUpdated', $project->fresh());
        } catch (\Exception $e) {
            logger($e->getMessage());
            $this->log($e->getMessage());
        }

        return $this->project;
    }

    private function createProjectCredits()
    {
        $inputs = $this->project->inputs()
                                ->where('type', Asset::MORPH_TYPE)
                                ->get();

        /*
            If own saved clips should not be added to credentials uncomment this lines
        */
//        $inputs = $inputs->filter(function ($input) {
//            return $input->object->project->author_id !== $this->project->author->id;
//        });

        if (! $inputs->count()) {
            return;
        }

        $this->log('Create project credits'. PHP_EOL);

        foreach ($inputs as $input) {
            $input_author = $input->object->project->author;
            $input_project = $input->object->project;
            $credit = new ProjectCredit();
            $credit->project_id = $this->project->id;
            $credit->version = $this->project->version;
            $credit->details = [
                'type' => $input->object->type,
                'from' => $input->position * 1000,
                'to' => ($input->length + $input->position) * 1000,
                'percentages' => round((($input->length * 1000) * 100 ) / $this->project->duration),
                'project' => [
                    'id' => $input_project->id,
                    'uuid' => $input_project->uuid,
                    'title' => $input_project->title,
                    'version' => $input->object->version,
                ],
                'author' => [
                    'id' => $input_author->id,
                    'username' => $input_author->username,
                    'displayName' => $input_author->display_name,
                    'email' => $input_author->email,
                ]
            ];
            $credit->save();
        }
    }

    private function log($message)
    {
        if (App::runningInConsole()) {
            echo "$message\r\n";
        }
    }

    private function setAssetsFrames($frames)
    {
        $assets = Asset::where('project_id', $this->project->id)
            ->where('version', $this->project->version)
            ->get();

        foreach ($assets as $asset) {
            $asset->frames = $frames;
            $asset->save();
        }
    }
}
