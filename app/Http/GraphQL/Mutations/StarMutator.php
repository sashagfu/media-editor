<?php

namespace App\Http\GraphQL\Mutations;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Project;
use App\Models\Star;
use App\Models\User;
use App\Notifications\StarNotification;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Execution\Utils\Subscription;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class StarMutator
{
    public function handleProjectStar($root, $args)
    {
        $user_id = AuthHelper::myId();
        $project_id = $args['id'];
        $type = DBHelper::getMapByModel(Project::class);
        $project = Project::find($project_id);

        $existing_star = Star::whereStarableType($type)
                             ->whereStarableId($project_id)
                             ->whereUserId($user_id)
                             ->first();

        if (!$existing_star) {
            $star_data = [
                'user_id' => $user_id,
                'starable_id' => $project_id,
                'starable_type' => $type,
            ];
            $star = Star::create($star_data);

            $total_stars = $project->author->total_stars + 1;

            $project->author->update([
                'total_stars' => $total_stars,
            ]);

            $project->author->notify(new StarNotification($star));

            return $star->user;
        } else {
            $existing_star->delete();
            return $existing_star->user;
        }
    }
}
