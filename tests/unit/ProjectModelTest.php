<?php

namespace Tests\Unit;

use App\Models\Image;
use App\Models\Video;
use App\Models\Project;
use App\Models\ProjectInput;
use \TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProjectModelTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;


    /** @test */
    public function determine_project_has_image_inputs()
    {
        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        $this->assertFalse($project->hasImages());

        // Paste a video into the project
        factory(ProjectInput::class)
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

        $this->assertFalse($project->hasImages());

        // Paste an image into the project
        factory(ProjectInput::class)
            ->create([
                'project_id'=> $project->id,
                'layer_id'  => 0,
                'position'  => 10,
                'length'    => 100,
                'type' => Image::MORPH_TYPE,
                'file_id'   => factory(Image::class)
                    ->create([
                        'imageable_id' => $project->id,
                        'imageable_type' => Project::MORPH_TYPE,
                    ])
                    ->id
            ]);

        $this->assertTrue($project->hasImages());
    }
}
