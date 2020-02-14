<?php

use Illuminate\Database\Seeder;

class TestProjectSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run()
    {

        \App\Models\Project::whereRaw('1')->delete();
        \App\Models\ProjectInput::whereRaw('1')->delete();

        /** @var \App\Models\Project $project */
        $project = factory(\App\Models\Project::class)->create();

        /** @var \App\Models\Video $video */
        $video = factory(\App\Models\Video::class)
            ->create([
                'videoable_id' => $project->id,
                'videoable_type' => \App\Models\Project::MORPH_TYPE
            ]);

        factory(\App\Models\ProjectInput::class)
            ->create([
                'project_id' => $project->id,
                'layer_id' => 0,
                'position' => 2,
                'length' => 11,
                'object_id' => $video->id,
                'file_type' => \App\Models\Video::MORPH_TYPE
            ]);
        factory(\App\Models\ProjectInput::class)
            ->create([
                'project_id' => $project->id,
                'layer_id' => 1,
                'position' => 3,
                'length' => 5,
                'object_id' => $video->id,
                'file_type' => \App\Models\Video::MORPH_TYPE
            ]);
        factory(\App\Models\ProjectInput::class)
            ->create([
                'project_id' => $project->id,
                'layer_id' => 1,
                'position' => 10,
                'length' => 1,
                'object_id' => $video->id,
                'type' => \App\Models\Video::MORPH_TYPE
            ]);
        factory(\App\Models\ProjectInput::class)
            ->create([
                'project_id' => $project->id,
                'layer_id' => 2,
                'position' => 4,
                'length' => 1,
                'object_id' => $video->id,
                'type' => \App\Models\Video::MORPH_TYPE
            ]);
        factory(\App\Models\ProjectInput::class)
            ->create([
                'project_id' => $project->id,
                'layer_id' => 2,
                'position' => 6,
                'length' => 1,
                'object_id' => $video->id,
                'ype' => \App\Models\Video::MORPH_TYPE
            ]);
        factory(\App\Models\ProjectInput::class)
            ->create([
                'project_id' => $project->id,
                'layer_id' => 2,
                'position' => 9,
                'length' => 3,
                'object_id' => $video->id,
                'type' => \App\Models\Video::MORPH_TYPE
            ]);

        $this->command->info("Command 'App\Models\Project::find($project->id)->draw()' draw project graphically");
    }
}
