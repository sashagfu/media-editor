<?php

namespace App\GraphQL\Mutation\Image;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Audio;
use App\Models\Image;
use App\Models\Video;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Illuminate\Support\Facades\Storage;

class DeleteImageFromProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteImageFromProject',
    ];

    public function type()
    {
        return GraphQL::type('Image');
    }

    public function args()
    {
        return [
            'imageId' => ['name' => 'imageId', 'type' => Type::int()],
            'projectId' => ['name' => 'projectId', 'type' => Type::int()],
        ];
    }

    public function rules()
    {
        return [
            'imageId' => [
                'required',
            ],
            'projectId' => [
                'required',
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $image_id = $args['imageId'];
        $project_id = $args['projectId'];
        $base_dir = 'users/' . AuthHelper::myId() . '/projects/' . $project_id . '/uploads';

        $image = Image::findOrFail($image_id);

        $image_dir = $base_dir . '/images/';
        $file_info = pathinfo($image->file_path);
        $file_name = $file_info['filename'];
        $ext = '.' . $file_info['extension'];

        $file_path = $image_dir . $file_name . $ext;

        $thumb_path = $image_dir . 'thumbs/' . $file_name . '_thumb' . $ext;

        $image->delete();

        Storage::disk('s3')->delete($file_path);
        Storage::disk('s3')->delete($thumb_path);

        return $image;
    }
}
