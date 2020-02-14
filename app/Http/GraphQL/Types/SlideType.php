<?php

namespace App\Http\GraphQL\Types;

use App\Models\Slide;

class SlideType
{
    public function fileType(Slide $slide)
    {
        return Slide::MORPH_TYPE;
    }

    public function texts(Slide $slide)
    {
        return $slide->texts;
    }
}
