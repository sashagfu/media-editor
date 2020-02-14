<?php

namespace App\Http\GraphQL\Types;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Star;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Asset;
use App\Models\Message;
use Illuminate\Notifications\DatabaseNotification;

class NotificationType
{
    public function notifyMessage(DatabaseNotification $notification)
    {
        return $notification->data['message'] ?? null;
    }

    public function readAt(DatabaseNotification $notification)
    {
        return $notification->read_at;
    }

    public function createdAt(DatabaseNotification $notification)
    {
        return $notification->created_at;
    }

    public function typeName($data)
    {
        return $data['type_name'] ?? null;
    }

    public function project($data)
    {
        $project_id = $data['project_id'] ?? null;

        return ($project_id) ? Project::withTrashed()->where('id', $project_id)->first() : null;
    }

    public function donation($data)
    {
        $transaction_id = $data['transaction_id'] ?? null;

        return ($transaction_id) ? Transaction::find($transaction_id) : null;
    }

    public function star($data)
    {
        $star_id = $data['star_id'] ?? null;

        return ($star_id) ? Star::find($star_id) : null;
    }

    public function user($data)
    {
        $user_id = $data['user_id'] ?? null;

        return ($user_id) ? User::find($user_id) : null;
    }

    public function comment($data)
    {
        $comment_id = $data['comment_id'] ?? null;

        return ($comment_id) ? Comment::find($comment_id) : null;
    }

    public function follower($data)
    {
        $follower_id = $data['follower_id'] ?? null;

        return ($follower_id) ? User::find($follower_id) : null;
    }

    public function asset($data)
    {
        $asset_id = $data['asset_id'] ?? null;

        return ($asset_id) ? Asset::find($asset_id) : null;
    }

    public function message($data)
    {
        $message_id = $data['message_id'] ?? null;

        return ($message_id) ? Message::find($message_id) : null;
    }
}
