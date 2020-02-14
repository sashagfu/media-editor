<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

final class FileHelper
{

    public static function uniqueFileName($path, $ext, $prefix = '')
    {
        $path = $path . DIRECTORY_SEPARATOR;

        do {
            $filename = uniqid($prefix, true) . '.' . $ext;
        } while (file_exists($path . $filename));

        return $filename;
    }

    public static function createTmpDirectoryIfNotExists($disk)
    {
        $directory_path = config("filesystems.disks.$disk.tmp_path");
        Storage::disk($disk)->makeDirectory($directory_path);
    }
}
