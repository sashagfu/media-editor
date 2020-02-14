<?php

namespace App\GraphQL\Mutation\Asset;

use App\Helpers\AuthHelper;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Illuminate\Validation\Rule;

class DeleteAssetMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteAsset',
    ];

    public function type()
    {
        return GraphQL::type('Asset');
    }

    public function args()
    {
        return [
            'assetId' => ['name' => 'assetId', 'type' => Type::int()],
        ];
    }

    public function rules()
    {
        return [
            'assetId' => [
                'required',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $user = AuthHelper::me();

        $user->savedAssets()->detach($args['assetId']);

        return null;
    }
}
