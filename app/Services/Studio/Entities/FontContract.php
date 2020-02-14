<?php

namespace App\Services\Studio\Entities;

/**
 * Interface FontContract
 *
 * @property-read string $key
 * @property-read string $title
 * @property-read string $path
 * @property-read string $absolute_path
 * @property-read array $types
 * @property-read float $size In pixels
 * @property-read string $type
 * @property-read array $color Array RGB[A]
 *
 */
interface FontContract
{
    /**
     *  Create instance of Font
     *
     * @param string $key
     * @return FontContract
     * @throws \App\Services\Studio\StudioException
     */
    public static function make(string $key) : FontContract;

    /**
     * @param float|string $size
     * @return FontContract
     */
    public function setSize($size) : FontContract;

    /**
     * @param string $type
     * @return FontContract
     * @throws \App\Services\Studio\StudioException
     */
    public function setType(string $type) : FontContract;

    /**
     *  Set color
     *  RGB[A] format array
     * @param array $color
     * @return FontContract
     * @throws \App\Services\Studio\StudioException
     */
    public function setColor(array $color) : FontContract;

    /**
     *  Key getter
     * @return string
     */
    public function getKey() : string;

    /**
     *  Title getter
     * @return string
     */
    public function getTitle() : string;

    /**
     *  Path getter
     * @param string $type
     * @return string
     * @throws \App\Services\Studio\StudioException
     */
    public function getPath(string $type = 'regular') : string;

    /**
     *  Absolute path getter
     *
     * @param string $type
     * @return string
     * @throws \App\Services\Studio\StudioException
     */
    public function getAbsolutePath(string $type = 'regular') : string;

    /**
     *  Font types (Bold, Italic, etc.)
     * @return array
     */
    public function getTypes() : array;
}
