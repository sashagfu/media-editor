<?php

namespace App\Http\GraphQL\Types;

use App\Models\Playlist;

class PlaylistType
{
    public function accessLevel(Playlist $playlist)
    {
        return $playlist->access_level;
    }

    public function authorId(Playlist $playlist)
    {
        return $playlist->author_id;
    }

    public function createdAt(Playlist $playlist)
    {
        return $playlist->created_at;
    }
}
