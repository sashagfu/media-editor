<?php

namespace App\Services\Sox\Classes;

interface InputInterface
{
    /**
     *  Static constructor
     * @param string $file
     * @return static
     */
    public static function make(string $file);

    /**
     *  Set volume factor
     * @param null|float $factor
     * @return InputInterface
     */
    public function volume($factor = null);

    /**
     *  Set cut points
     *
     * @param float $start
     * @param float $duration
     * @return InputInterface
     * @throws \InvalidArgumentException
     */
    public function cut(float $start, float $duration = 0.0);

    /**
     *  Set number of channels
     *
     * @param int $value
     * @return InputInterface
     */
    public function channels(int $value) : InputInterface;

    /**
     *  Set sample rate
     *
     * @param string $value
     * @return InputInterface
     */
    public function rate(string $value) : InputInterface;

    /**
     *  Set pipe mode
     * @param bool $enabled
     * @return InputInterface
     */
    public function pipe($enabled = true);

    /**
     *  Input string
     *
     * @return string
     */
    public function toString();

    /**
     *  Input string
     *
     * @return string
     */
    public function getInputString();
}
