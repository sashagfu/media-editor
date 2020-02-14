<?php

namespace App\Notifications;

use App\Channels\GraphQLChannel;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class FilesUploadedNotification extends Notification implements ShouldQueue
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
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', GraphQLChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        $message = trans('notifications.upload_files_success_text');

        return [
            'type_name'  => 'files',
            'message'    => $message,
            'project_id' => $this->project->id,
        ];
    }

    public function toGraphql($notifiable)
    {
        $notification = DatabaseNotification::findOrFail($this->id);

//        Subscription::broadcast('projectUpdated', $this->project);
        Subscription::broadcast('notificationCreated', $notification);
    }
}
