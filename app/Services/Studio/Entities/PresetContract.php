<?php
namespace App\Services\Studio\Entities;

interface PresetContract
{

    /**
     *  Get preset instance and create preset on AWS if not exists
     *
     * @param string $key
     * @param array $elasticData
     * @return PresetContract
     */
    public static function make(string $key, array $elasticData) : PresetContract;

    /**
     *  Get preset key
     *
     * @return string
     */
    public function getKey() : string;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     *  Get preset ID
     *
     * @return string
     */
    public function getId() : string;
}
