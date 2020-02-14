<?php

namespace App\Http\GraphQL\Types;

use App\Models\Message;
use App\Models\Project;

class MessageType
{
    public function threadId(Message $message)
    {
        return $message->thread_id;
    }

    public function userId(Message $message)
    {
        return $message->user_id;
    }

    public function createdAt(Message $message)
    {
        return $message->created_at;
    }

    public function updatedAt(Message $message)
    {
        return $message->updated_at;
    }

    public function shareData(Message $message)
    {
        return $message->share_data;
    }

    public function project(Message $message)
    {
        if ($message->share_data['shareType'] == Project::MORPH_TYPE) {
            return Project::findOrFail($message->share_data['shareId']);
        }
        return null;
    }
}
