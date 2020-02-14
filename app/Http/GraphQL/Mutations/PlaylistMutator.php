<?php

namespace App\Http\GraphQL\Mutations;

use App\Models\Project;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Playlist;
use Ramsey\Uuid\Uuid;

class PlaylistMutator
{
    public function create($rootValue, array $args)
    {
        $data = $args['playlist'];

        $playlist = new Playlist();
        $playlist->name = $data['name'];
        $playlist->author_id = Auth::user()->id;
        $playlist->access_level = $data['accessLevel'] ?? $playlist->access_level;
        $playlist->save();

        return $playlist;
    }

    public function update($root, $args)
    {
        $data = $args['playlist'];

        $playlist = Playlist::findOrFail($data['id']);
        $playlist->name = $data['name'];
        $playlist->author_id = $data['authorId'];
        $playlist->access_level = $data['accessLevel'] ?? $playlist->access_level;
        $playlist->save();

        return $playlist;
    }

    public function attachProject($root, $args)
    {
        $user = Auth::user();

        $playlists = $args['playlists'];

        $project = Project::find($args['projectId']);

        if (!count($playlists)) {
            $project->playlists()->detach();

            return [
                'message' => 'Success',
                'statusCode' => 200,
            ];
        }

        $project->playlists()->detach();
        foreach ($playlists as $playlist) {
            if ($playlist == 0) {
                $watch_later = $user->playlists()->where('name', 'Watch Later')->first();

                if ($watch_later) {
                    $project->playlists()->attach($watch_later->id);
                    continue;
                } else {
                    $watch_later = $user->playlists()->create([
                        'uuid' => Uuid::uuid4(),
                        'name' => 'Watch Later',
                        'access_level' => Playlist::ACCESS_LEVEL_PRIVATE,
                    ]);
                    $project->playlists()->attach($watch_later->id);
                    continue;
                }
            }
            $project->playlists()->attach($playlist);
        }

        return [
            'message' => 'Success',
            'statusCode' => '200',
        ];
    }

    public function detachProject($root, $args)
    {
        $playlist = Playlist::findOrFail($args['playlistId']);
        $playlist->projects()->detach($args['projectId']);

        return $playlist;
    }
}
