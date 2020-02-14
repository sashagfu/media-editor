<?php

namespace App\Observers;

use App\Models\ProjectInput;
use Illuminate\Support\Facades\Storage;

class ProjectInputObserver
{

    public function deleting(ProjectInput $input)
    {
        // Delete converted file
        if ($input->isImage() && $input->isConverted()) {
            Storage::disk('s3')->delete($input->converted_file);
        }
    }
}
