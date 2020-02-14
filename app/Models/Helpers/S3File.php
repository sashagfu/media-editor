<?php

namespace App\Models\Helpers;

use Illuminate\Database\Eloquent\Model;

/**
 * Class S3File
 *
 * @property-read string $s3_path
 *
 * @package App\Models\Helpers
 */

trait S3File
{

    /**
     *  File path formatted for S3
     * @return string
     */
    public function getS3PathAttribute()
    {
        $url = parse_url($this->file_path);

        if (!isset($url['path'])) {
            return '';
        }


        if ($url['path']{0} == '/') {
            $url['path'] = substr($url['path'], 1);
        }

        return $url['path'];
    }

    public function getS3PathEffectedAttribute()
    {
        if (!$this->file_path_effected) {
            return null;
        }

        $url = parse_url($this->file_path_effected);

        if (!isset($url['path'])) {
            return '';
        }


        if ($url['path']{0} == '/') {
            $url['path'] = substr($url['path'], 1);
        }

        return $url['path'];
    }
}
