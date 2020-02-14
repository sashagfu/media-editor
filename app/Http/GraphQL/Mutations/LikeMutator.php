<?php

namespace App\Http\GraphQL\Mutations;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Project;
use App\Notifications\LikeNotification;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class LikeMutator
{
    public function resolve($rootValue, array $args)
    {
        // TODO implement the resolver
    }

    public function handleProjectLike($root, $args)
    {
        $user_id = AuthHelper::myId();
        $project_id = $args['id'];
        $type = DBHelper::getMapByModel(Project::class);
        $project = Project::find($project_id);
        $project_author = $project->author;

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
            return $existing_like->user;
        }
    }

    public function handleCommentLike($root, $args)
    {
        $user_id = AuthHelper::myId();
        $comment_id = $args['id'];
        $type = DBHelper::getMapByModel(Comment::class);

        $existing_like = Like::whereLikeableType($type)
                             ->whereLikeableId($comment_id)
                             ->whereUserId($user_id)
                             ->first();

        if (!$existing_like) {
            $like = new Like();
            $like->user_id = $user_id;
            $like->likeable_id = $comment_id;
            $like->likeable_type = $type;
            $like->save();
            return $like->user;
        } else {
            $existing_like->delete();
            return $existing_like->user;
        }
    }
}
