<?php

namespace App\Http\GraphQL\Mutations;

use App\Helpers\AuthHelper;
use App\Models\Comment;
use App\Models\Project;
use App\Notifications\CommentNotification;
use App\Notifications\NewCommentNotification;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Execution\Utils\Subscription;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CommentMutator
{
    public function create($root, $args)
    {
        $data = $args['comment'];
        $user = Auth::user();
        $project = Project::findOrFail($data['projectId']);

        $comment = new Comment();
        $comment->project_id = $project->id;
        $comment->author_id = $user->id;
        $comment->text = $data['text'];
        $comment->parent_id = $data['parentId'] ?? null;
        $comment->save();

        if ($user->id !== $project->author->id) {
            $project->author->notify(new NewCommentNotification($comment));
        }

        return $comment;
    }

    public function delete($root, $args)
    {
        $user = AuthHelper::me();

        $comment_id = $args['id'];

        $comment = Comment::findOrFail($comment_id);

        if (!$comment->parent_id) {
            $comment->replies()->delete();
        }

        if ($comment->author->id === $user->id) {
            $comment->delete();

            Subscription::broadcast('commentDeleted', $comment);

            return $comment;
        }

        return \Response::json(trans('common.error_reload'), 422);
    }
}
