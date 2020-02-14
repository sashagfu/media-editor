<?php

use App\Models\Flag;
use App\Helpers\DBHelper;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */

class FollowingPageTest extends TestCase
{
    use DatabaseMigrations;

    public function test_unauthanticated_user_can_see_following_page()
    {
        $this->visit('/following');
        $this->seePageIs('/login');
    }

    public function test_authanticated_user_can_see_following_page()
    {
        $this->login_as_admin();
        $this->visit('/following');
        $this->seePageIs('/following');
    }

    public function test_if_user_can_like_post()
    {
        Session::start();
        $user = $this->login_as_admin();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();

        $this->patch(route('api.post.like'), ['post_id' => $post->id, '_token' => csrf_token()]);

        $this->seeInDatabase('likes', ['likeable_id' => $post->id, 'likeable_type' => 'post', 'user_id' => $user->id]);
    }

    public function test_if_user_can_unlike_post()
    {
        Session::start();
        $user = $this->login_as_admin();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();

        $this->patch(route('api.post.like'), ['post_id' => $post->id, '_token' => csrf_token()]);

        $this->seeInDatabase('likes', ['likeable_id' => $post->id, 'likeable_type' => 'post', 'user_id' => $user->id]);

        $this->patch(route('api.post.like'), ['post_id' => $post->id, '_token' => csrf_token()]);

        $this->notSeeInDatabase('likes', ['likeable_id' => $post->id, 'likeable_type' => 'post', 'user_id' => $user->id]);
    }

    public function test_if_user_can_see_other_users_who_liked_post()
    {
        Session::start();
        $user = $this->login_as_admin();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();

        $this->patch(route('api.post.like'), ['post_id' => $post->id, '_token' => csrf_token()]);

        $this->post(route('api.post.likes'), ['post_id' => $post->id, '_token' => csrf_token()])
            ->seeJson([
                'id' => $user->id,
                'username' => (string) $user->username,
            ]);
    }

    public function test_if_user_can_add_comment()
    {
        Session::start();
        $user = $this->login_as_admin();
        $post = $this->createPost();

        $this->patch(route('api.comment.add'), ['post_id' => $post->id, 'comment_text' => 'random text', '_token' => csrf_token()])
            ->seeJson([
                'count' => $post->comments->count(),
                'author' => $user->username,
                'created_at' => $post->comments->first()->created_at->diffForHumans(),
                'comment' => 'random text'
            ]);

    }

    public function test_if_user_can_like_comment()
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
    }

    public function test_if_user_can_unlike_comment()
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

        $this->patch(route('api.comment.like'), ['comment_id' => $comment->id, '_token' => csrf_token()]);

        $this->notSeeInDatabase('likes', ['likeable_id' => $post->id, 'likeable_type' => 'post', 'user_id' => $user->id]);
    }

    public function test_if_user_can_see_other_users_who_liked_comment()
    {
        Session::start();
        $user = $this->login_as_admin();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();
        $comment = $this->createComment([
            'author_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->patch(route('api.comment.like'), ['comment_id' => $comment->id, '_token' => csrf_token()]);

        $this->post(route('api.comment.likes'), ['comment_id' => $comment->id, '_token' => csrf_token()])
            ->seeJson([
                'id' => $user->id,
                'username' => (string)$user->username,
            ]);
    }

    public function test_if_user_can_flag_post()
    {
        Session::start();
        $this->login_as_admin();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();
        $flag_reason = $this->createFlagReason();

        $this->patch(route('api.flag.create'), [
            'post_id' => $post->id,
            'reason_id' => $flag_reason->id,
            'reason_comment' => 'I wanna rock! Rock!',
            '_token' => csrf_token()
        ]);

        $this->assertCount($post->flaggable->count(), Flag::all());

        $this->assertEquals($post->flaggable->first()->toArray(), Flag::first()->toArray());
    }

    public function test_if_user_can_not_flag_post_again()
    {
        Session::start();
        $this->login_as_admin();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();
        $flag_reason = $this->createFlagReason();

        $this->patch(route('api.flag.create'), [
            'post_id' => $post->id,
            'reason_id' => $flag_reason->id,
            'reason_comment' => 'I wanna rock! Rock!',
            '_token' => csrf_token()
        ]);

        $this->assertCount($post->flaggable->count(), Flag::all());

        $this->assertEquals($post->flaggable->first()->toArray(), Flag::first()->toArray());

        $this->patch(route('api.flag.create'), [
            'post_id' => $post->id,
            'reason_id' => $flag_reason->id,
            'reason_comment' => 'I wanna rock! Rock!',
            '_token' => csrf_token()
        ])
            ->seeJson([
            'exist_error' => trans('flags.flag_exists'),
        ]);
    }

    public function test_if_user_can_not_see_flagged_post()
    {
        Session::start();
        $admin = $this->login_as_admin();
        $user = $this->createUser();
        $this->createUserFollowing([
           'user_id' => $admin->id,
           'follower_id' => $user->id,
        ]);
        $media = $this->createVideo();
        $post = $this->createPost([
            'author_id' => $user->id
        ]);
        $post_2 = $this->createPost([
            'author_id' => $user->id
        ]);
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();
        $flag_reason = $this->createFlagReason();

        $this->visit(route('following.index'));
        $this->see($post->title);
        $this->see($post_2->title);

        $this->patch(route('api.flag.create'), [
            'post_id' => $post->id,
            'reason_id' => $flag_reason->id,
            'reason_comment' => 'I wanna rock! Rock!',
            '_token' => csrf_token()
        ]);

        $this->assertCount($post->flaggable->count(), Flag::all());

        $this->assertEquals($post->flaggable->first()->toArray(), Flag::first()->toArray());

        $this->visit(route('following.index'));
        $this->dontSee($post->makeContent());
        $this->see($post_2->makeContent());
    }

    public function test_if_other_users_can_see_flagged_post_by_another_user()
    {
        Session::start();
        $admin = $this->login_as_admin();
        $user = $this->createUser();
        $this->createUserFollowing([
            'user_id' => $admin->id,
            'follower_id' => $user->id,
        ]);
        $media = $this->createVideo();
        $post = $this->createPost([
            'author_id' => $user->id
        ]);
        $post_2 = $this->createPost([
            'author_id' => $user->id
        ]);
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();
        $flag_reason = $this->createFlagReason();

        $this->visit(route('following.index'));
        $this->see($post->title);
        $this->see($post_2->title);

        $this->patch(route('api.flag.create'), [
            'post_id' => $post->id,
            'reason_id' => $flag_reason->id,
            'reason_comment' => 'I wanna rock! Rock!',
            '_token' => csrf_token()
        ]);

        $this->assertCount($post->flaggable->count(), Flag::all());

        $this->assertEquals($post->flaggable->first()->toArray(), Flag::first()->toArray());

        $this->visit(route('following.index'));
        $this->dontSee($post->makeContent());
        $this->see($post_2->makeContent());

        Session::clear();

        $user_2 = $this->createUser();
        $this->be($user_2);
        $this->createUserFollowing([
            'user_id' => $user_2->id,
            'follower_id' => $user->id
        ]);
        $this->visit(route('following.index'));
        $this->see($post->makeContent());
        $this->see($post_2->makeContent());
    }

    public function test_if_user_can_not_see_flagged_post_in_following_and_in_performances_pages()
    {
        Session::start();
        $admin = $this->login_as_admin();
        $user = $this->createUser();
        $this->createUserFollowing([
            'user_id' => $admin->id,
            'follower_id' => $user->id,
        ]);
        $media = $this->createVideo();
        $post = $this->createPost([
            'author_id' => $user->id
        ]);
        $post_2 = $this->createPost([
            'author_id' => $user->id
        ]);
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();
        $flag_reason = $this->createFlagReason();

        $this->visit(route('following.index'));
        $this->see($post->makeContent());
        $this->see($post_2->makeContent());

        $this->patch(route('api.flag.create'), [
            'post_id' => $post->id,
            'reason_id' => $flag_reason->id,
            'reason_comment' => 'I wanna rock! Rock!',
            '_token' => csrf_token()
        ]);

        $this->visit(route('following.index'));
        $this->dontSee($post->makeContent());
        $this->see($post_2->makeContent());

        $this->visit(route('performances.index'));
        $this->dontSee($post->makeContent());
    }

    public function test_if_following_page_posts_are_in_date_desc_order()
    {
        $admin = $this->login_as_admin();
        $this->createPost([
            'author_id' => $admin->id
        ]);
        $this->createPost([
            'author_id' => $admin->id
        ]);

        $response = $this->get(route('following.index'));

        $page_posts = $response->response->getOriginalContent()->posts;
        $ordered_posts = Post::query()->followingPosts($admin)
            ->skipFlagged($admin)
            ->latest()
            ->paginate(10);

        $this->assertEquals($page_posts->toArray(), $ordered_posts->toArray());
    }

    public function test_if_following_page_consists_of_following_users_posts()
    {
        // Let's create user and following users for him
        $admin = $this->login_as_admin();
        $user_1 = $this->createUser();
        $user_2 = $this->createUser();

        $this->createUserFollowing([
            'user_id' => $admin->id,
            'follower_id' => $user_1->id
        ]);

        // Now create posts

        $user_1_post = $this->createPost([
            'author_id' => $user_1->id
        ]);
        $user_2_post = $this->createPost([
            'author_id' => $user_2->id
        ]);

        $this->visit(route('following.index'));

        $this->see($user_1_post->makeContent());
        $this->dontSee($user_2_post->makeContent());
    }
}

// @codingStandardsIgnoreEnd