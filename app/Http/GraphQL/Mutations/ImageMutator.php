<?php

namespace App\Http\GraphQL\Mutations;

use App\Helpers\AuthHelper;
use App\Models\Image;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Storage;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ImageMutator
{
    public function delete($root, $args)
    {
        $image_id = $args['id'];
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
