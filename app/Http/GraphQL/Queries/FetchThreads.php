<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Thread;
use Illuminate\Support\Facades\Auth;

class FetchThreads
{
    const THREADS_PAGINATION_NUMBER = 10;

    public function resolve($root, array $args)
    {
        $user = Auth::user();

        return $threads = Thread::forUser($user->id)
                                ->latest('updated_at')
                                ->paginate(self::THREADS_PAGINATION_NUMBER);
    }
}
