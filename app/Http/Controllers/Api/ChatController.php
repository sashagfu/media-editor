<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Auth;
use App\Helpers\AuthHelper;
use App\Models\Thread;
use App\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Events\ChatMessageSent;

class ChatController extends Controller
{
    const THREADS_PAGINATION_NUMBER = 10;
    const MESSAGES_PAGINATION_NUMBER = 12;

    public function loadThreads()
    {
        $user_id = AuthHelper::myId();
        $threads = Thread::forUser($user_id)->latest('updated_at')->paginate(self::THREADS_PAGINATION_NUMBER);

        return $threads;
    }

    public function loadLastThread()
    {
        $user_id = AuthHelper::myId();

        $thread = Thread::forUser($user_id)->latest('updated_at')->first();

        return $thread;
    }

    public function loadThread($id)
    {
        $thread = Thread::findOrFail($id);

        return $thread;
    }

    public function sendMessage($id, Request $request)
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json('error', 422);
        }
        // Validate message
        $this->validate(
            $request,
            [
            'message' => 'required',
            ]
        );

        $user_id = AuthHelper::myId();
        $thread->activateAllParticipants();

        $message_content = preg_replace(
            '(http:\/\/[^\/]+([^\s]+[^,.?!:;])?)',
            '<a href="$0">$0</a>',
            $request->message
        );

        // Create Message
        $message = Message::create(
            [
                'thread_id' => $thread->id,
                'user_id'   => $user_id,
                'body'      => $message_content,
            ]
        );


        // Add replier as a participant
        $participant = Participant::firstOrCreate(
            [
                'thread_id' => $thread->id,
                'user_id'   => $user_id,
            ]
        );
        $participant->last_read = new Carbon;
        $participant->save();

        // Send notifications
        $participants = $thread->participants;

        foreach ($participants as $user) {
            if ($user_id != $user->user->id) {
                event(new ChatMessageSent($message, $user->user, $thread->latestMessage));
            }
        }

        // Return for ajax call
        return [
            'message' => $message,
        ];
    }

    public function loadMessages($id)
    {
        $thread = Thread::findOrFail($id);
        $messages = $thread->messages()
            ->latest('created_at')
            ->paginate(self::MESSAGES_PAGINATION_NUMBER);

        return $messages;
    }

    public function getUnreadMessagesCount()
    {
        $user = AuthHelper::me();

        return $user->unreadMessagesCount;
    }

    public function markThreadRead($id)
    {
        $thread = Thread::findOrFail($id);
        $user = AuthHelper::me();
        $thread->markAsRead($user->id);

        return $user->unreadMessagesCount;
    }

    public function startProfileChat(Request $request)
    {
        $creator_id = AuthHelper::myId();
        $another_user_id = $request->user_id;

        $existing_thread = Thread::between([$creator_id, $another_user_id])->first();

        if ($existing_thread) {
            return $existing_thread;
        } else {
            $thread = Thread::create(
                [
                'creator_id' => $creator_id,
                ]
            );
            Participant::create(
                [
                    'thread_id' => $thread->id,
                    'user_id'   => $creator_id,
                    'last_read' => new Carbon,
                ]
            );
            if ($another_user_id) {
                $thread->addParticipant($another_user_id);
            }

            return $thread;
        }
    }
}
