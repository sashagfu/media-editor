<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectRenderingIsComplete extends Notification implements ShouldQueue
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(trans('notifications.rendering_finished_subject'))
                    ->line(trans('notifications.rendering_finished_text', [
                        'name' => $this->project->title,
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
            'action' => 'project_rendering_is_finished',
            'notification_text' => trans('notifications.rendering_finished_text', [
                'name' => $this->project->title,
            ]),
            'notifications_count' => $notifiable->unreadNotifications()->count(),
        ];
    }
}
