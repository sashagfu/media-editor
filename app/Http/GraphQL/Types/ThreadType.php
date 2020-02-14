<?php

namespace App\Http\GraphQL\Types;

use App\Models\Thread;
use Illuminate\Support\Facades\Auth;

class ThreadType
{
    public function creatorId(Thread $thread)
    {
        return $thread->creator_id;
    }

    public function users(Thread $thread)
    {
        return $thread->users()
                      ->wherePivot('deleted_at', null)
                      ->get();
    }

    public function unreadMessagesCount(Thread $thread, $args)
    {
        if (isset($args['userId'])) {
            return $thread->userUnreadMessagesCount($args['userId']);
        }
        return $thread->userUnreadMessagesCount(Auth::id());
    }

    public function hidden(Thread $thread)
    {
        return $thread->participants->where('user_id', Auth::id())->first()->hidden;
    }

    public function active(Thread $thread, $args)
    {
        $userId = $args['userId'] ?? Auth::id();

        return $thread->participants->where('user_id', $userId)->first()->is_active;
    }
}
