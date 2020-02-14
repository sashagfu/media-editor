<?php

namespace App\Http\GraphQL\Types;

use App\Models\Image;

class ImageType
{
    public function name(Image $image)
    {
        return $image->file_name;
    }

    public function fileName(Image $image)
    {
        return $image->file_name;
    }

    public function fileType()
    {
        return Image::MORPH_TYPE;
    }

    public function thumbPath(Image $image)
    {
        return $image->thumb_path;
    }

    public function filePath(Image $image)
    {
        return $image->file_path;
    }

    public function fileSize(Image $image)
    {
        return $image->file_size;
    }

    public function projectId(Image $image)
    {
        return $image->imageable_id;
    }
}
