<?php

namespace Tests\Feature;


use App\Services\Studio\Repositories\PresetRepository;
use Aws\Laravel\AwsFacade;
use \TestCase;

class PresetRepositoryTest extends TestCase
{
    /** @var \App\Services\Studio\Repositories\PresetRepositoryContract */
    protected $repository;

    /** @var array Preset IDs which should be deleted */
    protected $toDestroy = [];

    public function tearDown()
    {
        /** @var \Aws\ElasticTranscoder\ElasticTranscoderClient $elastic */
        $elastic = AwsFacade::createClient('ElasticTranscoder');

        foreach ($this->toDestroy as $preset) {
            $elastic->deletePreset(['Id' => $preset]);
        }


        parent::tearDown();
    }

    /** @test */
    public function create_preset_test()
    {

        $presetData = [
            'key' => 'test_preset',
            'name' => 'Test preset',
            'parent' => 'System preset: Generic 1080p'
        ];

        config(['aws.presets' => [$presetData]]);

        $this->repository = new PresetRepository;

        $preset = $this->repository->create($presetData['key']);

        $this->assertInstanceOf(\App\Services\Studio\Entities\PresetContract::class, $preset);

        $this->toDestroy[] = $preset->getId();
    }

    /** @test */
    public function create_and_return_preset_if_not_exists()
    {
        $presetData = [
            'key' => 'test_preset',
            'name' => 'Test preset',
            'parent' => 'System preset: Generic 1080p'
        ];

        config(['aws.presets' => [$presetData]]);

        $this->repository = new PresetRepository;

        $preset = $this->repository->findOrCreate($presetData['key']);

        $this->assertInstanceOf(\App\Services\Studio\Entities\PresetContract::class, $preset);

        $this->toDestroy[] = $preset->getId();
    }
}
