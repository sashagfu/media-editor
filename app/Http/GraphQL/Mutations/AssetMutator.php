<?php

namespace App\Http\GraphQL\Mutations;

use App\Helpers\AuthHelper;
use App\Models\Asset;
use App\Models\Project;
use App\Notifications\ProjectClippedNotification;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AssetMutator
{
    public function save($root, $args)
    {
        $asset_type = $args['assetType'];

        $project = Project::findOrFail($args['projectId']);

        $asset = $project->assets()
                         ->where('type', $asset_type)
                         ->where('version', $project->version)
                         ->first();

        $user = AuthHelper::me();

        $user->savedAssets()->attach($asset->id);

        $project->author->notify(new ProjectClippedNotification($asset, $user));

        return $asset;
    }

    public function delete($root, $args)
    {
        $user = Auth::user();
        $asset = Asset::findOrFail($args['id']);
        $user->savedAssets()->detach($asset->id);

        return $asset;
    }
}
