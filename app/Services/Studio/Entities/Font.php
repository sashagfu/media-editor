<?php

namespace App\Services\Studio\Entities;

use Illuminate\Support\Str;
use App\Services\Studio\StudioException;

/**
 * Class Font
 *
 * @property-read string $key
 * @property-read string $title
 * @property-read string $path
 * @property-read string $absolute_path
 * @property-read array $types
 */

class Font implements FontContract
{

    /** @var string Font directory */
    protected $fontDir;

    /** @var string $key Font name (in camel case) */
    protected $key;

    /** @var array Font files */
    protected $files;

    /** @var array Types (bold, italic, underscored, etc.) */
    protected $types = [];

    /** @var string $type Font type (italic, bold etc.) */
    protected $type = 'Regular';

    /** @var int $size Size in pixels */
    protected $size = 14;

    /** @var array $color RGB[A] color */
    protected $color = [1, 1, 1, 1];

    /**
     * Font constructor.
     * @param string $key
     * @param string|null $directory
     * @throws \App\Services\Studio\StudioException
     */
    public function __construct(string $key, string $directory = null)
    {
        $this->key = $key;

        $this->fontDir = $directory ?? config('studio.fonts_dir') . $key . '/';

        $this->files = array_filter(array_diff(scandir($this->fontDir), ['..', '.']), function ($file) {
            return !! preg_match("/^{$this->key}[A-z-_]*\.ttf$/", $file);
        });

        // If font files are not found then font is invalid
        if (empty($this->files)) {
            throw new StudioException("Font [{$key}] is not found", StudioException::FONT_NOT_FOUND);
        }

        $this->types = $this->searchTypes();
    }

    public function __get($name)
    {
        /** @var string $getter Getter name */
        $getter = 'get' . ucfirst(camel_case($name));
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        }

        return null;
    }

    /**
     *  Create font instance
     * @param string $key
     * @param string $directory
     * @return FontContract
     */
    public static function make(string $key, string $directory = null): FontContract
    {
        return new self($key, $directory);
    }

    /**
     * @param float|string $size
     * @return FontContract
     * @throws StudioException
     */
    public function setSize($size): FontContract
    {
        if (!is_string($size) && !is_float($size) && !is_int($size)) {
            throw new StudioException("Font size is incorrect", StudioException::INCORRECT_FONT_SIZE);
        }

        // Size is specified in pt
        if (is_string($size) && strpos($size, 'pt')) {
            $this->size = (float)$size * .75;
        } else {
            $this->size = (float)$size;
        }

        return $this;
    }

    /**
     * @param string $type
     * @return FontContract
     * @throws StudioException
     */
    public function setType(string $type) : FontContract
    {
        if (in_array(strtolower($type), array_map('strtolower', $this->types))) {
            $this->type = $type;
        } elseif (in_array(strtolower($type), array_map('strtolower', array_keys($this->types)))) {
            $this->type = $type;
        } else {
            throw new StudioException("Font type is incorrect", StudioException::INCORRECT_FONT_TYPE);
        }
        return $this;
    }

    public function setColor(array $color): FontContract
    {
        if (count($color) < 3) {
            throw new StudioException("Font type is incorrect", StudioException::INCORRECT_FONT_COLOR);
        } else {
            $this->color = array_map('intval', $color);
        }

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getTitle(): string
    {
        return Str::title(str_replace('_', ' ', snake_case($this->key)));
    }

    /**
     * @param string $type
     * @return string
     * @throws StudioException
     */
    public function getPath(string $type = null) : string
    {
        return substr($this->getAbsolutePath($type ?? $this->type), strlen(public_path()));
    }

    /**
     * @param string|null $type
     * @return string
     * @throws StudioException
     */
    public function getAbsolutePath(string $type = null): string
    {
        /** @var string $type If type is not specified use font type (specified or default) */
        $type = $type ?? $this->type;

        if (strtolower($type) === 'regular' || $type = '') {
            // Searching "Regular" type
            if (file_exists("{$this->fontDir}{$this->key}__Regular.ttf")) {
                return "{$this->fontDir}{$this->key}__Regular.ttf";
            } elseif (file_exists("{$this->fontDir}{$this->key}__regular.ttf")) {
                return "{$this->fontDir}{$this->key}__regular.ttf";
            } elseif (file_exists("{$this->fontDir}{$this->key}.ttf")) {
                return "{$this->fontDir}{$this->key}.ttf";
            // If type is empty then return first font file
            } elseif ($type = '') {
                reset($this->files);
                return "{$this->fontDir}" . current($this->files) . ".ttf";
            }
        } else {
            if (file_exists("{$this->fontDir}{$this->key}__{$type}.ttf")) {
                return "{$this->fontDir}{$this->key}__{$type}.ttf";
            } elseif (file_exists("{$this->fontDir}{$this->key}__" . strtolower($type) . ".ttf")) {
                return "{$this->fontDir}{$this->key}__" . strtolower($type) . ".ttf";
            }
        }
        throw new StudioException("Font {$this->getTitle()} [{$type}] not found", StudioException::FONT_NOT_FOUND);
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @return array
     */
    public function getColor(): array
    {
        return $this->color;
    }

    /**
     * @return float
     */
    public function getSize(): float
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function searchTypes()
    {
        return collect($this->files)
            ->mapWithKeys(function ($file) {
                /** @var array $split_file */
                $split_file = explode('__', $file);

                if (count($split_file) > 1) {
                    /** @var string $type Type with ext */
                    $type = str_replace('.ttf', '', array_pop($split_file));

                    return [ $type => Str::title(str_replace('_', ' ', snake_case($type)))];
                }
                return [ 'Regular' => 'Regular' ];
            })
            ->unique()
            ->toArray();
    }
}
