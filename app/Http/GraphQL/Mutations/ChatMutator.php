<?php

namespace App\Http\GraphQL\Mutations;

use App\Models\Message;
use App\Models\Project;
use App\Models\ProjectShare;
use App\Models\Thread;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class ChatMutator
{
    public function createThread($root, array $args)
    {
        $creator = Auth::user();

        $another_user = User::findOrFail($args['targetId']);

        $existing_thread = Thread::between([$creator->id, $another_user->id])
                                 ->has('participants', '<=', 2)
                                 ->first();

        if ($existing_thread) {
            $participant = $existing_thread->participants()
                                           ->where('user_id', $creator->id)
                                           ->first();
            $participant->is_active = true;
            $participant->save();

            return $existing_thread;
        }

        $thread = Thread::create(
            [
                'creator_id' => $creator->id,
            ]
        );
        $participant = new Participant();
        $participant->thread_id = $thread->id;
        $participant->user_id = $creator->id;
        $participant->is_active = true;
        $participant->last_read = new Carbon;
        $participant->save();

        if ($another_user->id) {
            $thread->addParticipant($another_user->id);
        }

        Subscription::broadcast('threadUpdated', $thread);

        return $thread;
    }

    public function createGroupThread($root, $args)
    {
        $ids = $args['ids'];
        $creator = Auth::user();

        if (!count($ids)) {
            throw new \Exception(trans('chat.invalid_members_count'));
        }

        $thread = new Thread();
        $thread->creator_id = $creator->id;
        $thread->save();

        $participant = new Participant();
        $participant->thread_id = $thread->id;
        $participant->user_id = $creator->id;
        $participant->is_active = true;
        $participant->last_read = new Carbon;
        $participant->save();

        foreach ($ids as $id) {
            $thread->addParticipant($id);
        }

        Subscription::broadcast('threadUpdated', $thread);

        return $thread;
    }

    public function updateThread($root, $args)
    {
        $user = Auth::user();
        $thread_data = $args['thread'];

        $this->authorize($user, $thread_data['id']);

        $thread = Thread::findOrFail($thread_data['id']);

        if ($thread_data['name'] == $thread->name) {
            return $thread;
        }

        $thread->name = $thread_data['name'];
        $thread->save();

        Subscription::broadcast('threadUpdated', $thread);

        return $thread;
    }

    public function createMessage($root, $args)
    {
        $message_data = $args['message'];
        try {
            $thread = Thread::findOrFail($message_data['threadId']);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
        }

        $user = Auth::user();
        $thread->activateAllParticipants();

        $this->authorize($user, $thread->id);

        if (isset($message_data['shareData']['shareId'])) {
            if ($message_data['shareData']['shareType'] === Project::MORPH_TYPE) {
                $project_id = $message_data['shareData']['shareId'];
                $thread->users
                       ->map(function ($user) use ($project_id) {
                           ProjectShare::create([
                               'user_id' => $user->id,
                               'project_id' => $project_id,
                           ]);
                       });
            }
        }
        // Create Message
        $message = Message::create(
            [
                'thread_id' => $thread->id,
                'user_id'   => $user->id,
                'body'      => $this->prepareMessage($message_data['body']),
                'share_data' => $message_data['shareData'] ?? null,
            ]
        );

        // Send notifications
        $participants = $thread->participants;

        foreach ($participants as $participant) {
            // if this user is not author of message and participant not opened this chat
            if ($participant->user_id != $user->id && !$participant->is_active) {
                $participant->user->notify(new NewMessageNotification($message));
            }

            // Graphql broadcast
            Subscription::broadcast('messageCreated', $message);

            // Show thread for participant if thread is hidden
            $participant->hidden = false;
            $participant->save();
            Subscription::broadcast('threadUpdated', $thread->fresh());
        }

        return $message;
    }

    public function updateMessage($root, $args)
    {
        $user = Auth::user();
        $message_data = $args['message'];
        $message = Message::findOrFail($message_data['id']);

        if ($user->id != $message->user_id) {
            throw new \Exception(trans('chat.wrong_access_rights'), 422);
        }

        if ($this->prepareMessage($message_data['body']) == $message->body) {
            return $message;
        }

        $message->body = $this->prepareMessage($message_data['body']);
        $message->save();

        Subscription::broadcast('messageUpdated', $message);

        return $message;
    }

    public function markThreadRead($root, $args)
    {
        $thread = Thread::findOrFail($args['threadId']);
        $user = Auth::user();
        $thread->markAsRead($user->id);

        // Add additional info for subscription resolver
        $thread->reader = Auth::user();

        Subscription::broadcast('threadRead', $thread->fresh());
        Subscription::broadcast('threadUpdated', $thread->fresh());

        return $thread;
    }

    public function deleteMessage($root, $args)
    {
        $user = Auth::user();
        $message = Message::findOrFail($args['id']);

        if ($user->id != $message->user_id) {
            throw new \Exception(trans('chat.wrong_access_rights'), 422);
        }

        $message->delete();

        Subscription::broadcast('messageDeleted', $message);

        return $message;
    }

    public function createParticipants($root, $args)
    {
        $user = Auth::user();
        $ids = $args['ids'];

        $this->authorize($user, $args['threadId']);

        if (!count($ids)) {
            throw new \Exception(trans('chat.invalid_members_count'));
        }

        $thread = Thread::findOrFail($args['threadId']);

        $participants = collect();

        foreach ($ids as $id) {
            $participant = User::findOrFail($id);
            $thread->addParticipant($id);

            // Set additional data for subscriptions filter
            $participant->new_thread = $thread;

            $participants->push($participant);
        }

        Subscription::broadcast('threadUpdated', $thread->fresh());

        return $thread->fresh();
    }

    public function deleteParticipants($root, $args)
    {
        $user = Auth::user();
        $ids = $args['ids'];

        $this->authorize($user, $args['threadId']);

        if (!count($ids)) {
            throw new \Exception(trans('chat.invalid_members_count'));
        }

        $thread = Thread::findOrFail($args['threadId']);

        $participants = collect();

        foreach ($ids as $id) {
            $participant = User::findOrFail($id);
            $thread->removeParticipant($id);

            // Set additional data for subscriptions filter
            $participant->thread = $thread;

            $participants->push($participant);
        }

        Subscription::broadcast('threadUpdated', $thread->fresh());

        return $thread->fresh();
    }

    public function hideThread($root, $args)
    {
        $thread = Thread::findOrFail($args['id']);

        $participant = $thread->participants->where('user_id', Auth::user()->id)->first();

        // hide chat for this participant
        $participant->hidden = true;
        $participant->update();

        return $thread;
    }

    public function openThread($root, $args)
    {
        $thread = Thread::findOrFail($args['id']);

        $participant = $thread->participants()
                              ->where('user_id', AUth::user()->id)
                              ->first();
        $participant->is_active = true;
        $participant->save();

        return $thread;
    }

    public function closeThread($root, $args)
    {
        $thread = Thread::findOrFail($args['id']);

        $participant = $thread->participants()
                              ->where('user_id', AUth::user()->id)
                              ->first();
        $participant->is_active = false;
        $participant->save();

        return $thread;
    }

    private function prepareMessage($message)
    {
        $message_content = preg_replace(
            '(https?:\/\/[^\/]+([^\s]+[^,.?!:;])?)',
            '<a href="$0">$0</a>',
            $message
        );

        // Remove scripts from message
        $message_content = preg_replace(
            '#<script(.*?)>(.*?)</script>#is',
            '',
            $message_content
        );

        return $message_content;
    }

    private function authorize(User $user, $threadId)
    {
        if (!$user->threads->where('id', $threadId)->first()) {
            throw new \Exception(trans('chat.wrong_access_rights'), 422);
        }
    }
}
