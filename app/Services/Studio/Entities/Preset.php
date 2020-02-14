<?php

namespace App\Services\Studio\Entities;

use App\Services\Studio\StudioException;
use App\Services\Studio\Entities\PresetContract;
use Aws\Laravel\AwsFacade;

class Preset implements PresetContract
{

    /**
     * @var string $key Preset key (set in config/aws.php)
     */
    protected $key;

    /**
     * @var string $id Preset ID on AWS Server
     */
    protected $id;

    /**
     * @var string $name Preset name
     */
    protected $name;

    /**
     * @var string $parent Parent preset name
     */
    protected $parent;

    /**
     *  Preset constructor.
     * @param string $key
     * @param array $elasticData
     * @throws \App\Services\Studio\StudioException
     */
    public function __construct(string  $key, array $elasticData)
    {
        $this->key = $key;

        if (isset($elasticData['Name'])) {
            $this->name = $elasticData['Name'];
        } else {
            throw new StudioException(
                'Preset instance has not been created. Preset "Name" was not specified',
                StudioException::PRESET_VALIDATION_ERROR
            );
        }


        if (isset($elasticData['Id'])) {
            $this->id = $elasticData['Id'];
        } else {
            throw new StudioException(
                'Preset instance has not been created. Preset "Id" was not specified',
                StudioException::PRESET_VALIDATION_ERROR
            );
        }
    }

    /**
     *  Create real preset instance
     *
     * @param string $key
     * @param array $elasticData
     * @return \App\Services\Studio\Entities\PresetContract
     */
    public static function make(string $key, array $elasticData) : PresetContract
    {
        $self = new static($key, $elasticData);
        return $self;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
