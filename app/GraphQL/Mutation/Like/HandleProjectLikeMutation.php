<?php

namespace App\GraphQL\Mutation\Like;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Like;
use App\Models\Post;
use App\Models\Project;
use App\Notifications\LikeNotification;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class HandleProjectLikeMutation extends Mutation
{
    protected $attributes = [
        'name' => 'handleProjectLike',
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

        $existing_like = Like::whereLikeableType($type)
            ->whereLikeableId($project_id)
            ->whereUserId($user_id)
            ->first();

        if (!$existing_like) {
            $like_data = [
                'user_id' => $user_id,
                'likeable_id' => $project_id,
                'likeable_type' => $type,
            ];

            $like = Like::create($like_data);

            $project_author->total_likes++;
            $project_author->save();

            if ($project_author->id != $user_id) {
                $project_author->notify(new LikeNotification($like));
            }

            return $like->user;
        } else {
            $existing_like->delete();
            return null;
        }
    }
}
