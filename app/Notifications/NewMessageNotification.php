<?php

namespace App\Notifications;

use App\Channels\GraphQLChannel;
use App\Models\Message;
use App\Models\Star;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
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
            'type_name' => 'message',
            'message' => trans(
                'notifications.new_message_notification',
                [
                    'user' => $this->message->user->display_name,
                ]
            ),
            'message_id' => $this->message->id,
            'user_id' => $this->message->user->id
        ];
    }

    public function toGraphql($notifiable)
    {
        $notification = DatabaseNotification::findOrFail($this->id);
        Subscription::broadcast('notificationCreated', $notification);
    }
}
