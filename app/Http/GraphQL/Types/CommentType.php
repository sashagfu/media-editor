<?php

namespace App\Http\GraphQL\Types;

use App\Models\Comment;

class CommentType
{
    public function projectId(Comment $comment)
    {
        return $comment->project_id;
    }

    public function parentId(Comment $comment)
    {
        return $comment->parent_id;
    }

    public function authorId(Comment $comment)
    {
        return $comment->author_id;
    }
}
