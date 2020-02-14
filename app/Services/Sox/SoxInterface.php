<?php

namespace App\Services\Sox;

use App\Services\Sox\Classes\InputInterface;

interface SoxInterface
{

    /**
     *  SoxInterface constructor.
     *
     * @param string $mode Edit mode
     */
    public function __construct(string $mode = '');

    /**
     *  Set concat mode
     *
     * @return \App\Services\Sox\SoxInterface;
     */
    public static function concat();

    /**
     *  Set mix mode
     *
     * @return \App\Services\Sox\SoxInterface
     */
    public static function mix();

    /**
     *  Add input file
     *
     * @param \App\Services\Sox\Classes\InputInterface $input File path
     * @return \App\Services\Sox\SoxInterface;
     */
    public function addInput(InputInterface $input);

    /**
     *  Output the result
     *
     * @param string $new_file Path to new file
     * @param string $options Output options
     * @return \App\Services\Sox\SoxInterface;
     */
    public function saveAs(string $new_file, string $options = '');
}
