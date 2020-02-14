<?php

namespace App\Notifications;

use App\Channels\GraphQLChannel;
use App\Models\Star;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class StarNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $star;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Star $star)
    {
        $this->star = $star;
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
            'type_name' => 'star',
            'message' => trans(
                'notifications.new_star_notification',
                [
                    'project' => $this->star->starable->title,
                    'user' => $this->star->user->display_name,
                ]
            ),
            'star_id' => $this->star->id,
            'user_id' => $this->star->user_id
        ];
    }

    public function toGraphql($notifiable)
    {
        $notification = DatabaseNotification::findOrFail($this->id);

        Subscription::broadcast('projectStarred', $this->star);
        Subscription::broadcast('notificationCreated', $notification);
    }
}
