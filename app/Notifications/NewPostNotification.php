<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewPostNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $post;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Post $post, User $user)
    {
        $this->post = $post;
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

        $post_url = route('post.single', ['post_id' => $this->post->slug]);
        $notification_text = trans(
            'notifications.follower_post_create',
            [
            'name' => $this->user->username,
            ]
        );
        $action_text = trans('notifications.view_post');
        $subject = trans('notifications.follower_post_subject');

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
                'notifications.follower_post_create',
                [
                'name' => $user_name,
                ]
            ),
            'notification_url' => route('post.single', ['slug' => $post->slug]),
            'notifications_count' => $notifiable->unreadNotifications()->count(),
        ];
    }
}
