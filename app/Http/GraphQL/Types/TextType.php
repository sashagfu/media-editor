<?php

namespace App\Http\GraphQL\Types;

use App\Models\Text;

class TextType
{
    public function text(Text $text)
    {
        return $text->content;
    }

    public function fontFamily(Text $text)
    {
        return $text->font;
    }

    public function fontStyle(Text $text)
    {
        if ($text->font_type === 'Regular') {
            return 'normal';
        }
        return $text->font_type;
    }

    public function fontSize(Text $text)
    {
        return $text->size;
    }

    public function textAlign(Text $text)
    {
        return $text->align;
    }

    public function color(Text $text)
    {
        return $text->getOriginal('color');
    }

    public function size(Text $text)
    {
        return $text->box_size;
    }
}
