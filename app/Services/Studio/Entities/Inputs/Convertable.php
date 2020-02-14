<?php

namespace App\Services\Studio\Entities\Inputs;

use App\Models\ProjectInput;
use App\Services\Studio\Tools\Media;
use Illuminate\Support\Facades\Storage;

trait Convertable
{
    /**
     *  Image is converted into video
     *
     * @return bool
     */
    public function isConverted()
    {
        if ($this->getModel()->converted_file && $this->checkConvertedFile()) {
            return true;
        }
        return false;
    }

    public function getConvertedVideo()
    {
        return $this->getModel()->converted_file;
    }

    /**
     *  Check converted file
     * @return bool
     */
    protected function checkConvertedFile() : bool
    {
        if (Storage::disk('s3')->exists($this->getModel()->converted_file)) {
            /** @var float $actual_duration */
//            $actual_duration = Media::getVideoDuration(Storage::disk('s3')->url($this->getModel()->converted_file));
//            if ($actual_duration !== $this->getConvertedFileLength()) {
//                return false;
//            }
            return true;
        }
        return false;
    }

    abstract public function getModel() : ProjectInput;

    abstract protected function getConvertedFileLength() : float;
}
