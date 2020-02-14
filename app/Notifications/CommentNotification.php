<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Project $project, User $user)
    {
        $this->project = $project;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $post_url = route('post.single', ['slug' => $this->project->id]);
        $notification_text = trans(
            'notifications.post_notification_comment',
            [
            'name' => $this->user->username,
            ]
        );
        $action_text = trans('notifications.view_post');
        $subject = $notification_text;

        return (new MailMessage)
            ->subject($subject)
            ->line($notification_text)
            ->action($action_text, $post_url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $notifier = $this->user;
        $post = $this->post;
        $user_name = $notifier->username;

        return [
            'id' => $this->id,
            'notifier_avatar' => $notifier->avatar,
            'notification_text' => trans(
                'notifications.post_notification_comment',
                [
                'name' => $user_name,
                ]
            ),
            'notification_url' => route('post.single', ['slug' => $post->slug]),
            'notifications_count' => $notifiable->unreadNotifications()->count(),
        ];
    }
}
