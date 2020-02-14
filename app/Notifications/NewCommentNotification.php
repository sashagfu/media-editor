<?php

namespace App\Notifications;

use App\Channels\GraphQLChannel;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
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
        $message = ($this->comment->parent_id)
            ? trans(
                'notifications.new_reply_notification',
                [
                    'replier' => $this->comment->author->display_name,
                    'project' => $this->comment->project->title,
                ]
            )
            : trans(
                'notifications.new_comment_notification',
                [
                    'user'    => $this->comment->author->display_name,
                    'project' => $this->comment->project->title,
                ]
            );
        return [
            'type_name' => 'comment',
            'message' => $message,
            'comment_id' => $this->comment->id,
            'user_id' => $this->comment->author->id,
        ];
    }

    public function toGraphql($notifiable)
    {
        $notification = DatabaseNotification::findOrFail($this->id);

        Subscription::broadcast('commentCreated', $this->comment);
        Subscription::broadcast('notificationCreated', $notification);
    }
}
