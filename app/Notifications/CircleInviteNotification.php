<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Circle;
use App\Models\User;
use App\Models\Invite;

class CircleInviteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $circle;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Circle $circle, Invite $invite)
    {
        $this->circle = $circle;
        $this->user = $user;
        $this->invite = $invite;
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
        $notification_text = trans(
            'notifications.circle_invite_notification',
            [
            'name' => $this->user->username,
            'circle_title' => $this->circle->title
            ]
        );
        $subject = $notification_text;

        return (new MailMessage)
            ->subject($subject)
            ->line($notification_text);
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
        $circle = $this->circle;
        $user_name = $notifier->username;
        $circle_title = $circle->title;

        return [
            'action' => 'circle_invite',
            'invite_id' => $this->invite->id,
            'id' => $this->id,
            'notifier_avatar' => $notifier->avatar,
            'notification_text' => trans(
                'notifications.circle_invite_notification',
                [
                'name' => $user_name,
                'circle_title' => $circle_title
                ]
            ),
            'notification_url' => route('circle.single', ['slug' => $this->circle->slug]),
            'notifications_count' => $notifiable->unreadNotifications()->count(),
        ];
    }
}
