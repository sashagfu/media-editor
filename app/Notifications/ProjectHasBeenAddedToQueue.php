<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectHasBeenAddedToQueue extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var \App\Models\Project */
    protected $project;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Project $project
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('notifications.start_rendering_subject'))
            ->line(trans('notifications.start_rendering_message', [
                'name' => $this->project->title
            ]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->id,
            'action' => 'project_rendering_is_started',
            'notification_text' => trans('notifications.start_rendering_message', [
                'name' => $this->project->title
            ]),
            'notifications_count' => $notifiable->unreadNotifications()->count(),
        ];
    }
}
