<?php

namespace App\Http\GraphQL\Types;

use App\Models\Asset;

class AssetType
{
    public function projectId(Asset $asset)
    {
        return $asset->project_id;
    }

    public function filePath(Asset $asset)
    {
        return $asset->file_path;
    }
}
