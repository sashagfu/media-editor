<?php

use App\Models\Comment;
use App\Helpers\DBHelper;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */

class CommentModelTest extends TestCase
{
    use DatabaseMigrations;

    //TESTS

    public function test_if_comment_can_be_created()
    {
        $user = $this->createUser();
        $comment = $this->createComment();
        $comment->author()->associate($user);
        $comment->save();

        $this->assertCount(1, Comment::all());

        $this->assertEquals($comment->toArray(), Comment::first()->toArray());
    }

    public function test_if_comment_has_user_relationship()
    {
        $user = $this->createUser();

        $comment = $this->createComment([
            'author_id' => $user->id
        ]);

        $this->assertCount(1, Comment::all());

        $this->assertEquals($comment->author_id, $comment->author->id);
    }

    public function test_if_user_has_comment_relationship()
    {
        $user = $this->createUser();
        $comment = $this->createComment([
            'author_id' => $user->id
        ]);
        $this->assertCount(1, $user->comments);

        $this->assertEquals($comment->id, $user->comments->first()->id);
    }

    public function test_if_comment_has_post_relationship()
    {
        $post = $this->createPost();
        $comment = $this->createComment([
            'post_id' => $post->id
        ]);
        $this->assertCount(1, $post->comments);

        $this->assertEquals($comment->id, $post->comments->first()->id);
    }

    public function test_if_comment_can_be_deleted_by_id()
    {
        $comment = $this->createComment();

        $this->assertTrue(Comment::find($comment->id)->delete());

        $this->assertEmpty(Comment::all());
    }

    public function test_if_comment_can_be_updated_with_given_fields()
    {
        $comment = $this->createComment();

        $fake_comment = $this->makeComment();

        $updated_at = $comment->updated_at;
        sleep(1);

        // Set new fields
        $comment->author_id = $fake_comment->author_id;
        $comment->text = $fake_comment->text;
        $comment->post_id = $fake_comment->post_id;
        $comment->save();

        $updated_comment = Comment::find($comment->id);

        $this->assertNotEquals($updated_at, $updated_comment->updated_at);
        $this->assertEquals($comment->author_id, $updated_comment->author_id);
        $this->assertEquals($comment->text, $updated_comment->text);
        $this->assertEquals($comment->post_id, $updated_comment->post_id);
    }

    public function test_if_comment_can_be_checked_for_like_by_user()
    {
        Session::start();
        $user = $this->login_as_admin();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();
        $comment = $this->createComment([
            'author_id' =>$user->id,
            'post_id' => $post->id,
        ]);

        $this->patch(route('api.comment.like'), ['comment_id' => $comment->id, '_token' => csrf_token()]);

        $this->seeInDatabase('likes', ['likeable_id' => $comment->id, 'likeable_type' => 'comment', 'user_id' => $user->id]);

        $this->assertEquals(true, $comment->isLikedByUser());
    }

    public function test_protect_number_of_tested_fields()
    {
        $table = with(new Comment)->getTable();
        $columns = \Schema::getColumnListing($table);

        $this->assertEquals([
            'id',
            'text',
            'author_id',
            'post_id',
            'parent_id',
            'created_at',
            'updated_at'
        ], $columns);
    }
}

// @codingStandardsIgnoreEnd