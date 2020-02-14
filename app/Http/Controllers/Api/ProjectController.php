<?php

namespace App\Http\Controllers\Api;

use App\Models\Asset;
use App\Models\ProjectInput;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use App\Models\Project;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    const THUMB_NAME = 'thumb.jpg';

    public function createProject(Request $request)
    {
        $user = AuthHelper::me();
        $default_title = Project::DEFAULT_PROJECT_NAME . '-' . ($user->projects()->count() + 1);
        $title = $request->title ? $request->title : $default_title;
        $description = $request->description ? $request->description : null;
        $thumb_time = $request->thumbTime ? $request->thumbTime : null;

        $project = new Project();
        $project->author_id = $user->id;
        $project->title = $title;
        $project->description = $description;
        $project->thumb_time = $thumb_time;
        $project->save();

        if ($request->thumb) {
            $thumb = $request->thumb;
            $image = Image::uploadProjectThumb($thumb, $project->id);
            $project->thumb()->associate($image);
        }

        return $project;
    }

    public function updateProject(Request $request)
    {
        $project_id = $request->id;
        $user = AuthHelper::me();

        /** @var \App\Models\Project $project */
        $project = $user->projects()->findOrFail($project_id);
        $title = $request->title ? $request->title : $project->title;
        $description = $request->description ? $request->description : $project->description;
        $thumb_time = $request->thumbTime ? $request->thumbTime : $project->thumbTime;

        $project->author_id = $user->id;
        $project->title = $title;
        $project->description = $description;
        $project->thumb_time = $thumb_time;
        $project->layers = $request->layers;
        $project->save();

        // Update inputs (information about files which are used in project)
        \DB::transaction(function () use ($project, $request) {
            $project->inputs()->delete();
            foreach ($request->value as $input) {
                $project->inputs()
                    ->create([
                        'object_id' => $input['object']['id'] ?? $input['file']['id'],
                        'type'      => $input['type'] ?? $input['file']['fileType'],
                        'content'   => $input['content'] ?? null,
                        'length'    => $input['length'] * .001,
                        'layer_id'  => $input['layerId'],
                        'position'  => $input['position'] * .001,
                        'start_from'=> $input['startFrom'] * .001,
                        'transform' => $input['transform'],
                        'volume_levels' => $input['volumeControl'] ?? [],
                    ]);
            }
        });

        // Sync assets used in project
        $project_assets = [];
        foreach ($request->value as $input) {
            if (isset($input['file']['fileType']) &&
                $input['file']['fileType'] == Asset::MORPH_TYPE) {
                $project_assets[] = $input['file']['id'];
            }
        }
        $project->usedAssets()->sync($project_assets);

        if ($request->thumb) {
            $thumb = $request->thumb;
            $image = Image::uploadProjectThumb($thumb, $project->id);
            if ($project->thumb) {
                $project->thumb->delete();
            }
            $project->thumb()->associate($image);
        }

        return $project;
    }

    public function fetchProject($id)
    {
        $user = AuthHelper::me();

        /** @var \App\Models\Project $project */
        $project = $user->projects()->findOrFail($id);

        $project->value = $project->inputs
            ->map(function (ProjectInput $input) {
                return [
                    'id' => $input->id,
                    'file' => $input->getObjectData(),
                    'object' => $input->getObjectData(),
                    'type' => $input->type,
                    'length' => $input->length * 1000,
                    'layerId' => $input->layer_id,
                    'position' => $input->position * 1000,
                    'startFrom' => $input->start_from * 1000,
                    'transform' => $input->transform,
                    'volumeControl' => $input->volume_levels,
                ];
            })
            ->values()
            ->toArray();

        $project->makeHidden(['inputs']);

        return $project->toArray();
    }

    public function fetchUserProjects()
    {
        $user = AuthHelper::me();

        return $user->projects;
    }

    public function fetchProjectImages($id)
    {
        $user = AuthHelper::me();
        $project = $user->projects()->findOrFail($id);

        return collect($project->projectImages)
            ->map(function ($image) {
                return [
                    'uuid'     => $image->uuid,
                    'name'     => $image->file_name,
                    'height'   => $image->height,
                    'width'    => $image->width,
                    'thumb'    => $image->thumb_path,
                    'id'       => $image->id,
                    'filePath' => $image->file_path,
                    'fileType' => 'image',
                ];
            });
    }

    public function fetchProjectAudio($id)
    {
        $user = AuthHelper::me();
        $project = $user->projects()->findOrFail($id);

        return collect($project
            ->projectAudio
            ->map(
                function ($audio) {
                    return [
                        'uuid'          => $audio->uuid,
                        'name'          => $audio->name,
                        'waveformData'  => $audio->sprite,
                        'time'          => $audio->time,
                        'id'            => $audio->id,
                        'filePath'      => $audio->file_path,
                        'fileType'      => 'audio',
                    ];
                }
            ));
    }

    public function fetchProjectVideos($id)
    {
        $user = AuthHelper::me();
        $project = $user->projects()->findOrFail($id);

        return collect($project
            ->projectVideos
            ->map(
                function ($video) {
                    return [
                        'uuid' => $video->uuid,
                        'name'     => $video->name,
                        'filePath' => $video->file_path,
                        'id'       => $video->id,
                        'thumb'    => $video->thumbnail_path,
                        'sprite'   => $video->sprite_path,
                        'time'     => $video->time,
                        'frames'   => $video->frames,
                        'height'   => $video->height,
                        'width'    => $video->width,
                        'waveformData' => $video->waveform,
                        'fileType' => 'video',
                    ];
                }
            ));
    }

    public function fetchAllMedia($id)
    {
        $media = $this->fetchProjectImages($id)
            ->merge($this->fetchProjectAudio($id))
            ->merge($this->fetchProjectVideos($id));

        return $media;
    }

    public function deleteProject(Request $request)
    {
        $project_id = $request->id;
        $user = AuthHelper::me();
        $project = $user->projects()->findOrFail($project_id);

        if ($project->delete()) {
            return trans('projects.deleteSuccessful');
        };

        return trans('projects.deleteFailed');
    }
}
