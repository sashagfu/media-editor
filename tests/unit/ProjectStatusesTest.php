<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\ProjectProcess;
use Carbon\Carbon;
use \TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProjectStatusesTest extends TestCase
{

    use DatabaseTransactions, DatabaseMigrations;

    /** @test */
    public function projects_statuses_with()
    {
        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        // Check default status
        $this->assertEquals(Project::DEFAULT_STATUS, $project->status);

        // Check status when exists process
        /** @var \App\Models\ProjectProcess $process */
        $process = factory(ProjectProcess::class)->create([
            'project_id' => $project->id,
            'created_at' => Carbon::now()->addDay(-1),
            'status'     => ProjectProcess::STATUS_PROCESSING,
        ]);

        $this->assertEquals($process->status_name, $project->status);

        // Check status when exists few processes
        factory(ProjectProcess::class)->create([
            'project_id' => $project->id,
            'created_at' => Carbon::now()->addDay(-2)
        ]);

        /** @var \App\Models\ProjectProcess $process */
        $process = factory(ProjectProcess::class)->create([
            'project_id' => $project->id,
            'created_at' => Carbon::now(),
            'status'     => ProjectProcess::STATUS_ERROR,
        ]);

        $this->assertEquals($process->status_name, $project->status);
    }
}
