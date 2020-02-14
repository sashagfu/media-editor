<?php

namespace App\Notifications;

use App\Channels\GraphQLChannel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class NewFollowerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    protected $follower;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, User $follower)
    {
        $this->user = $user;
        $this->follower = $follower;
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
        return [
            'type_name' => 'user',
            'message' => trans(
                'notifications.new_follower_notification',
                [
                    'user' => $this->follower->display_name
                ]
            ),
            'follower_id' => $this->follower->id
        ];
    }

    public function toGraphql($notifiable)
    {
        $notification = DatabaseNotification::findOrFail($this->id);
        $this->follower->new_following = $this->user;
        Subscription::broadcast('followerCreated', $this->follower);
        Subscription::broadcast('notificationCreated', $notification);
    }
}
