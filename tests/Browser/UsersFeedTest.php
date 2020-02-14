<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\UsersFeed;
use User;

class UsersFeedTest extends DuskTestCase
{
    const ADMIN_ID = 1;

    public function testIfUserCanSeeFeed()
    {
        $this->browse(function ($admin) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new UsersFeed)
                ->assertVisible('@feed_main');
            if (User::find(self::ADMIN_ID)->feed->count() == 0) {
                $admin->assertMissing('@post_container');
            } else {
                $admin->assertVisible('@post_container');
            }
        });
    }

    public function testPostCreationValidation()
    {
        $this->browse(function ($admin) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new UsersFeed);
            $admin->driver->executeScript('window.scrollTo(0, 500)');
            $admin->click('@create_post_btn')
                ->assertVisible('@loader')
                ->waitForText(trans('validation.required', [
                    'attribute' => 'post content',
                ]))
                ->assertSeeIn('@feed_main', trans('validation.required', [
                    'attribute' => 'post content',
                ]))
                ->assertSeeIn('@feed_main', trans('validation.required_without_all', [
                    'attribute' => 'video id',
                    'values'    => 'images',
                ]))
                ->assertMissing('@loader')
                ->type('@content_div', 'something')
                ->click('@create_post_btn')
                ->assertVisible('@loader')
                ->waitForText(trans('validation.required_without_all', [
                    'attribute' => 'video id',
                    'values'    => 'images',
                ]))
                ->assertDontSeeIn('@feed_main', trans('validation.required', [
                    'attribute' => 'post content',
                ]))
                ->assertSeeIn('@feed_main', trans('validation.required_without_all', [
                    'attribute' => 'video id',
                    'values'    => 'images',
                ]))
                ->assertMissing('@loader')
                ->attach('images', storage_path() . '/uploads/defaults/post_images/default-1.jpg')
                ->click('@create_post_btn')
                ->waitFor('@post_container')
                ->assertVisible('@post_container');
        });
    }

    public function testIfPostWithImageCanBeCreated()
    {
        $this->browse(function ($admin) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new UsersFeed)
                ->createPost('Test post with images #image', 'image');
        });
    }

    public function testIfPostWithVideoCanBeCreated()
    {
        $this->browse(function ($admin) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new UsersFeed)
                ->createPost('Test post with video #video', 'video');
        });
    }
}
