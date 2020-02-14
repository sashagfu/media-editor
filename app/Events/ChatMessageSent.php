<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user;
    public $unread_count;
    public $latest_message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $user, $latest_message)
    {
        $this->message = $message;
        $this->user = $user;
        $this->unread_count = $user->unreadMessagesCount;
        $this->latest_message = $latest_message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('ChatBroadcaster.'.$this->user->id);
    }
}
