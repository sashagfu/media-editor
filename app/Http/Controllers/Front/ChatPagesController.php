<?php

namespace App\Http\Controllers\Front;

use App\Models\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use App\Models\Thread;
use App\Helpers\AuthHelper;
use Illuminate\Http\Request;

class ChatPagesController extends Controller
{
    const USERS_COUNT_PER_PAGE = 10;

    /**
     * Show index page and load vue component
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('front.chat.index');
    }

    /**
     * Page to create new chat thread
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $user_id = AuthHelper::myId();
        $users = User::where('id', '!=', $user_id)->paginate(self::USERS_COUNT_PER_PAGE);

        return view('front.chat.create', compact('users'));
    }

    /**
     * Stores a new message thread
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
            'message'    => 'required',
            'recipients' => 'required',
            ]
        );

        $message = $request->message;
        $recipients = $request->recipients;

        $user_id = AuthHelper::myId();
        if (count($recipients) == 1) {
            $recipient_id = $recipients[0]['id'];
            $thread = Thread::between([$user_id, $recipient_id])->first();
            if (!$thread) {
                // Create thread if needed
                $thread = Thread::create(
                    [
                    'creator_id' => $user_id,
                    ]
                );
            }
        } else {
            $thread = Thread::create(
                [
                'creator_id' => $user_id,
                ]
            );
        }

        // Create message
        Message::create(
            [
                'thread_id' => $thread->id,
                'user_id'   => $user_id,
                'body'      => $message,
            ]
        );

        // Create sender
        $participant = Participant::firstOrCreate(
            [
                'thread_id' => $thread->id,
                'user_id'   => $user_id,
            ]
        );

        $participant->last_read = new Carbon;
        $participant->save();
        // Add recipients
        foreach ($recipients as $recipient) {
            $thread->addParticipant($recipient['id']);
        }

        return route('chat.index');
    }
}
