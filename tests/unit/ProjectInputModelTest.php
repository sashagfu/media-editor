<?php

namespace Tests\Unit;

use App\Models\Image;
use App\Models\Project;
use App\Models\ProjectInput;
use App\Models\Video;
use \TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProjectInputModelTest extends TestCase
{

    use DatabaseMigrations, DatabaseTransactions;

    public function test_visible_scope_on_one_layer()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        /** @var ProjectInput $above */
        $above = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 10,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        // Entry under first
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 12,
                'length'    => 4,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertEquals(
            ProjectInput::visible()->count(),
            1,
            'Expected 1 entry '. ProjectInput::visible()->count() . ' found'
        );
        $this->assertEquals(
            ProjectInput::visible()->first()->id,
            $above->id,
            'Found entry is invisible'
        );
    }

    public function test_visible_scope_on_multi_layers()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        // Entries above
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 10,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 90,
                'length'    => 40,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        // Entry under first
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 12,
                'length'    => 4,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        // Entry under second
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 2,
                'position'  => 13,
                'length'    => 2,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertEquals(
            ProjectInput::visible()->count(),
            3,
            'Expected 2 entry '. ProjectInput::visible()->count() . ' found'
        );
    }

    public function test_isSplit_method_test_with_one_splitting()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        /** @var ProjectInput $split_input */
        $split_input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 0,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertFalse($split_input->isSplit(), 'Input is not split, but determined as split');

        // Splitter
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 2,
                'position'  => 12,
                'length'    => 4,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertTrue($split_input->isSplit(), 'Input is split, but determined as not');
    }

    public function test_isSplit_method_test_with_few_splittings()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        /** @var ProjectInput $split_input */
        $split_input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 0,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertFalse($split_input->isSplit(), 'Input is not split, but determined as split');

        // Splitters
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 2,
                'position'  => 12,
                'length'    => 4,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 91,
                'length'    => 5,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertTrue($split_input->isSplit(), 'Input is split, but determined as not');
    }

    public function test_hasOverlaps_method_test_with_one_overlap_inside()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        /** @var ProjectInput $split_input */
        $split_input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 0,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertFalse($split_input->hasOverlaps());

        // Splitter
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 2,
                'position'  => 12,
                'length'    => 4,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertTrue($split_input->hasOverlaps(), 'Overlaps were not found');
    }

    public function test_hasOverlaps_method_test_overlap_at_the_beginning()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        /** @var ProjectInput $split_input */
        $split_input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 1,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 0,
                'length'    => 4,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertFalse($split_input->hasOverlaps());

        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 0,
                'length'    => 5,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertTrue($split_input->hasOverlaps(), 'Overlaps were not found');
    }

    public function test_hasOverlaps_method_test_overlap_at_the_end()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        /** @var ProjectInput $split_input */
        $split_input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 10,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 100,
                'length'    => 30,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertFalse($split_input->hasOverlaps());

        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 100,
                'length'    => 30,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertTrue($split_input->hasOverlaps(), 'Overlaps were not found');
    }

    public function test_getSplitters_method_with_one_splitter()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        /** @var ProjectInput $split_input */
        $split_input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 0,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        /** @var ProjectInput $splitter */
        $splitter = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 2,
                'position'  => 12,
                'length'    => 4,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        /** @var \Illuminate\Database\Eloquent\Collection $splitters */
        $splitters = $split_input->getSplitters();

        $this->assertEquals($splitters->count(), 1, 'Splitters are not found');
        $this->assertEquals($splitters->first()->id, $splitter->id, 'The found splitter is not correct');
    }

    public function test_getSplitters_method_with_one_the_biggest_splitter()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        /** @var ProjectInput $split_input */
        $split_input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 0,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        /** @var ProjectInput $splitter */
        $splitter = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 2,
                'position'  => 12,
                'length'    => 56,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        // Second line splitters
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 20,
                'length'    => 5,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 30,
                'length'    => 5,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        /** @var \Illuminate\Database\Eloquent\Collection $splitters */
        $splitters = $split_input->getSplitters();

        $this->assertEquals($splitters->count(), 1, 'Splitters are not found');
        $this->assertEquals($splitters->first()->id, $splitter->id, 'The found splitter is not correct');
    }

    public function test_getSplitters_method_with_multi_splitter()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        /** @var ProjectInput $split_input */
        $split_input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 0,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        /// First line
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 2,
                'position'  => 10,
                'length'    => 40,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 2,
                'position'  => 60,
                'length'    => 30,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        // Second line splitters
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 20,
                'length'    => 10,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 35,
                'length'    => 1,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 70,
                'length'    => 5,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        /** @var \Illuminate\Database\Eloquent\Collection $splitters */
        $splitters = $split_input->getSplitters();

        $this->assertEquals($splitters->count(), 2, 'Splitters are not found');
    }

    public function test_getSplitters_method_with_crossed_splitters()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        /** @var ProjectInput $split_input */
        $split_input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 0,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 2,
                'length'    => 10,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 5,
                'length'    => 10,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertEquals($split_input->getSplitters()->count(), 2);
    }

    public function test_getOverlaps_method()
    {

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $input */
        $input = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 10,
                'length'    => 100,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        // At the beginning
        $extended[] = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 0,
                'length'    => 15,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ])
            ->id;

        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 1,
                'length'    => 10,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ])
            ->id;
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 2,
                'position'  => 2,
                'length'    => 10,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ])
            ->id;

        // Inside
        $extended[] = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 15,
                'length'    => 20,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ])
            ->id;
        $extended[] = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 1,
                'position'  => 45,
                'length'    => 5,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ])
            ->id;

        // At the end
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 90,
                'length'    => 40,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ])
            ->id;
        $extended[] = factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 2,
                'position'  => 90,
                'length'    => 20,
                'type' => Video::MORPH_TYPE,
                'file_id'   => factory(Video::class)
                    ->create([
                        'videoable_id' => $project->id,
                        'videoable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ])
            ->id;


        /** @var array $actual */
        $actual = $input->getOverlaps()->pluck('id')->toArray();

        $this->assertNotEmpty($actual, 'Overlaps were not found');

        $this->assertEmpty(array_diff($extended,$actual), 'Found overlaps are incorrect');

    }

    public function test_loop_method()
    {
        // Mock storage
        \Storage::shouldReceive('disk->exists')
            ->andReturn(true);

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $image */
        $image = factory(ProjectInput::class)->create([
            'length' => 10,
            'project_id' => $project->id,
            'position' => 0,
            'converted_file' => 'some.mp4',
            'type' => Image::MORPH_TYPE,
            'file_id' => factory(Image::class)->create([
                'imageable_id' => $project->id,
                'imageable_type' => Project::MORPH_TYPE
            ])
        ]);

        $position = $image->position;
        do {
            $expected[] = [
                'Key' => $image->converted_file,
                'position' => $position,
                'TimeSpan' => [
                    'StartTime' => 0,
                    'Duration' => ProjectInput::IMAGE_VIDEO_LENGTH,
                ]
            ];

            $position += ProjectInput::IMAGE_VIDEO_LENGTH;
        } while($position < $image->length);

        /** @var \Illuminate\Support\Collection $result */
        $result = $image->loop($image->position, $image->length, 0, ProjectInput::IMAGE_VIDEO_LENGTH);

        $this->assertEquals($expected, $result->toArray());
    }

    public function test_getInputEntries_method_for_images()
    {
        // Mock storage
        \Storage::shouldReceive('disk->exists')
            ->andReturn(true);

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Models\ProjectInput $image */
        $image = factory(ProjectInput::class)->create([
            'layer_id' => 0,
            'length' => 10,
            'position' => 0,
            'converted_file' => 'some.mp4',
            'project_id' => $project->id,
            'type' => Image::MORPH_TYPE,
            'file_id' => factory(Image::class)->create([
                'imageable_id' => $project->id,
                'imageable_type' => Project::MORPH_TYPE
            ])
        ]);

        /** @var \App\Models\ProjectInput $overlap */
        $overlap = factory(ProjectInput::class)->create([
            'layer_id' =>1,
            'length' => 8,
            'position' => 1.5,
            'project_id' => $project->id,
            'type' => Video::MORPH_TYPE,
            'file_id' => factory(Video::class)->create([
                'videoable_id' => $project->id,
                'videoable_type' => Project::MORPH_TYPE
            ])
        ]);

        $expected = [];

        $position = $image->position;
        do {
            $duration = ProjectInput::IMAGE_VIDEO_LENGTH;
            if (($position + $duration) > $overlap->position) {
                $duration  = ProjectInput::IMAGE_VIDEO_LENGTH - (($position + $duration) - $overlap->position);
            }
            $expected[] = [
                'Key' => $image->converted_file,
                'position' => $position,
                'TimeSpan' => [
                    'StartTime' => 0,
                    'Duration' => $duration,
                ]
            ];

            $position += $duration;
        } while($position < $overlap->position);

        $position = $overlap->end_point;
        do {
            $duration = ProjectInput::IMAGE_VIDEO_LENGTH;
            if (($position + $duration) > $image->end_point) {
                $duration  = ProjectInput::IMAGE_VIDEO_LENGTH - (($position + $duration) - $image->end_point);
            }
            $expected[] = [
                'Key' => $image->converted_file,
                'position' => $position,
                'TimeSpan' => [
                    'StartTime' => 0,
                    'Duration' => $duration,
                ]
            ];

            $position += $duration;
        } while($position < $image->end_point);

        $this->assertEquals($expected, $image->getInputEntries()->toArray());

    }

}
