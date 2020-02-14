<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\URL;
use Mail;

class SendVerificationEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered $event
     * @return void
     */
    public function handle(Registered $event)
    {
        /**
 * @var User $user
*/
        $user = $event->user;

        $data['user'] = $user;
        $data['verification_link'] = URL::to(env('FRONT_URL')) . "/verify/{$user->email}/{$user->verification_code}";

        Mail::send(
            'emails.user_verification',
            $data,
            function (Message $message) use ($data) {
                $message->subject(trans('email.email_welcome_subject'));
                $message->to($data['user']->email);
            }
        );
    }
}
