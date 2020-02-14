<?php
/**
 * Created by PhpStorm.
 * User: vlas
 * Date: 26.06.18
 * Time: 10:47
 */

namespace App\GraphQL\Mutation\Post;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Image;
use App\Models\Post;
use App\Models\User;
use App\Models\Video;
use App\Notifications\NewPostNotification;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class AddPostMutation extends Mutation
{
    protected $attributes = [
        'name' => 'addPost',
    ];

    public function type()
    {
        return GraphQL::type('Post');
    }

    public function args()
    {
        return [
            'postContent' => ['name' => 'postContent', 'type' => Type::string()],
            'circleSlug'  => ['name' => 'circleSlug', 'type' => Type::string()],
            'videoId'     => ['name' => 'videoId', 'type' => Type::int()],

//            'comment' => ['name' => 'comment', 'type' => GraphQL::type('CommentInput')],
        ];
    }

    public function rules()
    {
        return [
            'postContent' => [
                'required_without_all:media,video_id',
            ],
            'videoId'      => [
                'required_without_all:post_content,media|has_video',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $video_id = $args['videoId'];
        $image_type = DBHelper::getMapByModel(Image::class);
        $video_type = DBHelper::getMapByModel(Video::class);
        $content = $args['postContent'];
//        $media = $request->media;
        $user_id = AuthHelper::myId();
        $user = AuthHelper::me();
        $user_type = DBHelper::getMapByModel(User::class);
        $followers = $user->followers;

        $post = new Post();
        $post->content = $content;
        $post->author_id = $user_id;
        $post->save();
        $post = $user->feed()->save($post, [
            'feed_type' => $user_type,
        ]);

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

    /**
     * Return an array of custom validation messages.
     *
     * @return array
     */
    public function validationErrorMessages($root, $args, $context)
    {
        return [
            'postContent.required_without_all' => 'Please provide text or media',
            'videoId.required_without_all' => 'Please include video, if no media and text provided',
        ];
    }
}
