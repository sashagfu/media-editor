<?php

namespace App\GraphQL\Type;

use App\Models\Asset;
use Folklore\GraphQL\Support\Type as GraphQLType;
use GraphQL;
use GraphQL\Type\Definition\Type;

class AssetType extends GraphQLType
{
    protected $attributes = [
        'name'        => 'Asset',
        'description' => 'An asset',
    ];

    public function fields()
    {

        return [
            'id' => [
                'type'        => Type::int(),
                'description' => 'The id of the asset',
            ],
            'projectId' => [
                'type'        => Type::int(),
                'description' => 'The id of project',
                'resolve' => function ($asset) {
                    return $asset->project_id;
                }
            ],
            'project' => [
                'type'        => GraphQL::type('Project'),
                'description' => 'Project related to asset',
            ],
            'type'      => [
                'type'        => Type::string(),
                'description' => 'Type of the asset', // full || video || audio
            ],
            'fileType' => [
                'type'        => Type::string(),
                'description' => 'Morph type of this model',
                'resolve'     => function () {
                    return Asset::MORPH_TYPE;
                },
            ],
            'filePath' => [
                'type'        => Type::string(),
                'description' => 'Path to the asset',
            ],
            'version'   => [
                'type'        => Type::int(),
                'description' => 'The version of the project(asset)'
            ],
            'thumbPath' => [
                'type'        => Type::string(),
                'description' => 'Path to the thumb image',
            ],
            'spritePath' => [
                'type' => Type::string(),
                'description' => 'Path to the sprite',
            ],
            'waveformData' => [
                'type'        => Type::string(),
                'description' => 'Path to the waveform',
            ],
            'enableToSave' => [
                'type' => Type::boolean(),
                'description' => 'If asset is enabled to save',
            ],
            'credits' => [
                'type' => Type::listOf(GraphQL::type('User')),
                'description' => 'The authors of used assets'
            ]
        ];
    }

    public function resolveEnableToSaveField(Asset $asset, $args)
    {
        if ($asset->type == Asset::FULL_TYPE) {
            return $asset->project->usedAssetTypesCount(Asset::VIDEO_TYPE) == 0 &&
                   $asset->project->usedAssetTypesCount(Asset::AUDIO_TYPE) == 0 &&
                   $asset->project->usedAssetTypesCount(Asset::FULL_TYPE) == 0;
        }

        if ($asset->type == Asset::VIDEO_TYPE) {
            return $asset->project->usedAssetTypesCount(Asset::VIDEO_TYPE) == 0 &&
                   $asset->project->usedAssetTypesCount(Asset::FULL_TYPE) == 0;
        }

        if ($asset->type == Asset::AUDIO_TYPE) {
            return $asset->project->usedAssetTypesCount(Asset::AUDIO_TYPE) == 0 &&
                   $asset->project->usedAssetTypesCount(Asset::FULL_TYPE) == 0;
        }

        return null;
    }

    public function resolveCreditsField(Asset $asset)
    {
        $credits = [];

        foreach ($asset->project->usedAssets as $asset) {
            $credits[] = $asset->project->author;
        }

        return $credits;
    }
}
