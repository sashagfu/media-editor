<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Circle;
use App\Models\Invite;

class CircleInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Circle $circle, Invite $invite)
    {
        $this->circle = $circle;
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $json = json_encode(
            [
            'email' => $this->invite->email,
            'invite_id' => $this->invite->id,
            'type' => 'circle',
            'inviter_id' => $this->circle->creator_id,
            ]
        );
        $encoded_invite = base64_encode($json);

        return $this->view('emails.circle_invitation')->with(
            [
            'circle' => $this->circle,
            'invite' => $this->invite,
            'invite_link' => route('unregistered.invite', ['encoded_invite' => $encoded_invite]),
            ]
        );
    }
}
