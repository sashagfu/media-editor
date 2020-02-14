<?php

namespace App\GraphQL\Mutation\Star;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Project;
use App\Models\Star;
use App\Notifications\StarNotification;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class HandleStarMutation extends Mutation
{
    protected $attributes = [
        'name' => 'handleStar',
    ];

    public function type()
    {
        return GraphQL::type('User');
    }

    public function args()
    {
        return [
            'projectId' => ['name' => 'projectId', 'type' => Type::int()],
        ];
    }

    public function rules()
    {
        return [
            'projectId'      => [
                'required',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $user_id = AuthHelper::myId();
        $project_id = $args['projectId'];
        $type = DBHelper::getMapByModel(Project::class);
        $project_author = Project::find($project_id)->author;

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

            $project_author->total_stars++;
            $project_author->save();

            if ($project_author->id != $user_id) {
                $project_author->notify(new StarNotification($star));
            }

            return $star->user;
        } else {
            $existing_star->delete();
            return null;
        }
    }
}
