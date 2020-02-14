<?php

namespace Tests\Unit;

use App\Models\Audio;
use App\Models\Image;
use App\Models\ProjectInput;
use App\Models\Text;
use App\Models\Video;
use App\Services\Studio\Entities\Inputs\BlankInput;
use App\Services\Studio\Entities\Inputs\InputImage;
use App\Services\Studio\Entities\Inputs\InputText;
use App\Services\Studio\Studio;
use App\Services\Studio\Tools\Media;
use Illuminate\Support\Facades\Storage;
use \TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Project;
use Mockery as m;

class StudioClassTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    public function test_getInputs_the_simplest_case()
    {

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $input1 */
        $input1 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'position'  => 0,
                'start_from'=> 0,
                'length'    => 10,
                'layer_id'  => 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);
        /** @var \App\Models\ProjectInput $input2 */
        $input2 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'position'  => 10,
                'start_from'=> 0,
                'length'    => 10,
                'layer_id'  => 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        $getInputs = $this->getMethod('getInputs');

        /** @var  $studio */
        $studio = new Studio($project);
        $actual = $getInputs->invokeArgs($studio, []);

        /** @var array $inputs Expected inputs from getInputs method */
        $expected = [
            [
                'Key' => $input1->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '10.0',
                ]
            ],[
                'Key' => $input2->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '10.0',
                ]
            ],
        ];

        $this->assertEquals($expected, $actual,'Inputs are not correct');

    }

    public function test_getInputs_with_overlap_at_the_beginning()
    {

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $input1 */
        $input1 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 0,
                'length'    => 20, // end point 20
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);
        /** @var \App\Models\ProjectInput $input2 */
        $input2 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 10,
                'length'    => 20, // end point 30
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        $getInputs = $this->getMethod('getInputs');

        /** @var  $studio */
        $studio = new Studio($project);
        $actual = $getInputs->invokeArgs($studio, []);

        /** @var array $inputs Expected inputs from getInputs method */
        $expected = [
            [
                'Key' => $input1->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '20.0',
                ]
            ],[
                'Key' => $input2->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '10.0',
                    'Duration' => '10.0',
                ]
            ],
        ];

        $this->assertEquals($actual, $expected, 'Inputs are not correct');

    }

    public function test_getInputs_with_overlap_at_the_end()
    {
        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $input1 */
        $input1 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 0,
                'length'    => 20, // end point 20
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);
        /** @var \App\Models\ProjectInput $input2 */
        $input2 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 10,
                'length'    => 20, // end point 30
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        $getInputs = $this->getMethod('getInputs');

        /** @var  $studio */
        $studio = new Studio($project);
        $actual = $getInputs->invokeArgs($studio, []);

        /** @var array $inputs Expected inputs from getInputs method */
        $expected = [
            [
                'Key' => $input1->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '10.0',
                ]
            ],[
                'Key' => $input2->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '20.0',
                ]
            ],
        ];

        $this->assertEquals($expected, $actual, 'Inputs are not correct');
    }

    public function test_getInputs_with_overlaps_at_the_beginning_and_at_the_end()
    {

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();


        /** @var \App\Models\ProjectInput $input1 */
        $input1 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 10,
                'length'    => 30, // End point 40
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        /** @var \App\Models\ProjectInput $input2 */
        $input2 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 0,
                'length'    => 20, // end point 20
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        /** @var \App\Models\ProjectInput $input3 */
        $input3 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 30,
                'length'    => 20, // end point 25
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        $getInputs = $this->getMethod('getInputs');

        /** @var  $studio */
        $studio = new Studio($project);
        $actual = $getInputs->invokeArgs($studio, []);

        /** @var array $inputs Expected inputs from getInputs method */
        $expected = [
            [
                'Key' => $input2->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '20.0',
                ]
            ],[
                'Key' => $input1->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '10.0',
                    'Duration' => '10.0',
                ]
            ],[
                'Key' => $input3->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '20.0',
                ]
            ],
        ];

        $this->assertEquals($expected, $actual, 'Inputs are not correct');

    }

    public function test_getInputs_with_overlap_in_the_middle()
    {

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $input1 */
        $input1 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 0,
                'length'    => 30, // End point 30
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        /** @var \App\Models\ProjectInput $input2 */
        $input2 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 10,
                'length'    => 10, // End point 20
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        $getInputs = $this->getMethod('getInputs');

        /** @var  $studio */
        $studio = new Studio($project);
        $actual = $getInputs->invokeArgs($studio, []);

        /** @var array $inputs Expected inputs from getInputs method */
        $expected = [
            [
                'Key' => $input1->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '10.0',
                ]
            ],[
                'Key' => $input2->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '10.0',
                ]
            ],[
                'Key' => $input1->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '20.0',
                    'Duration' => '10.0',
                ]
            ]
        ];

        $this->assertEquals($expected, $actual,'Inputs are not correct');

    }

    public function test_getInputs_with_two_overlaps_in_the_middle()
    {

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $input1 */
        $input1 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 0,
                'length'    => 70, // End point 70
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        /** @var \App\Models\ProjectInput $input2 */
        $input2 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 10,
                'length'    => 10, // End point 20
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        /** @var \App\Models\ProjectInput $input3 */
        $input3 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 30,
                'length'    => 20, // End point 50
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        $getInputs = $this->getMethod('getInputs');

        /** @var  $studio */
        $studio = new Studio($project);
        $actual = $getInputs->invokeArgs($studio, []);

        /** @var array $inputs Expected inputs from getInputs method */
        $expected = [
            [
                'Key' => $input1->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '10.0',
                ]
            ],[
                'Key' => $input2->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '10.0',
                ]
            ],[
                'Key' => $input1->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '20.0',
                    'Duration' => '10.0',
                ]
            ],[
                'Key' => $input3->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '20.0',
                ]
            ],[
                'Key' => $input1->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '50.0',
                    'Duration' => '20.0',
                ]
            ],
        ];

        $this->assertEquals($actual, $expected, 'Inputs are not correct');

    }

    public function test_getInputs_with_multiple_overlaps()
    {

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $input1 */
        $input1 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 0,
                'length'    => 20, // End point 20
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        /** @var \App\Models\ProjectInput $input2 */
        $input2 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 10,
                'length'    => 100, // End point 110
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        /** @var \App\Models\ProjectInput $input3 */
        $input3 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 40,
                'length'    => 20, // End point 60
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);


        /** @var \App\Models\ProjectInput $input4 */
        $input4 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 100,
                'length'    => 20, // End point 120
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        $getInputs = $this->getMethod('getInputs');

        /** @var  $studio */
        $studio = new Studio($project);
        $actual = $getInputs->invokeArgs($studio, []);

        /** @var array $inputs Expected inputs from getInputs method */
        $expected = [
            [
                'Key' => $input1->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '20.0',
                ]
            ],[
                'Key' => $input2->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '10.0',
                    'Duration' => '20.0',
                ]
            ],[
                'Key' => $input3->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '20.0',
                ]
            ],[
                'Key' => $input2->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '50.0',
                    'Duration' => '40.0',
                ]
            ],[
                'Key' => $input4->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => '20.0',
                ]
            ],
        ];

        $this->assertEquals($expected, $actual,'Inputs are not correct');
    }

    public function test_getInputs_method_with_empty_space()
    {
        Storage::shouldReceive('disk->exists')
            ->andReturn(true);

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $input */
        $input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 4.5,
                'length'    => 20, // End point 20
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        $getInputs = $this->getMethod('getInputs');

        $expected = [];

        $position = 0;
        $duration = $input->position;
        while($position < $input->position) {
            $length = $duration;
            if ($duration > BlankInput::BLANK_VIDEO_LENGTH) {
                $length = BlankInput::BLANK_VIDEO_LENGTH;
            }
            $expected[] = [
                'Key' =>  BlankInput::BLANK_VIDEO_NAME,
                'TimeSpan' => [
                    'StartTime' => 0,
                    'Duration' => $length
                ]];

            $position += $length;
            $duration -= $length;
        }

        $expected = array_merge(
            $expected,
            $input->getInputEntries()
                ->map(function ($input) {
                    unset($input['position']);
                    return $input;
                })
                ->toArray()
        );

        $this->assertEquals($expected, $getInputs->invoke(new Studio($project)));
    }

    public function test_getInputs_method_with_empty_space_at_the_beginning_and_in_the_middle()
    {
        Storage::shouldReceive('disk->exists')
            ->andReturn(true);

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $input */
        $input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 1.83,
                'length'    => 19.12, // End point 20
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        /** @var \App\Models\ProjectInput $input2 */
        $input2 = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 23.96,
                'length'    => 26, // End point 20
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Video::MORPH_TYPE
                    ])
                    ->id,
            ]);

        $getInputs = $this->getMethod('getInputs');

        $expected = [];
        $position = 0;
        $duration = $input->position;
        while($position < $input->position) {
            $length = $duration;
            if ($duration > BlankInput::BLANK_VIDEO_LENGTH) {
                $length = BlankInput::BLANK_VIDEO_LENGTH;
            }
            $expected[] = [
                'Key' => BlankInput::BLANK_VIDEO_NAME,
                'TimeSpan' => [
                    'StartTime' => 0,
                    'Duration' => round($length, 4),
                ]];

            $position += $length;
            $duration -= $length;
        }

        $expected = array_merge($expected,
            $input->getInputEntries()
            ->map(function ($input) {
                unset($input['position']);
                return $input;
            })
            ->toArray()
        );

        $position = $input->end_point;
        $duration = $input2->position - $input->end_point;
        while($position < $input2->position) {
            if ($duration > BlankInput::BLANK_VIDEO_LENGTH) {
                $length = BlankInput::BLANK_VIDEO_LENGTH;
            } else {
                $length = $duration;
            }
            $expected[] = [
                'Key' => BlankInput::BLANK_VIDEO_NAME,
                'TimeSpan' => [
                    'StartTime' => 0,
                    'Duration' => round($length, 4),
                ]];

            $position += $length;
            $duration -= $length;
        }

        $expected = array_merge(
            $expected,
            $input2->getInputEntries()
                ->map(function ($input) {
                    unset($input['position']);
                    return $input;
                })
                ->toArray()
        );

        $this->assertEquals($expected, $getInputs->invoke(new Studio($project)));
    }

    public function test_getInputs_method_with_audio_input()
    {
        Storage::shouldReceive('disk->exists')
            ->andReturn(true);

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $input */
        $input = factory(ProjectInput::class)->create([
            'project_id'=> $project->id,
            'layer_id'  => 0,
            'position'  => 0,
            'length'    => 10.56, // End point 10.56
            'start_from'=> 0,
            'type'      => Audio::MORPH_TYPE,
            'object_id' => factory(Audio::class)->create([
                'audioable_id' => $project->id,
                'audioable_type' => Project::MORPH_TYPE
            ])->id,
        ]);

        /** @var \App\Models\ProjectInput $input2 */
        $input2 = factory(ProjectInput::class)->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 2,
                'length'    => 4.4, // End point 6.4
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)->create([
                    'videoable_id' => $project->id,
                    'videoable_type' => Project::MORPH_TYPE
                ])->id,
            ]);

        $getInputs = $this->getMethod('getInputs');
        $studio = new Studio($project);

        $expected = [];

        $position = $input->position;
        $left = $input2->position - $input->position;
        while ($position < $input2->position) {
            $duration = $left;
            if ($duration > BlankInput::BLANK_VIDEO_LENGTH) {
                $duration = BlankInput::BLANK_VIDEO_LENGTH;
            }
            $expected[] = [
                "Key" => BlankInput::BLANK_VIDEO_NAME,
                "TimeSpan" => [
                    "StartTime" => 0.0,
                    "Duration" => $duration
                ]
            ];
            $left -= $duration;
            $position += $duration;
        }

        $expected[] = [
            "Key" => $input2->object->s3_path,
            "TimeSpan" => [
                "StartTime" => $input2->start_from,
                "Duration" => $input2->length
            ]
        ];
        $position = $input2->end_point;
        $left = $input->end_point - $input2->end_point;
        while ($position < $input->end_point) {
            $duration = $left;
            if ($duration > BlankInput::BLANK_VIDEO_LENGTH) {
                $duration = BlankInput::BLANK_VIDEO_LENGTH;
            }
            $expected[] = [
                "Key" => BlankInput::BLANK_VIDEO_NAME,
                "TimeSpan" => [
                    "StartTime" => 0.0,
                    "Duration" => $duration
                ]
            ];
            $left -= $duration;
            $position += $duration;
        }

        $this->assertEquals($expected, $getInputs->invoke($studio));
    }

    public function test_getInputs_method_with_image_input()
    {
        // Override exists method that calls in isConverted() method
        // we don't need to test conversation
        Storage::shouldReceive('disk->exists')
            ->andReturn(true);

        // Overload class Media to mock getVideoDuration method that used to check if convert file is actual
        $mock = m::mock('overload:' . Media::class);
        $mock->shouldReceive('getVideoDuration')
            ->andReturn(InputImage::IMAGE_VIDEO_LENGTH);

        /** @var \App\Models\project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $video */
        $video = factory(ProjectInput::class)->create([
            'project_id'    => $project->id,
            'layer_id'      => 1,
            'position'      => 0.0,
            'length'        => 10.0, // End point 10.0
            'start_from'    => 0.0,
            'type'          => Video::MORPH_TYPE,
            'object_id'     => factory(Video::class)->create([
                'videoable_type'    => Project::MORPH_TYPE,
                'videoable_id'      => $project->id,
            ])->id,
        ]);

        /** @var \App\Models\ProjectInput $image */
        $image = factory(ProjectInput::class)->create([
            'project_id'    => $project->id,
            'layer_id'      => 2,
            'position'      => 9.0,
            'start_from'    => 0,
            'length'        => 2.45,
            'converted_file'=> 'http:://example.com/path/to/converted/file.mp4',
            'type'          => Image::MORPH_TYPE,
            'object_id'     => factory(Image::class)->create([
                'imageable_type'    => Project::MORPH_TYPE,
                'imageable_id'      => $project->id,
            ])->id,
        ]);

        $getInputs = $this->getMethod('getInputs');

        /** @var array $actual Elastic Transcoder inputs */
        $actual = $getInputs->invoke(new Studio($project));

        /** @var array $expected */
        $expected = [
            [
                'Key'   => $video->object->s3_path,
                'TimeSpan'  => [
                    'StartTime' => '0.0',
                    'Duration'  => $image->position,
                ],
            ],
        ];

        $left = $image->length;
        while ($left > 0) {
            $duration = $left > InputImage::IMAGE_VIDEO_LENGTH ? InputImage::IMAGE_VIDEO_LENGTH : $left;
            $expected[] = [
                'Key' => $image->converted_file,
                'TimeSpan' => [
                    'StartTime' => 0.0,
                    'Duration'  => $duration,
                ],
            ];
            $left -= $duration;
        }

        $this->assertEquals($expected, $actual, "Inputs are incorrect");
    }

    public function test_getInputs_method_with_text_input()
    {
        // Override exists method that calls in isConverted() method
        // we don't need to test conversation
        Storage::shouldReceive('disk->exists')
            ->andReturn(true);

        // Overload class Media to mock getVideoDuration method that used to check if convert file is actual
        $mock = m::mock('overload:' . Media::class);
        $mock->shouldReceive('getVideoDuration')
            ->andReturn(InputText::LOOPED_VIDEO_DURATION);


        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $video */
        $video = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 0,
                'length'    => 10.56, // End point 10.56
                'start_from'=> 0,
                'type'      => Video::MORPH_TYPE,
                'object_id' => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE
                    ])
                    ->id,
            ]);

        /** @var \App\Models\ProjectInput $text */
        $text = factory(ProjectInput::class)->create([
            'project_id'=> $project->id,
            'layer_id'  => 0,
            'position'  => 10.56,
            'length'    => 4.67,
            'start_from'=> 0.0,
            'type'      => 'text',
            'object_id' => factory(Text::class)->create(['project_id' => $project->id]),
            'converted_file'=> 'http://example.com/path/to/converted/text/video.mp4'
        ]);

        $getInputs = $this->getMethod('getInputs');

        /** @var array $actual */
        $actual = $getInputs->invoke(new Studio($project));

        $expected = [
            [
                'Key'   => $video->object->s3_path,
                'TimeSpan' => [
                    'StartTime' => $video->start_from,
                    'Duration'  => $video->length,
                ],
            ],
        ];

        $left = $text->length;
        while ($left > 0) {
            $duration = $left > InputText::LOOPED_VIDEO_DURATION ? InputText::LOOPED_VIDEO_DURATION : $left;
            $expected[] = [
                'Key' => $text->converted_file,
                'TimeSpan' => [
                    'StartTime' => '0.0',
                    'Duration' => $duration,
                ],
            ];

            $left -= $duration;
        }

        $this->assertEquals($expected, $actual);

    }

    public function test_getInputs_method_with_text_input_image_and_empty_space()
    {
        // Override exists method that calls in isConverted() method
        // we don't need to test conversation
        Storage::shouldReceive('disk->exists')
            ->andReturn(true);

        // Overload class Media to mock getVideoDuration method that used to check if convert file is actual
        $mock = m::mock('overload:' . Media::class);
        $mock->shouldReceive('getVideoDuration')
            ->andReturn(InputText::LOOPED_VIDEO_DURATION);


        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $image */
        $image = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 0,
                'length'    => 11.75, // End point 11.75
                'start_from'=> 0,
                'type'      => Image::MORPH_TYPE,
                'object_id' => factory(Image::class)
                    ->create([
                        'imageable_id' => $project->id,
                        'imageable_type' => Project::MORPH_TYPE
                    ])
                    ->id,
                'converted_file' => 'https://example.com/path/to/converted/image_to_video.mp4',
            ]);

        /** @var \App\Models\ProjectInput $text */
        $text = factory(ProjectInput::class)->create([
            'project_id'=> $project->id,
            'layer_id'  => 0,
            'position'  => 15.0,
            'length'    => 10.0,
            'start_from'=> 0.0,
            'type'      => 'text',
            'object_id' => factory(Text::class)->create(['project_id' => $project->id]),
            'converted_file'=> 'http://example.com/path/to/converted/text_to_video.mp4'
        ]);

        $getInputs = $this->getMethod('getInputs');

        /** @var array $actual */
        $actual = $getInputs->invoke(new Studio($project));

        $img_inputs = $this->loopInput($image->converted_file, $image->length, InputImage::IMAGE_VIDEO_LENGTH);

        $blank_inputs = $this->loopInput(
            BlankInput::BLANK_VIDEO_NAME,
            $text->position - $image->end_point,
            BlankInput::BLANK_VIDEO_LENGTH
        );

        $text_inputs = $this->loopInput($text->converted_file, $text->length, InputText::LOOPED_VIDEO_DURATION);

        $expected = array_merge($img_inputs, $blank_inputs, $text_inputs);

        $this->assertEquals($expected, $actual);

    }

    protected function getMethod($name)
    {
        /** @var \ReflectionClass $class */
        $class = new \ReflectionClass(Studio::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    private function loopInput($key, $length, $item_length) : array
    {
        $inputs = [];
        $left = $length;
        while ($left > 0) {
            $duration = $left > $item_length ? $item_length : $left;
            $inputs[] = [
                'Key' => $key,
                'TimeSpan' => [
                    'StartTime' => 0.0,
                    'Duration' => $duration,
                ],
            ];
            $left -= $duration;
        }

        return $inputs;
    }
}
