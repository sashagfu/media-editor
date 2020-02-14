<?php

namespace Tests\Browser;

use Tests\Browser\Pages\ChatPage;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\PostPage;
use App\Models\User;
use App\Models\Post;
use Tests\Browser\Pages\UsersFeed;

class PostTest extends DuskTestCase
{
    const ADMIN_ID = 1;
    const TESTER_ID = 3;
    const POST_ID = 120;

    public function testIfUnregisteredUserWillSeeSharedPostPage()
    {
        $post = Post::find(self::POST_ID + 4);
        $this->browse(function ($user) use ($post) {
            $user->visit(new PostPage)
                ->visit(route('post.single', ['slug' => $post->slug]))
                ->assertVisible('@post_container')
                ->assertMissing('@post_container_actions');
        });
    }

    public function testIfUserCanSeeSinglePostPage()
    {
        $post = Post::find(self::POST_ID);
        $this->browse(function ($admin) use ($post) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PostPage)
                ->goToSinglePostPage($post->slug);
        });
    }

    public function testIfUserCanReactToThePost()
    {
        $post = Post::find(self::POST_ID);
        $this->browse(function ($admin) use ($post) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PostPage)
                ->goToSinglePostPage($post->slug)
                ->driver->executeScript('scroll(0, 500)');
            if ($post->isPerformance) {
                $reaction_count = $post->stars->count();
                $admin->assertVisible('@star_btn')
                    ->click('@star_btn')
                    ->assertVisible('@reaction_loading')
                    ->waitFor('@star_btn.starred');
            } else {
                $reaction_count = $post->likes->count();
                $admin->assertVisible('@like_btn')
                    ->click('@like_btn')
                    ->assertVisible('@reaction_loading')
                    ->waitFor('@like_btn.liked');
            }
            if ($post->isPerformance) {
                $admin->assertSeeIn('@star_count', $reaction_count + 1);
            } else {
                $admin->assertSeeIn('@like_count', $reaction_count + 1);
            }
            $admin->assertMissing('@reaction_loading');
        });
    }

    public function testIfUserCanFlagPost()
    {
        $post = Post::find(self::POST_ID);
        $this->browse(function ($admin) use ($post) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PostPage)
                ->goToSinglePostPage($post->slug)
                ->driver->executeScript('scroll(0, 500)');
            $admin->mouseover('@post_menu_headline')
                ->waitFor('@flag_post_btn')
                ->click('@flag_post_btn')
                ->assertVisible('@flags_modal')
                ->click('@create_flag_btn')
                ->waitForText(trans('validation.required', [
                    'attribute' => 'reason comment',
                ]))
                ->type('@flags_modal_comment', 'Not relevant content')
                ->click('@create_flag_btn')
                ->waitUntilMissing('@flags_modal')
                ->assertDontSee($post->parsedContent);
        });
    }

    public function testIfUserCanCommentPost()
    {
        $post = Post::find(self::POST_ID + 1);
        $this->browse(function ($admin) use ($post) {
            $comment_text = 'New comment';
            $comments_count = $post->comments->count();
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PostPage)
                ->goToSinglePostPage($post->slug)
                ->driver->executeScript('scroll(0, 500)');
            $admin->click('@comments_btn')
                ->assertVisible('@new_comment_form')
                ->click('@add_comment_btn')
                ->waitForText(trans('validation.required', [
                    'attribute' => 'comment text',
                ]))
                ->type('@new_comment_form', $comment_text)
                ->click('@add_comment_btn')
                ->waitForText($comment_text)
                ->assertDontSee(trans('validation.required', [
                    'attribute' => 'comment text',
                ]))
                ->assertSeeIn('@comments_count', $comments_count + 1);
        });
    }

    public function testIfUserCanAddSubCommentPost()
    {
        $post = Post::find(self::POST_ID + 2);
        $this->browse(function ($admin) use ($post) {
            $comment_text = 'New comment';
            $comment_text_2 = 'New subcomment';
            $comments_count = $post->comments->count();
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PostPage)
                ->goToSinglePostPage($post->slug)
                ->driver->executeScript('scroll(0, 500)');
            $admin->click('@comments_btn')
                ->type('@new_comment_form', $comment_text)
                ->click('@add_comment_btn')
                ->waitForText($comment_text)
                ->assertDontSee(trans('validation.required', [
                    'attribute' => 'comment text',
                ]))
                ->assertSeeIn('@comments_count', $comments_count + 1);
            $admin->driver->executeScript('scroll(0, 1000)');
            $admin->click('@subcomments_open_btn:last-of-type')
                ->assertVisible('@new_subcomment_form')
                ->type('@new_subcomment_form', $comment_text_2)
                ->click('@add_comment_btn:last-of-type')
                ->waitForText($comment_text_2);
        });
    }

    public function testIfUserCanShareHisPostByEmail()
    {
        $post = Post::find(self::POST_ID + 3);
        $this->browse(function ($admin) use ($post) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PostPage)
                ->goToSinglePostPage($post->slug)
                ->driver->executeScript('scroll(0, 500)');
            $admin->click('@post_share_btn')
                ->waitFor('@email_share_btn')
                ->click('@email_share_btn')
                ->waitFor('@email_share_modal')
                ->type('@emails_input', 'not_even_email')
                ->click('@email_share_btn_send')
                ->assertDontSee('@progress_bar')
                ->type('@emails_input', 'tester@test.com')
                ->click('@email_share_btn_send')
                ->waitFor('@progress_bar')
                ->waitUntilMissing('@email_share_modal', 15);
        });
    }

    public function testIfUserCanShareHisPostByEmailToMultiple()
    {
        $post = Post::find(self::POST_ID + 3);
        $this->browse(function ($admin) use ($post) {

            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PostPage)
                ->goToSinglePostPage($post->slug)
                ->driver->executeScript('scroll(0, 500)');
            $admin->click('@post_share_btn')
                ->waitFor('@email_share_btn')
                ->click('@email_share_btn')
                ->waitFor('@email_share_modal')
                ->type('@emails_input', 'not_even_email')
                ->click('@email_share_btn_send')
                ->assertDontSee('@progress_bar')
                ->type('@emails_input', 'tester@test.com abracadabra')
                ->click('@email_share_btn_send')
                ->assertDontSee('@progress_bar')
                ->type('@emails_input', 'tester@test.com, email2@test.com')
                ->click('@email_share_btn_send')
                ->waitFor('@progress_bar')
                ->waitUntilMissing('@email_share_modal', 18);
        });
    }

    public function testIfUserCanSharePostToHisTimeLine()
    {
        $post = Post::find(self::POST_ID + 7);
        $this->browse(function ($admin) use ($post) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PostPage)
                ->goToSinglePostPage($post->slug)
                ->driver->executeScript('scroll(0, 500)');
            $admin->assertVisible('@post_container')
                ->assertVisible('@post_share_btn')
                ->click('@post_share_btn')
                ->waitFor('@users_feed_share')
                ->click('@users_feed_share')
                ->waitForText(trans('feed.shared'))
                ->mouseover('@post_container')
                ->mouseover('@post_share_btn')
                ->waitForText(trans('feed.shared'));
            $content = $admin->text('@post_description');
            $admin->visit(new UsersFeed)
                ->assertSee($content);
        });
    }

    public function testIfUserCanSharePostToUser()
    {
        $post = Post::find(self::POST_ID + 6);
        $this->browse(function ($admin, $tester) use ($post) {
            $dummy_tester_name = User::find(3)->display_name;
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PostPage)
                ->goToSinglePostPage($post->slug)
                ->driver->executeScript('scroll(0, 500)');
            $admin->assertVisible('@post_container')
                ->assertVisible('@post_share_btn')
                ->click('@post_share_btn')
                ->assertVisible('@chat_share')
                ->click('@chat_share')
                ->waitFor('@sharing_modal')
                ->type('@sharing_modal .multiselect__input', 'abracafabfarfafaf')
                ->assertMissing('@chat_to_share_item')
                ->type('@sharing_modal .multiselect__input', $dummy_tester_name)
                ->waitFor('@chat_to_share_item', 10)
                ->click('@chat_to_share_item:first-of-type')
                ->click('@chat_to_share_btn')
                ->waitForText(trans('posts.share_successful'), 10)
                ->waitUntilMissing('@sharing_modal', 12);
            $tester->loginAs(User::find(self::TESTER_ID))
                ->visit(new ChatPage())
                ->waitForText(trans('posts.user_has_shared', ['user' => User::find(self::ADMIN_ID)->display_name]), 10)
                ->waitForText(str_limit($post->parsedContent, 85), 10);
        });
    }

    /**
     * @group test
     */
    public function testIfUserCanSeeFacebookShareBtn()
    {
        $post = Post::find(self::POST_ID + 7);
        $this->browse(function ($admin) use ($post) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PostPage)
                ->goToSinglePostPage($post->slug)
                ->driver->executeScript('scroll(0, 500)');
            $admin->assertVisible('@feed_main')
                ->assertVisible('@post_container')
                ->assertVisible('@post_share_btn')
                ->click('@post_share_btn')
                ->waitFor('@facebook_share');
        });
    }

    public function testIfUserCanSeeTwitterShareBtn()
    {
        $post = Post::find(self::POST_ID + 8);
        $this->browse(function ($admin) use ($post) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PostPage)
                ->goToSinglePostPage($post->slug)
                ->driver->executeScript('scroll(0, 500)');
            $admin->assertVisible('@feed_main')
                ->assertVisible('@post_container')
                ->assertVisible('@post_share_btn')
                ->click('@post_share_btn')
                ->waitFor('@twitter_share');
        });
    }
}
