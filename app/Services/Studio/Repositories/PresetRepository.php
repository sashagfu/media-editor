<?php

namespace App\Services\Studio\Repositories;

use App\Services\Studio\StudioException;
use Aws\Laravel\AwsFacade;
use Illuminate\Support\Collection;
use App\Services\Studio\Entities\PresetContract;
use App\Services\Studio\Entities\Preset;
use Mockery\Exception;

class PresetRepository implements PresetRepositoryContract
{

    /** Cache key of saved presets */
    const ELASTIC_PRESETS_CACHE_KEY = 'cached_elastic_presets';

    /** @var \Illuminate\Support\Collection $presetConfigs */
    protected $presetConfigs;

    /** @var \Illuminate\Support\Collection|\App\Services\Studio\Entities\PresetContract[] $store */
    protected $store;

    /** @var \Illuminate\Support\Collection $store */
    protected $elasticStore;

    /** @var \Aws\ElasticTranscoder\ElasticTranscoderClient $elastic */
    protected $elastic;

    /** @var string $nextPageToken */
    protected $nextPageToken;

    public function __construct()
    {
        $this->presetConfigs = collect(config('aws.presets'));

        $this->elasticStore = collect();

        $this->store = collect();

        $this->elastic = AwsFacade::createClient('ElasticTranscoder');
    }

    /**
     * @param string $key
     * @return PresetContract
     */
    public function create(string $key) : PresetContract
    {
        /** @var array $presetData */
        $presetData = $this->presetConfigs->first(function ($presetData) use ($key) {
            return $presetData['key'] == $key;
        }, function () use ($key) {
            throw new StudioException(
                "Preset $key is not defined in config file",
                StudioException::PRESET_IS_NOT_DEFINED
            );
        });

        /** @var array $parentPreset */
        $parentPreset = $this->getElasticPreset($presetData['parent']);

        $preset['Name'] = $presetData['name'];
        $preset['Description'] = 'It is \''. $parentPreset['Name']. '\' preset copy with \'PaddingPolicy\' \'Pad\'';
        $preset['Video'] = $parentPreset['Video'];
        $preset['Audio'] = $parentPreset['Audio'];
        $preset['Thumbnails'] = $parentPreset['Thumbnails'];
        $preset['Container'] = $parentPreset['Container'];
        $preset['Video']['PaddingPolicy'] = 'Pad';
        $preset['Thumbnails']['PaddingPolicy'] = 'Pad';
        $preset['Video']['SizingPolicy'] = 'Fit';
        $preset['Thumbnails']['SizingPolicy'] = 'Fit';

        unset($preset['Video']['AspectRatio']);

        try {
            /** @var \Aws\Result $result */
            $result = $this->elastic->createPreset($preset);
            if ($result->hasKey('Preset')) {
                // Save elastic preset info
                $this->elasticStore->push($result->get('Preset'));

                /** @var \App\Services\Studio\Entities\PresetContract $preset */
                $preset = Preset::make($key, $result->get('Preset'));

                // Save preset in repository
                $this->store->push($preset);

                return $preset;
            } else {
                throw new StudioException(
                    "Cannot fetch created preset '$key'",
                    StudioException::PRESET_CANNOT_BE_FETCHED
                );
            }
        } catch (\Exception $exception) {
            throw new Exception(
                "Cannot fetch created preset '$key'",
                StudioException::PRESET_CANNOT_BE_FETCHED,
                $exception
            );
        }
    }

    /**
     * @param string $key
     * @return PresetContract
     * @throws StudioException
     */
    public function findOrCreate($key) : PresetContract
    {
        if ($preset = $this->find($key)) {
            return $preset;
        }

        return $this->create($key);
    }

    public function all() : Collection
    {
        if ($this->store->count() < $this->presetConfigs->count()) {
            return $this->presetConfigs->map(function ($presetData) {
                return $this->findOrCreate($presetData['key']);
            });
        }

        return $this->store;
    }

    /**
     *  Check if preset is already exists in store
     *
     * @param string $key
     * @return bool
     */
    protected function inStore(string $key) : bool
    {
        return !! $this->store->filter(function (PresetContract $preset) use ($key) {
            return $preset->getKey() == $key;
        })->count();
    }

    /**
     * @param string $key
     * @return PresetContract|null
     * @throws \App\Services\Studio\StudioException
     */
    public function find(string $key)
    {
        if ($this->store->isNotEmpty()) {
            /** @var \App\Services\Studio\Entities\PresetContract $preset */
            $preset = $this->store->first(function (PresetContract $preset) use ($key) {
                return $preset->getKey() == $key;
            });
            return $preset;
        }

        // If store is empty load all presets from aws
        if ($this->elasticStore->isEmpty()) {
            $this->loadPresets();
        }

        $presetName = $this->getPresetName($key);

        /** @var array $elasticPreset */
        $elasticPreset = $this->elasticStore->first(function ($elasticPreset) use ($presetName) {
            return $presetName == $elasticPreset['Name'];
        });

        if (!empty($elasticPreset)) {
            return Preset::make($key, $elasticPreset);
        }

        return null;
    }

    /**
     * @param string $key
     * @return string
     * @throws \App\Services\Studio\StudioException
     */
    protected function getPresetName(string $key)
    {
        /** @var array $presetData Information about preset from config file */
        $presetData =$this->presetConfigs->first(
            function ($presetData) use ($key) {
                return $presetData['key'] == $key;
            },
            function () use ($key) {
                throw new StudioException(
                    "Preset $key is not defined in config file",
                    StudioException::PRESET_IS_NOT_DEFINED
                );
            }
        );

        return $presetData['name'];
    }

    protected function getElasticPreset(string $name)
    {
        if ($this->elasticStore->isEmpty()) {
            $this->loadPresets();
        }

        return $this->elasticStore->first(function ($elasticPreset) use ($name) {
            return $elasticPreset['Name'] == $name;
        });
    }

    protected function loadPresets()
    {
        if (\Cache::has(self::ELASTIC_PRESETS_CACHE_KEY)) {
            $this->elasticStore = \Cache::get(self::ELASTIC_PRESETS_CACHE_KEY);
            return $this;
        }
        /** @var \Aws\ResultInterface $result $result */
        $result = $this->elastic->listPresets(['Ascending' => 'false']);

        if ($result->hasKey('Presets')) {
            $this->elasticStore = $this->store->merge(collect($result->get('Presets')));
        }

        if ($result->hasKey('NextPageToken') && !is_null($result->get('NextPageToken'))) {
            $this->nextPageToken = $result->get('NextPageToken');
        } else {
            $this->nextPageToken = null;
        }

        do {
            /** @var \Aws\ResultInterface $result $result */
            $result = $this->elastic->listPresets(['PageToken' => $this->nextPageToken, 'Ascending' => 'false']);

            if ($result->hasKey('Presets')) {
                $this->elasticStore = $this->elasticStore->merge(collect($result->get('Presets')));
            }

            if ($result->hasKey('NextPageToken') && !is_null($result->get('NextPageToken'))) {
                $this->nextPageToken = $result->get('NextPageToken');
            } else {
                $this->nextPageToken = null;
            }
        } while ($this->nextPageToken);

        \Cache::forever(self::ELASTIC_PRESETS_CACHE_KEY, $this->elasticStore);

        return $this;
    }
}
