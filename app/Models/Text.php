<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Text
 *
 * @property int $id
 * @property int $project_id
 * @property string $font
 * @property string $font_type
 * @property string $align
 * @property float $font_size
 * @property array $color
 * @property array $background
 * @property-read Project $project
 */
class Text extends BaseModel
{
    const MORPH_TYPE = 'text';
    /**
     * Default text color (JSON array with RGB value)
     * 255, 255, 255 => white
     *
     * @var string
     */
    const DEFAULT_COLOR = "#ffffff";

    /**
     * Default text background (JSON array with RGBA value)
     * 0,0,0,0 => transparent
     *
     * @var string
     */
    const DEFAULT_BACKGROUND = "[0,0,0,0]";

    /**
     * Text align left
     *
     * @val string
     */
    const ALIGN_LEFT = 'left';

    public $timestamps = false;

    protected $attributes = [
        'color'         => self::DEFAULT_COLOR,
        'background'    => self::DEFAULT_BACKGROUND,
        'align'         => self::ALIGN_LEFT,
    ];

    protected $casts = [
        'background' => 'array',
        'position' => 'array',
        'box_size' => 'array'
    ];

    protected $fillable = [
        'project_id',
        'slide_id',
        'content',
        'box_size',
        'font',
        'font_type',
        'position',
        'size',
        'align',
        'color',
        'background',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function inputs()
    {
        return $this->morphMany(ProjectInput::class, 'text', 'type', 'object_id');
    }

    // GETTERS
    public function getColorAttribute($color)
    {
        return $this->isHEX($color)
            ? $this->hexToRGB($color)
            : json_decode($color ?: self::DEFAULT_BACKGROUND);
    }
    public function getBackgroundAttribute($background)
    {
        return $this->isHEX($background)
            ? $this->hexToRGB($background)
            : json_decode($background ?: self::DEFAULT_BACKGROUND);
    }

    // OWN HELPERS

    /**
     *  Check if string is HEX color
     * @param string $str
     * @return bool
     */
    private function isHEX(string $str) : bool
    {
        return ctype_xdigit(substr($str, 1)) && in_array(strlen(substr($str, 1)), [6, 8]);
    }

    /**
     *  Convert HEX to RGB array
     * @param string $hex
     * @return array
     */
    private function hexToRGB(string $hex) : array
    {
        return hex2rgba($hex);
    }
}
