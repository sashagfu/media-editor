<?php

use App\Models\Like;
use App\Helpers\DBHelper;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class LikeModelTest extends TestCase
{
    use DatabaseMigrations;

    public function test_if_like_can_be_created_properly()
    {
        $like = $this->createLike();

        // Existence check
        $this->assertCount(1, Like::all());

        // Exact array check
        $this->assertEquals($like->toArray(), Like::first()->toArray());
    }

    public function test_if_like_can_be_deleted_by_id()
    {
        $like = $this->createLike();

        // Delete should return true
        $this->assertTrue(Like::find($like->id)->delete());

        // No results are expected
        $this->assertEmpty(Like::all());
    }

    public function test_post_to_like_relations()
    {
        $user = $this->createUser();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();

        $this->assertEquals($media->id, $post->videos->first()->id);

        $like = $this->createLike([
            'user_id' => $user->id,
            'likeable_id' => $post->id,
            'likeable_type' => 'post'
        ]);

        $this->assertEquals($like->id, $post->likes->first()->id);
    }

    public function test_comment_to_like_relations()
    {
        $user = $this->createUser();
        $comment = $this->createComment([
            'author_id' => $user->id
        ]);

        $like = $this->createLike([
            'user_id' => $user->id,
            'likeable_id' => $comment->id,
            'likeable_type' => 'comment'
        ]);

        $this->assertEquals($like->likeable_id, $comment->likes()->first()->pivot->likeable_id);
    }

    public function test_protect_number_of_tested_fields()
    {
        $table = with(new Like)->getTable();
        $columns = \Schema::getColumnListing($table);

        $this->assertEquals([
            'id',
            'user_id',
            'likeable_id',
            'likeable_type',
            'created_at',
            'updated_at',
        ], $columns);
    }
}
