<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use App\Models\Asset;
use App\Models\Post;
use App\Models\Project;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Video;
use App\Models\Circle;
use App\Models\Invite;
use Illuminate\Notifications\DatabaseNotification;
use App\Notifications\LikeNotification;
use App\Notifications\StarNotification;
use App\Notifications\UserFollowingNotification;
use App\Models\SearchResult;
use App\Notifications\CommentNotification;
use App\Models\Thread;

$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'username'          => $faker->unique()->userName,
        'email'             => $faker->unique()->safeEmail,
        'password'          => $password ?: $password = bcrypt('secret'),
        'is_verified'       => $faker->numberBetween(0, 1),
        'verification_code' => str_random(30),
        'remember_token'    => str_random(100),
        'talent'            => $faker->words(3, true),
        'display_name'      => $faker->name,
        'quote'             => $faker->paragraph,
        'total_likes'       => $faker->numberBetween(0, 10),
        'total_stars'       => $faker->numberBetween(0, 10),
        'created_at'        => $faker->dateTimeThisMonth,
    ];
});

// Video Model
$factory->define(Video::class, function (Faker\Generator $faker) {
    $user_ids = \DB::table('users')->pluck('id')->toArray();
    $user_id = $faker->randomElement($user_ids);

    return [
        'thumbnail_path' => $faker->imageUrl(160, 120),
        'file_path'      => $faker->imageUrl(),
        'author_id'      => $user_id,
        'is_performance' => $faker->boolean(50),
        'created_at'     => $faker->dateTimeThisMonth,
        'name'           => $faker->sentence(4),
        'sprite_path'    => $faker->imageUrl(50, 1000), // TODO: Make a real sprite set if needed
        'time'           => $faker->numberBetween(0, 500),
        'frames'         => $faker->numberBetween(0, 1000),
    ];
});

// Post Model
$factory->define(Post::class, function (Faker\Generator $faker) {
    $user_ids = \DB::table('users')->pluck('id')->toArray();
    $user_id = $faker->randomElement($user_ids);

    $hashtag_count = $faker->numberBetween(0, 6);
    $hashtags = "";
    for ($i = 0; $i <= $hashtag_count; $i++) {
        $hashtags .= ' #' . $faker->word;
    }

    return [
        'slug'       => $faker->uuid,
        'content'    => $faker->paragraph(3) . $hashtags,
        'created_at' => $faker->dateTimeThisMonth,
        'author_id'  => $user_id,
    ];
});

// Comment Model
$factory->define(App\Models\Comment::class, function (Faker\Generator $faker) {
    return [
        'text'       => $faker->text(25),
        'author_id'  => User::inRandomOrder()->first()->id,
        'project_id' => Project::inRandomOrder()->first()->id,
        'parent_id'  => $faker->optional($weight = 0.3)->numberBetween(0, 100),
        'created_at' => $faker->dateTimeThisMonth,
    ];
});

// Flag Model
$factory->define(App\Models\Flag::class, function (Faker\Generator $faker) {
    $user_ids = \DB::table('users')->pluck('id')->toArray();
    $user_id = $faker->randomElement($user_ids);

    $post_ids = \DB::table('posts')->pluck('id')->toArray();
    $post_id = $faker->randomElement($post_ids);

    return [
        'description'    => $faker->paragraph(1),
        'author_id'      => $user_id,
        'flaggable_id'   => $post_id,
        'flaggable_type' => 'post',
        'is_verified'    => $faker->boolean(),
        'created_at'     => $faker->dateTimeThisMonth,
    ];
});

// Like Model
$factory->define(App\Models\Like::class, function (Faker\Generator $faker) {
    $user_ids = \DB::table('users')->pluck('id')->toArray();
    $user_id = $faker->randomElement($user_ids);

    $item_type = $faker->randomElement(['post', 'comment']);

    if ($item_type == 'post') {
        $post_ids = \DB::table('posts')->pluck('id')->toArray();
        $item_id = $faker->randomElement($post_ids);
    } else {
        $comment_ids = \DB::table('comments')->pluck('id')->toArray();
        $item_id = $faker->randomElement($comment_ids);
    }

    return [
        'user_id'       => $user_id,
        'likeable_id'   => $item_id,
        'likeable_type' => $item_type,
    ];
});

// Star Model
$factory->define(App\Models\Star::class, function (Faker\Generator $faker) {
    $user_ids = \DB::table('users')->pluck('id')->toArray();
    $user_id = $faker->randomElement($user_ids);

    $post_ids = Post::performancePosts()->get()->pluck('id')->toArray();
    $post_id = $faker->randomElement($post_ids);

    return [
        'user_id'       => $user_id,
        'starable_id'   => $post_id,
        'starable_type' => 'post',
    ];
});

// Followings
$factory->define(App\Models\UserFollowing::class, function (Faker\Generator $faker) {
    $user_ids = \DB::table('users')->pluck('id')->toArray();
    $user_id = $faker->randomElement($user_ids);
    unset($user_ids[$user_id]);
    $another_user_id = $faker->randomElement($user_ids);

    return [
        'user_id'     => $user_id,
        'follower_id' => $another_user_id,
    ];
});


// FlagReasons Model
$factory->define(App\Models\FlagReason::class, function (Faker\Generator $faker) {
    return [
        'title'   => $faker->sentence(),
        'enabled' => $faker->boolean(50),
    ];
});

// SearchResult Model
$factory->define(SearchResult::class, function (Faker\Generator $faker) {
    return [
        'search_term'  => $faker->word,
        'total_search' => 1,
    ];
});

// Notifications Model
$factory->define(DatabaseNotification::class, function (Faker\Generator $faker) {

    $notification_types = ['post', 'following', 'comment'];
    $notification_type = $faker->randomElement($notification_types);

    if ($notification_type == 'post') {
        $post_ids = \DB::table('posts')->pluck('id')->toArray();
        $post_id = $faker->randomElement($post_ids);
        $post = Post::find($post_id);
        $post_author_id = $post->author_id;

        $user_ids = \DB::table('users')->pluck('id')->toArray();
        unset($user_ids[$post_author_id]);
        $another_user_id = $faker->randomElement($user_ids);
        $another_user = User::find($another_user_id);

        if ($post->isPerformance) {
            $data = [
                'notifier_avatar'   => $another_user->avatar,
                'notification_text' => trans('notifications.post_notification_star', [
                    'name'  => $another_user->username,
                    'title' => $post->title,
                ]),
                'notification_url'  => route('post.single', ['slug' => $post->slug]),
//                'notification_icon' => $post->media->file_path,
            ];

            return [
                'id'              => $faker->uuid,
                'type'            => StarNotification::class,
                'notifiable_id'   => $post_author_id,
                'notifiable_type' => 'user',
                'data'            => $data,
            ];

        } else {
            $data = [
                'notifier_avatar'   => $another_user->avatar,
                'notification_text' => trans('notifications.post_notification_like', [
                    'name'  => $another_user->username,
                    'title' => $post->title,
                ]),
                'notification_url'  => route('post.single', ['slug' => $post->slug]),
//                'notification_icon' => $post->media->file_path,
            ];

            return [
                'id'              => $faker->uuid,
                'type'            => LikeNotification::class,
                'notifiable_id'   => $post_author_id,
                'notifiable_type' => 'user',
                'data'            => $data,
            ];
        }
    } elseif ($notification_type == 'following') {
        $user_ids = \DB::table('users')->pluck('id')->toArray();
        $user_id = $faker->randomElement($user_ids);
        unset($user_ids[$user_id]);
        $another_user_id = $faker->randomElement($user_ids);
        $another_user = User::find($another_user_id);

        $data = [
            'notifier_avatar'   => $another_user->avatar,
            'notification_text' => trans('notifications.following_notification', [
                'name' => $another_user->username,
            ]),
            'notification_url'  => route('front.another_profile', ['username' => $another_user->username]),
        ];

        return [
            'id'              => $faker->uuid,
            'type'            => UserFollowingNotification::class,
            'notifiable_id'   => $user_id,
            'notifiable_type' => 'user',
            'data'            => $data,
        ];

    } elseif ($notification_type == 'comment') {
        $post_ids = \DB::table('posts')->pluck('id')->toArray();
        $post_id = $faker->randomElement($post_ids);
        $post = Post::find($post_id);
        $post_author_id = $post->author_id;

        $user_ids = \DB::table('users')->pluck('id')->toArray();
        unset($user_ids[$post_author_id]);
        $another_user_id = $faker->randomElement($user_ids);
        $another_user = User::find($another_user_id);

        $data = [
            'notifier_avatar'   => $another_user->avatar,
            'notification_text' => trans('notifications.post_notification_comment', [
                'name'  => $another_user->username,
                'title' => $post->title,
            ]),
            'notification_url'  => route('post.single', ['slug' => $post->slug]),
//            'notification_icon' => $post->media->file_path,
        ];

        return [
            'id'              => $faker->uuid,
            'type'            => CommentNotification::class,
            'notifiable_id'   => $post_author_id,
            'notifiable_type' => 'user',
            'data'            => $data,
        ];
    } else {
        return false;
    }
});

// Circles Model
$factory->define(Circle::class, function (Faker\Generator $faker) {
    $user_ids = \DB::table('users')->pluck('id')->toArray();
    $user_id = $faker->randomElement($user_ids);
    $title = $faker->sentence();
    $types = ['secret', 'open', 'closed'];
    $type = $faker->randomElement($types);
    $post_adding_privacies = [1, 2];
    $post_adding_privacy = $faker->randomElement($post_adding_privacies);
    $members_block_privacies = [1, 2];
    $members_block_privacy = $faker->randomElement($members_block_privacies);


    return [
        'title'                 => $title,
        'slug'                  => $faker->unixTime() * $faker->unixTime(),
        'description'           => $faker->paragraph(2),
        'creator_id'            => $user_id,
        'type'                  => $type,
        'post_adding_privacy'   => $post_adding_privacy,
        'members_block_privacy' => $members_block_privacy,
    ];
});

// Invites Model
$factory->define(Invite::class, function (Faker\Generator $faker) {
    $user_ids = \DB::table('users')->pluck('id')->toArray();
    $user_id = $faker->randomElement($user_ids);
    $user_email = User::find($user_id)->email;

    $circles_ids = \DB::table('circles')->pluck('id')->toArray();
    $circle_id = $faker->randomElement($circles_ids);


    return [
        'id'         => $faker->uuid,
        'email'      => $user_email,
        'user_id'    => $user_id,
        'circle_id'  => $circle_id,
        'active'     => $faker->numberBetween(0, 1),
        'expiration' => $faker->dateTimeThisMonth,
    ];
});

// Threads table
$factory->define(Thread::class, function (Faker\Generator $faker) {
    $user_ids = \DB::table('users')->pluck('id')->toArray();
    $user_id = $faker->randomElement($user_ids);

    return [
        'creator_id' => $user_id,
    ];
});

// Messages table
$factory->define(\Cmgmyr\Messenger\Models\Message::class, function (Faker\Generator $faker) {
    $threads_ids = \DB::table('messenger_threads')->pluck('id')->toArray();
    $thread_id = $faker->randomElement($threads_ids);

    $user_ids = \DB::table('messenger_participants')->pluck('user_id')->toArray();
    $user_id = $faker->randomElement($user_ids);

    return [
        'thread_id' => $thread_id,
        'user_id'   => $user_id,
        'body'      => $faker->sentence(),
    ];
});

// Projects table
$factory->define(\App\Models\Project::class, function (Faker\Generator $faker) {
    return [
        'author_id' => function () {
            return User::inRandomOrder()->first()->id;
        },
        'title' => $faker->sentence(rand(1, 4)),
        'description' => $faker->paragraph(rand(1, 10)),
        'status' => rand(1, 4),
        'version' => 1,
        'thumb_time'=> null,
        'thumb_path' => "https://picsum.photos/id/" . rand(1, 100) . "/1280/720",
    ];
});

// Assets table
$factory->define(\App\Models\Asset::class, function (Faker\Generator $faker) {
    return [
        'project_id' => function () {
            return Project::inRandomOrder()
                          ->where('status', Project::STATUS_PUBLISHED)
                          ->first()->id;
        },
        'type' => Asset::FULL_TYPE,
        'version' => 1,
        'file_path' => 'http://vjs.zencdn.net/v/oceans.mp4',
        'time' => 46000,

    ];
});

// Project inputs table
$factory->define(\App\Models\ProjectInput::class, function (Faker\Generator $faker) {
    return [
        'project_id' => function () {
            return Project::inRandomOrder()->first()->id;
        },
        'type' => $faker->randomElement([
            \App\Models\Audio::MORPH_TYPE,
            \App\Models\Video::MORPH_TYPE,
            \App\Models\Image::MORPH_TYPE,
        ]),
        'object_id' => function ($row) {
            if ($row['type'] == \App\Models\Audio::MORPH_TYPE) {
                return factory(\App\Models\Audio::class)
                    ->create([
                        'audioable_id' => $row['project_id'],
                        'audioable_type' => \App\Models\Project::MORPH_TYPE,
                    ])
                    ->id;
            } elseif ($row['type'] == \App\Models\Video::MORPH_TYPE) {
                return factory(\App\Models\Video::class)
                    ->create([
                        'videoable_id' => $row['project_id'],
                        'videoable_type' => \App\Models\Project::MORPH_TYPE,
                    ])
                    ->id;
            } elseif ($row['type'] == \App\Models\Image::MORPH_TYPE) {
                return factory(\App\Models\Video::class)
                    ->create([
                        'imageable_id' => $row['project_id'],
                        'imageable_type' => \App\Models\Project::MORPH_TYPE,
                    ])
                    ->id;
            } elseif ($row['type'] === \App\Models\Text::MORPH_TYPE) {
                return factory(\App\Models\Text::class)->create(['project_id' => $row['project_id']]);
            }

            return null;
        },
        'converted_file' => null,
        'layer_id' => rand(0,2),
        'position' => $faker->randomFloat(),
        'start_from' => $faker->randomFloat(8, 0, 10000),
        'length' => $faker->randomFloat(),
        'transform' => [
            'size' => [ 'width' => 1, 'height' => 1 ],
            'scale' => 1,
            'position' => [ 'x' => 1, 'y' => 1 ]
        ]
    ];
});

$factory->define(\App\Models\Image::class, function (Faker\Generator $faker) {
    return [
        'file_name' => str_replace(' ', '_', $faker->sentence(rand(1,3))) .  $faker->randomElement(['mp3', 'wav', 'flac']),
        'thumb_path' => $faker->imageUrl(100, 100),
        'file_path' => $faker->imageUrl(500, 500),
        'width' => rand(100, 3000),
        'height' => rand(100, 3000),
        'file_size' => rand(100, 3000),
        'album_id' => null,
        'imageable_id' => null,
        'imageable_type' => null,
    ];
});
$factory->define(\App\Models\Audio::class, function (Faker\Generator $faker) {
    return [
        'sprite'  => null,
        'time' => rand(100, 30000) * .01 ,
        'name' => str_replace(' ', '_', $faker->sentence(rand(1,3))) . 'jpg',
        'file_path' => $faker->url,
        'audioable_id' => null,
        'audioable_type' => null,
    ];
});

$factory->define(\App\Models\Video::class, function (Faker\Generator $faker) {
    return [
        'author_id' => function () {
            return User::inRandomOrder()->first()->id;
        },
        'name' => str_replace(' ', '_', $faker->sentence(rand(1,3))) . 'mp4',
        'file_path' => function ($row) use ($faker) {
            return $faker->url . '/' . $row['name'];
        },
        'sprite_path' => $faker->imageUrl(100, 100),
        'thumbnail_path' => $faker->imageUrl(100, 100),
        'time' => $faker->randomFloat(2, 1, 2000),
        'width' => 1280,
        'height' => 720,
        'frames' => rand(2, 300),
        'waveform' => $faker->url,
        'is_performance' => true,
        'videoable_id' => null,
        'videoable_type' => null,
    ];
});

$factory->define(\App\Models\Text::class, function (Faker\Generator $faker) {
    /** @var \App\Services\Studio\Entities\FontContract $font */
    $font = (new \App\Services\Studio\Repositories\FontRepository())->all()->random();
    return [
        'project_id' => function () {
            return Project::inRandomOrder()->first()->id;
        },
        'content' => $faker->sentence,
        'font' => $font->getKey(),
        'font_type' => $faker->randomElement($font->types),
        'size' => rand(12, 24),
        'align' => 'left',
    ];
});

$factory->define(\App\Models\ProjectProcess::class, function (Faker\Generator $faker) {
    return [
        'project_id' => function () {
            return Project::inRandomOrder()->first()->id;
        },
        'job_id' => null,
        'status'    => $faker->randomElement([
            \App\Models\ProjectProcess::STATUS_WAITING,
            \App\Models\ProjectProcess::STATUS_COMPLETE,
            \App\Models\ProjectProcess::STATUS_ERROR,
            \App\Models\ProjectProcess::STATUS_CANCELED,
        ]),
        'outputs' => function ($row) use ($faker) {
            /** @var null|string $outputs Default outputs */
            $outputs = null;
            if ($row['status'] == \App\Models\ProjectProcess::STATUS_COMPLETE) {
                /** @var array $presets */
                $presets = array_map(
                    function ($presetData) {
                        return $presetData['key'];
                    },
                    config('aws.presets')
                );

                $outputs = array_combine($presets, array_fill(0, count($presets), $faker->url));
            }
            return json_encode($outputs);
        },
    ];
});

// Transactions Model
$factory->define(Transaction::class, function (Faker\Generator $faker) {
    $types = [Transaction::DONATION_TYPE];
    $type = $faker->randomElement($types);
    $payer_id = $faker->randomElement([
        User::inRandomOrder()->first()->id,
        User::inRandomOrder()->first()->id,
        1
    ]);

    $payee_id = ($payer_id == 1) ? User::inRandomOrder()->first()->id : 1;

    $statuses = [2, 3, 4, 7];
    $status = $faker->randomElement($statuses);

    return [
        'payer_id'          => $payer_id,
        'payee_id'          => $payee_id,
        'type'              => $type,
        'status'            => $status,
        'amount'            => $faker->numberBetween(10, 100),
        'created_at'        => $faker->dateTimeThisMonth,
        'updated_at'        => $faker->dateTimeThisMonth,
    ];
});
