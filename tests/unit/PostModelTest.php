<?php

use App\Models\Post;
use App\Models\Video;
use App\Helpers\DBHelper;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */

class PostModelTest extends TestCase
{
    use DatabaseMigrations;

    public function test_if_post_can_be_created_properly()
    {
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = DBHelper::getMapByModel(Video::class);
        $post->videos()->attach($media, ['media_type' => $video_type]);

        $post->save();

        // Dup/Existence check
        $this->assertCount(1, Post::all());

        // Exact check
         $this->assertEquals($post->title, Post::first()->title);
         $this->assertEquals($post->description, Post::first()->description);
         $this->assertEquals($post->media_id, Post::first()->media_id);
         $this->assertEquals($post->id, Post::first()->id);
         $this->assertEquals($post->created_at, Post::first()->created_at);
         $this->assertEquals($post->updated_at, Post::first()->updated_at);
    }

    public function test_if_post_can_be_deleted_by_id()
    {
        $post = $this->createPost();

        // Delete should return true
        $this->assertTrue(Post::find($post->id)->delete());

        // No results are expected
        $this->assertEmpty(Post::all());
    }

    public function test_if_post_can_be_updated_with_given_fields()
    {
        // Create a post in DB
        $post = $this->createPost();

        // Create a template
        $fake_post = $this->makePost();

        // Let's sleep for a while to allow updated_at be different
        $updated_at = $post->updated_at;
        sleep(1);

        // Set new fields
        $post->content = 'new_content';
        $post->save();

        $fresh = Post::find($post->id);

        $this->assertNotEquals($updated_at, $fresh->updated_at);
        $this->assertEquals($post->content, $fresh->content);
    }

    public function test_post_to_video_relations()
    {
        $video = $this->createVideo();
        $post = $this->createPost();
        $video_type = DBHelper::getMapByModel(Video::class);
        $post->videos()->attach($video, ['media_type' => $video_type]);
        $post->save();

        $this->assertEquals($video->id, $post->videos->first()->id);

        $this->assertEquals(2, Video::count());
        $this->assertEquals(1, Post::count());
    }


    public function test_post_to_user_relations()
    {
        $user = $this->createUser();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = DBHelper::getMapByModel(Video::class);
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->author()->associate($user);
        $post->save();

        $this->assertEquals($post->id, $user->posts->first()->id);
        $this->assertEquals($post->author_id, $user->id);
        $this->assertEquals($post->title, $user->posts->first()->title);
    }
    public function test_if_post_has_comment_relationship()
    {
        $post = $this->createPost();
        $comment = $this->createComment([
            'post_id' => $post->id
        ]);
        $this->assertCount(1, $post->comments);

        $this->assertEquals($comment->post->first()->id, $post->id);
        $this->assertEquals($comment->post->first()->title, $post->title);
        $this->assertEquals($comment->post->first()->description, $post->description);
    }

    public function test_if_post_can_be_checked_for_like_by_user()
    {
        Session::start();
        $user = $this->login_as_admin();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = DBHelper::getMapByModel(Video::class);
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();

        $this->patch(route('api.post.like'), ['post_id' => $post->id, '_token' => csrf_token()]);

        $this->seeInDatabase('likes', ['likeable_id' => $post->id, 'likeable_type' => 'post', 'user_id' => $user->id]);

        $this->assertEquals(true, $post->isLikedByUser());
    }

    public function test_if_post_can_be_checked_if_it_is_performance()
    {
        $media = $this->createVideo([
            'is_performance' => true
        ]);
        $post = $this->createPost();
        $video_type = DBHelper::getMapByModel(Video::class);
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();

        $this->assertEquals(true, $post->isPerformance());
    }

    public function test_protect_number_of_tested_fields()
    {
        $table = with(new Post)->getTable();
        $columns = \Schema::getColumnListing($table);

        $this->assertEquals([
            'id',
            'content',
            'author_id',
            'created_at',
            'updated_at',
        ], $columns);
    }
}

// @codingStandardsIgnoreEnd