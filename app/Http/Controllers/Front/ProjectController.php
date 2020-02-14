<?php

namespace App\Http\Controllers\Front;

use App\Helpers\DBHelper;
use App\Models\Image;
use App\Models\Project;
use App\Notifications\NewPostNotification;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use App\Models\Post;
use App\Models\Video;
use App\Models\Circle;
use App\Models\User;
use App\Models\FlagReason;
use Illuminate\Support\Facades\Auth;
use Session;

class ProjectController extends Controller
{
    protected $project;

    public function __construct(Request $request)
    {
        if ($request->uuid) {
            $this->project = Project::whereUuid($request->uuid);

            $this->project = $this->project
                ->with('comments', 'author', 'stars', 'likes', 'tags')
                ->first();

            $this->project->load(['assets' => function ($query) {
                $query->where('version', $this->project->last_rendered_version);
            }]);
        }
    }

    public function singleProject(Request $request, $uuid)
    {
        $user = Auth::user();

        $canView = !!$this->project->shares->where('user_id', Auth::user()->id)->first();

        if ($this->project->status == Project::STATUS_PUBLISHED
            ||
            $this->project->author_id == $user->id
            ||
            ($this->project->status == Project::STATUS_RENDERED && $canView)
        ) {
            return view('front.project-single')
                ->with(
                    [
                        'project' => $this->project,
                        'user'    => $user,
                        'deprecated' => $request->deprecated ?? json_encode(false)
                    ]
                );
        }

        abort(404);
    }

    public function statistics(Request $request, $uuid)
    {
        $user = Auth::user();

        if ($this->project->status === Project::STATUS_PUBLISHED
            &&
            $user->id == $this->project->author->id
        ) {
            return view('front.project-statistics')
                ->with(
                    [
                        'project' => $this->project,
                        'user'    => $user,
                    ]
                );
        }

        return abort(404);
    }
}
