<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */

class PerformancePageTest extends TestCase
{
    use DatabaseMigrations;

    public function test_unauthanticated_user_can_see_following_page()
    {
        $this->visit('/performances');
        $this->seePageIs('/login');
    }

    public function test_authanticated_user_can_see_performances_page()
    {
        $this->login_as_admin();
        $media = $this->createVideo(['is_performance' => true]);
        $post = $this->createPost();
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();

        $this->visit(route('performances.index'))
            ->see($post->id)
            ->see($post->title)
            ->see($post->makeContent())
        ;
    }

    public function test_if_user_can_star_post()
    {
        Session::start();
        $user = $this->login_as_admin();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();

        $this->patch(route('api.post.star'), ['post_id' => $post->id, '_token' => csrf_token()]);

        $this->seeInDatabase('stars', ['starable_id' => $post->id, 'starable_type' => 'post', 'user_id' => $user->id]);
    }

    public function test_if_user_can_unstar_post()
    {
        Session::start();
        $user = $this->login_as_admin();
        $media = $this->createVideo();
        $post = $this->createPost();
        $video_type = 'video';
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();

        $this->patch(route('api.post.star'), ['post_id' => $post->id, '_token' => csrf_token()]);

        $this->seeInDatabase('stars', ['starable_id' => $post->id, 'starable_type' => 'post', 'user_id' => $user->id]);

        $this->patch(route('api.post.star'), ['post_id' => $post->id, '_token' => csrf_token()]);

        $this->notSeeInDatabase('stars', ['starable_id' => $post->id, 'starable_type' => 'post', 'user_id' => $user->id]);
    }

    public function test_if_performance_page_posts_are_in_date_desc_order()
    {
        $admin = $this->login_as_admin();
        $post_1 = $this->createPost([
            'author_id' => $admin->id
        ]);
        $media_1 = $this->createVideo([
            'is_performance' => true
        ]);
        $video_type = 'video';
        $post_1->videos()->attach($media_1, ['media_type' => $video_type]);
        $post_1->save();
        $post_2 = $this->createPost([
            'author_id' => $admin->id
        ]);
        $media_2 = $this->createVideo([
            'is_performance' => true
        ]);
        $post_2->videos()->attach($media_2, ['media_type' => $video_type]);
        $post_2->save();

        $response = $this->get(route('performances.index'));

        $page_posts = $response->response->getOriginalContent()->p_posts;

        $ordered_posts = Post::performancePosts()
            ->skipFlagged($admin)
            ->with('videos', 'images', 'comments', 'author', 'stars')
            ->latest()
            ->paginate(10);

        $this->assertEquals($ordered_posts->first()->id, $page_posts->first()->id);
        $this->assertEquals($ordered_posts->first()->content, $page_posts->first()->content);
        $this->assertEquals($ordered_posts->last()->id, $page_posts->last()->id);
        $this->assertEquals($ordered_posts->last()->content, $page_posts->last()->content);
    }
}

// @codingStandardsIgnoreEnd