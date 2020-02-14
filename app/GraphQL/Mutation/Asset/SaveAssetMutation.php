<?php

namespace App\GraphQL\Mutation\Asset;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Jobs\FireProjectExport;
use App\Models\Asset;
use App\Models\ProjectProcess;
use App\Models\Tag;
use App\Models\User;
use App\Models\Project;
use App\Notifications\NewPostNotification;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Illuminate\Validation\Rule;

class SaveAssetMutation extends Mutation
{
    protected $attributes = [
        'name' => 'saveAsset',
    ];

    public function type()
    {
        return GraphQL::type('Asset');
    }

    public function args()
    {
        return [
            'projectId' => ['name' => 'projectId', 'type' => Type::int()],
            'assetType' => ['name' => 'assetType', 'type' => Type::string()],
        ];
    }

    public function rules()
    {
        return [
            'projectId' => [
                'required',
            ],
            'assetType' => [
                'required',
                Rule::in([Asset::VIDEO_TYPE, Asset::AUDIO_TYPE, Asset::FULL_TYPE]),
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $asset_type = $args['assetType'];

        $project = Project::findOrFail($args['projectId']);

        $asset = $project->assets()
                         ->where('type', $asset_type)
                         ->where('version', $project->version)
                         ->first();

        $user = AuthHelper::me();

        $user->savedAssets()->attach($asset->id);

        return $asset;
    }
}
