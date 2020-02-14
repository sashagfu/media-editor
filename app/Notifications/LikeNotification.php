<?php

namespace App\Notifications;

use App\Helpers\AuthHelper;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LikeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $like;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Like $like)
    {
        $this->like = $like;
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
        $notifier_id = $this->like->user_id;
        $notifier = User::find($notifier_id);
        $post_id = $this->like->likeable_id;
        $post = Post::find($post_id);

        $post_url = route('post.single', ['slug' => $post->slug]);
        $notification_text = trans(
            'notifications.post_notification_like',
            [
            'name' => $notifier->username,
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
        $notifier_id = $this->like->user_id;
        $notifier = User::find($notifier_id);
        $post_id = $this->like->likeable_id;
        $post = Post::find($post_id);
        $user_name = $notifier->username;

        return [
            'id' => $this->id,
            'notifier_avatar' => $notifier->avatar,
            'notification_text' => trans(
                'notifications.post_notification_like',
                [
                    'name' => $user_name,
                ]
            ),
            'notification_url' => route('post.single', ['slug' => $post->slug]),
            'notifications_count' => $notifiable->unreadNotifications()->count(),
        ];
    }
}
