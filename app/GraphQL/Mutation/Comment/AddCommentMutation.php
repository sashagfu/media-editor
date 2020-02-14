<?php

namespace App\GraphQL\Mutation\Comment;

use App\Helpers\AuthHelper;
use App\Models\Comment;
use App\Models\Project;
use App\Notifications\CommentNotification;
use Folklore\GraphQL\Support\Mutation;
use GraphQL;

class AddCommentMutation extends Mutation
{
    protected $attributes = [
        'name' => 'addComment',
    ];

    public function type()
    {
        return GraphQL::type('Comment');
    }

    public function args()
    {
        return [
            'comment' => ['name' => 'comment', 'type' => GraphQL::type('CommentInput')],
        ];
    }

    public function rules()
    {
        return [
            'comment'      => [
                'required',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $user = AuthHelper::me();
        $project_id = $args['comment']['projectId'];
        $comment_text = $args['comment']['text'];
        $parent_id = (isset($args['comment']['parentId'])) ? $parent_id = $args['comment']['parentId'] : null;
        $project = Project::find($project_id);

        $comment_data = [
            'author_id' => $user->id,
            'project_id' => $project_id,
            'text' => $comment_text,
            'parent_id' => $parent_id,
        ];

        $comment = Comment::create($comment_data);

        if ($user->id !== $project->author->id) {
            $project->author->notify(new CommentNotification($project, $user));
        }

        return $comment;
    }
}
