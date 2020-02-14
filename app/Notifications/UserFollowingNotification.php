<?php

namespace App\Notifications;

use User;
use App\Models\UserFollowing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserFollowingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $follower;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->follower = $user;
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
        $notifier_id = $this->follower->id;
        $notifier = User::find($notifier_id);

        $url = route('front.another_profile', ['username' => $notifier->username]);
        $notification_text = trans(
            'notifications.following_notification',
            [
            'name' => $notifier->username,
            ]
        );
        $action_text = trans('notifications.view_user');
        $subject = $notification_text;

        return (new MailMessage)
            ->subject($subject)
            ->line($notification_text)
            ->action($action_text, $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $notifier_id = $this->follower->id;
        $notifier = User::find($notifier_id);
        $notifier_user_name = $notifier->username;

        return [
            'id' => $this->id,
            'notifier_avatar' => $notifier->avatar,
            'notification_text' => trans(
                'notifications.following_notification',
                [
                'name' => $notifier_user_name,
                ]
            ),
            'notification_url' => route('front.another_profile', ['username' => $notifier_user_name]),
            'notifications_count' => $notifiable->unreadNotifications()->count(),
        ];
    }
}
