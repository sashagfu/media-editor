<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Playlist;
use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FetchPlaylists
{
    public function resolve($rootValue, array $args)
    {
        if (isset($args['userId'])) {
            $playlists = Playlist::where('author_id', $args['userId'])
                                 ->get();

            return $playlists;
        }

        return null;
    }
}
