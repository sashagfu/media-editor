<?php

namespace App\Jobs;

use App\Models\Asset;
use App\Models\Project;
use App\Models\ProjectCredit;
use App\Models\ProjectProcess;
use App\Notifications\ProjectHasBeenAddedToQueue;
use App\Notifications\ProjectPublishedNotification;
use App\Notifications\ProjectRenderingIsComplete;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Studio\Studio;
use Illuminate\Support\Facades\Storage;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class FireProjectExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Project $project */
    protected $project;

    /** @var ProjectProcess $project */
    protected $process;

    /** @var int */
    protected $finish_status;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Project
     * @return void
     */
    public function __construct(Project $project, $finish_status)
    {
        $this->project = $project;
        $this->finish_status = $finish_status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);

        $this->process = $this->project->processes()->latest();

        // Update project status
        $this->process->update([ 'status' => ProjectProcess::STATUS_PROCESSING, ]);

        /** @var \App\Services\Studio\Studio $studio */
        $studio = new Studio($this->project);

        try {
            // Update project version
            $this->project->update(
                [
                    'status' => Project::STATUS_PROCESSING,
                    'progress' => 10
                ]
            );

            Subscription::broadcast('projectUpdated', $this->project);

            /** @var string $status */
            $status = $studio->setPresets(['720p'])->export();

            if ($status == "Complete") {
                // If status is not 'Error' then 'Complete'
                $this->process->update([
                    'status' => ProjectProcess::STATUS_COMPLETE,
                    'outputs' => json_encode($studio->outputs()),
                ]);
                $this->project->update(
                    [
                        'status' => $this->finish_status,
                        'progress' => 100,
                        'last_rendered_version' => $this->project->version,
                    ]
                );

                $this->project->author->notify(new ProjectPublishedNotification($this->project));
            } elseif ($status == "Error") {
                // Send notification about error
                $this->process->update(['status' => ProjectProcess::STATUS_ERROR]);
                Storage::disk('s3')
                       ->deleteDirectory($this->project->path . 'assets/' . $this->project->version);
                $this->project->author->notify(new ProjectPublishedNotification($this->project));
            }
        } catch (\Exception $e) {
            $this->project->update(
                [
                    'status' => Project::STATUS_FAILED,
                ]
            );
            Storage::disk('s3')
                   ->deleteDirectory($this->project->path . 'assets/' . $this->project->version);
            $this->project->author->notify(new ProjectPublishedNotification($this->project));
            logger($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
