<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DBHelper;
use Carbon\Carbon;
use App\Models\Image;
use App\Notifications\NewPostNotification;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use App\Models\Post;
use App\Models\Video;
use App\Models\Circle;
use App\Models\User;
use Session;
use Mail;
use App\Mail\PostShare;
use App\Models\Thread;
use App\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use App\Events\ChatMessageSent;

class PostController extends Controller
{
    public function addPostToUsersFeed(Request $request)
    {
        $video_id = $request->video_id;
        $image_type = DBHelper::getMapByModel(Image::class);
        $video_type = DBHelper::getMapByModel(Video::class);
        $content = $request->post_content;
        $media = $request->media;
        $user_id = AuthHelper::myId();
        $user = AuthHelper::me();
        $user_type = DBHelper::getMapByModel(User::class);
        $followers = $user->followers;

        $rules = [
            'post_content' => 'required_without_all:media,video_id',
            'video_id'     => 'required_without_all:post_content,media|has_video',
            'media.*'      => 'mimetypes:video/mp4,image/*'
        ];

        $messages = [
            'post_content.required_without_all' => 'Please provide text or media',
            'video_id.required_without'         => 'Please include video, if no media and text provided',
            'media.*.mimetypes'                 => 'Please include only images or videos'
        ];

        $this->validate($request, $rules, $messages);

        $post = new Post();
        $post->content = $content;
        $post->author_id = $user_id;
        $post->save();
        $post = $user->feed()->save($post, [
            'feed_type' => $user_type,
        ]);

        if ($media) {
            foreach ($media as $file) {
                if (strpos($file->getMimeType(), 'image') !== false) {
                    $uploaded_image = Image::newPostImage($file, $post);
                    $post->images()->attach($uploaded_image, ['media_type' => $image_type]);
                } else {
                    $video = Video::uploadVideoToPost($file, $post);
                    $post->videos()->attach($video, ['media_type' => $video_type]);
                }
            }
        }

        if ($video_id) {
            $video = Video::find($video_id);
            $post->videos()->attach($video, ['media_type' => $video_type]);
        }
        $user->feed()->attach($post, [
            'feed_type' => $user_type,
        ]);

        foreach ($followers as $follower) {
            $follower->notify(new NewPostNotification($post, $user));
        }

        return $post->load('author');
    }

    public function addPostToCircleFeed(Request $request)
    {
        $user_id = AuthHelper::myId();
        $video_id = $request->video_id;
        $image_type = DBHelper::getMapByModel(Image::class);
        $video_type = DBHelper::getMapByModel(Video::class);
        $content = $request->post_content;
        $media = $request->media;
        $circle_slug = $request->circle_slug;
        $circle = Circle::whereSlug($circle_slug)->firstOrFail();
        $circle_type = DBHelper::getMapByModel(Circle::class);

        $rules = [
            'post_content' => 'required_without_all:media,video_id',
            'video_id'     => 'required_without_all:post_content,media|has_video',
            'media.*'      => 'mimetypes:video/mp4,image/*'
        ];

        $messages = [
            'post_content.required_without_all' => 'Please provide text or media',
            'video_id.required_without'         => 'Please include video, if no media and text provided',
            'media.*.mimetypes'                 => 'Please include only images or videos'
        ];

        $this->validate($request, $rules, $messages);

        $post = new Post();
        $post->content = $content;
        $post->author_id = $user_id;
        $post->save();

        if ($media) {
            foreach ($media as $file) {
                if (strpos($file->getMimeType(), 'image') !== false) {
                    $uploaded_image = Image::newPostImage($file, $post);
                    $post->images()->attach($uploaded_image, ['media_type' => $image_type]);
                } else {
                    $video = Video::uploadVideoToPost($file, $post);
                    $post->videos()->attach($video, ['media_type' => $video_type]);
                }
            }
        }

        if ($video_id) {
            $video = Video::find($video_id);
            $post->videos()->attach($video, ['media_type' => $video_type]);
        }

        $circle->feed()->attach(
            $post,
            [
            'feed_type' => $circle_type,
            ]
        );

        return $post->load('author');
    }

    public function sharePostEmail(Request $request)
    {
        $post_id = $request->post_id;
        $post = Post::findOrFail($post_id);
        // Get links for images
        $media = $post->media;
        $links = collect();
        $media->each(
            function ($item) use ($links) {
                if (isset($item->sprite_path)) {
                    $links->push($item->file_path);
                }
                if (isset($item->file_name)) {
                    if (isset($item->imageable_id)) {
                        $links->push(
                            route(
                                'front.post_image',
                                [
                                'post_id'  => $item->imageable_id,
                                'filename' => $item->file_name,
                                ]
                            )
                        );
                    } else {
                        $links->push(
                            route(
                                'front.default.post_image',
                                [
                                'filename' => $item->file_name,
                                ]
                            )
                        );
                    }
                }
            }
        );

        // Send emails
        $pattern = '/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i';
        $emails = $request->emails;
        preg_match_all($pattern, $emails, $matches);
        $emails = $matches[0];
        $heading = trans(
            'posts.shared_mail_post',
            [
            'name' => AuthHelper::me()->display_name,
            ]
        );

        foreach ($emails as $email) {
            Mail::to($email)->send(new PostShare($post, $links, $heading));
        }
    }

    public function sharePostToFeed(Request $request)
    {
        $post_id = $request->post_id;
        $user = AuthHelper::me();
        $user_type = DBHelper::getMapByModel(User::class);

        $user->feed()->attach(
            $post_id,
            [
            'feed_type' => $user_type,
            ]
        );
    }

    public function findUsersToShare(Request $request)
    {
        $user_id = AuthHelper::myId();
        if ($request->has('q')) {
            $term = $request->get('q');

            return User::search($term)->get()->except($user_id);
        }
        return [];
    }

    public function sharePostToChat(Request $request)
    {
        $this->validate(
            $request,
            [
            'users' => 'required',
            'post'  => 'required',
            ]
        );

        $users = $request->users;
        $post_id = $request->post['id'];
        $post = Post::find($post_id);
        $cur_user = AuthHelper::me();

        if ($post->media->first()->file_path) {
            $link = $post->media->first()->file_path;
        } elseif ($post->media->first()->album_id) {
            $link = route('front.post_image', ['post_id' => $post->id, 'filename' => $post->media->first()->file_name]);
        } else {
            $link = route('front.default.post_image', ['filename' => $post->media->first()->file_name]);
        }

        $message_content = view('front.chat.post-preview')->with(
            [
            'header'    => trans('posts.user_has_shared', ['user' => $cur_user->display_name]),
            'image'     => $link,
            'content'   => str_limit($post->parsedContent, 85),
            'post_link' => route('post.single', ['slug' => $post->slug]),
            ]
        )->render();

        foreach ($users as $user) {
            $thread = Thread::between([$cur_user->id, $user['id']])->first();
            if (!$thread) {
                // Create thread if needed
                $thread = Thread::create(
                    [
                    'creator_id' => $cur_user->id,
                    ]
                );
            }
            // Create message
            $message = Message::create(
                [
                    'thread_id' => $thread->id,
                    'user_id'   => $cur_user->id,
                    'body'      => $message_content,
                ]
            );

            // Create sender
            $participant = Participant::firstOrCreate(
                [
                    'thread_id' => $thread->id,
                    'user_id'   => $cur_user->id,
                ]
            );
            $participant->last_read = new Carbon;
            $participant->save();

            // Add recipients
            $thread->addParticipant($user['id']);
            event(new ChatMessageSent($message, User::find($user['id']), $thread->latestMessage));
        }
    }

    public function deletePost(Request $request)
    {
        $this->validate(
            $request,
            [
            'post_id'  => 'required',
            ]
        );
        $user = AuthHelper::me();

        $post_id = $request->post_id;

        $post = Post::findOrFail($post_id);

        if ($post->author->id === $user->id) {
            $post->delete();

            return [
                'is_success' => true
            ];
        }
        return \Response::json(trans('common.error_reload'), 422);
    }
}
