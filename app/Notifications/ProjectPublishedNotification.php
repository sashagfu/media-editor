<?php

namespace App\Notifications;

use App\Channels\GraphQLChannel;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class ProjectPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $project;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', GraphQLChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $message = ($this->project->status === Project::STATUS_PUBLISHED)
            ? trans('notifications.rendering_success_text', [
                'name' => $this->project->title,
            ])
            : trans('notifications.rendering_fail_text', [
                'name' => $this->project->title,
            ]);

        return [
            'type_name' => 'project',
            'message' => $message,
            'project_id' => $this->project->id,
        ];
    }

    public function toGraphql($notifiable)
    {
        $notification = DatabaseNotification::findOrFail($this->id);

        Subscription::broadcast('projectUpdated', $this->project);
        Subscription::broadcast('notificationCreated', $notification);
    }
}
