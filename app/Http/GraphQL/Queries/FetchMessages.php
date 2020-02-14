<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Thread;
use Illuminate\Support\Facades\Auth;

class FetchMessages
{
    const MESSAGES_PAGINATION_NUMBER = 12;

    public function resolve($root, array $args)
    {
        $thread = Thread::findOrFail($args['threadId']);

        $user = Auth::user();

        if (!$thread->users->where('id', $user->id)->first()) {
            throw new \Exception(trans('chat.wrong_access_rights'), 422);
        }

        $messages = $thread->messages()
                           ->orderBy('created_at', 'desc')
                           ->take(self::MESSAGES_PAGINATION_NUMBER)
                           ->get();

        return $messages->sortBy('created_at');
    }
}
