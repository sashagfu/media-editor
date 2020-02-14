<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Post;

class PostShare extends Mailable
{
    use Queueable, SerializesModels;

    protected $post;
    protected $links;
    protected $heading;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($post, $links, $heading)
    {
        $this->post = $post;
        $this->links = $links;
        $this->heading = $heading;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.post_share')
            ->with(
                [
                'visit_text' => trans('posts.visit_post'),
                'post_url'   => route('post.single', ['slug' => $this->post->slug]),
                'post'       => $this->post,
                'links'      => $this->links,
                'heading'    => $this->heading,
                ]
            );
    }
}
