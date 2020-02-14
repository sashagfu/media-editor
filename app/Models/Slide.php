<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends BaseModel
{
    const MORPH_TYPE = 'slide';
    const CANVAS_SIZE = [
        'width' => 640,
        'height' => 364,
    ];
    const DEFAULT_EFFECTS = [
        'fadeIn' => [
            'duration' => null,
        ],
        'fadeOut' => [
            'duration' => null,
        ]
    ];

    protected $fillable = [
        'project_id',
        'name',
        'effects',
    ];

    protected $casts = [
        'effects' => 'json',
    ];

    protected $appends = [
        'canvasSize',
    ];

    public function texts()
    {
        return $this->hasMany(Text::class);
    }

    public function getCanvasSizeAttribute()
    {
        return self::CANVAS_SIZE;
    }
}
