<?php

use App\Models\Post;
use App\Models\Video;
use App\Models\Image;
use App\Models\User;
use App\Helpers\DBHelper;
use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    protected $faker;

    public function __construct(Faker\Generator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $max_posts = 300;
        $max_video = 1;
        $max_images = 7;
        $video_type = DBHelper::getMapByModel(Video::Class);
        $image_type = DBHelper::getMapByModel(Image::Class);

        factory(Post::class, $max_posts)->create()->each(function ($post) use ($max_video, $video_type, $max_images, $image_type) {
            Video::inRandomOrder()->limit($max_video)->get()->each(function ($video) use ($post, $video_type) {
                $post->videos()->attach($video, ['media_type' => $video_type]);
            });

            $images_count = rand(1, $max_images);
            Image::inRandomOrder()->limit($images_count)->get()->each(function ($image) use ($post, $image_type) {
                $post->images()->attach($image, ['media_type' => $image_type]);
            });
            $user = User::find($post->author_id);
            $type = DBHelper::getMapByModel(User::class);
            $user->feed()->attach($post, [
                'feed_type' => $type,
            ]);
        });
    }
}
