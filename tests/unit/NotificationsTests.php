<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Jobs\FireProjectExport;
use App\Notifications\ProjectHasBeenAddedToQueue;
use App\Notifications\ProjectRenderingIsComplete;
use App\Services\Studio\Studio;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use \TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class NotificationsTests extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function send_notification_after_project_has_been_added_to_queue()
    {
        // To send this notification \App\Jobs\FireProjectExport have to be created
        // Therefore we have to create fake Queue pipeline
        Queue::fake();

        Notification::fake();

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        dispatch(new FireProjectExport($project));

        Notification::assertSentTo(
            $project->author,
            ProjectHasBeenAddedToQueue::class,
            function ($notification, $channels) use ($project) {
                $data = $notification->toArray($project->author);
                return empty(array_diff(['broadcast', 'mail'], $channels)) // Check notification channels
                    && isset($data['notification_text'])                   // and data
                    && isset($data['action'])
                    && $data['action'] == 'project_rendering_is_started';
            }
        );
    }

    /** @test */
    public function send_notification_after_project_has_been_completed()
    {
        // To send this notification \App\Jobs\FireProjectExport have to be created
        // Therefore we have to create fake Queue pipeline
        Queue::fake();

        Notification::fake();

        $studio = m::mock('overload:'.Studio::class);
        $studio->shouldIgnoreMissing();
        $studio->shouldReceive('setPresets->export')
            ->andReturn('Complete');

        $this->app->instance(Studio::class, $studio);

        /** @var \App\Models\Project $project */
        $project = factory(Project::class)->create();

        /** @var \App\Jobs\FireProjectExport $job */
        $job = new FireProjectExport($project);

        // Fire job manually
        $job->handle();

        Notification::assertSentTo(
            $project->author,
            ProjectRenderingIsComplete::class,
            function ($notification, $channels) use ($project) {
                $data = $notification->toArray($project->author);
                return empty(array_diff(['mail', 'broadcast'], $channels))
                    && isset($data['notification_text'])
                    && isset($data['action'])
                    && $data['action'] == 'project_rendering_is_finished';
            }
        );
    }
}
