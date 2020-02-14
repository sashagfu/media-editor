<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\FollowingPage;
use App\Models\User;
use App\Models\Post;

class FollowingPageTest extends DuskTestCase
{
    const ADMIN_ID = 1;
    const POSTS_PER_PAGE = 10;

    public function testFollowingPage()
    {
        $this->browse(function ($admin) {
            $user = User::find(self::ADMIN_ID);
            $posts_count = Post::followingPosts($user)
                ->skipFlagged($user)
                ->latest()
                ->count();
            $scroll_times = floor($posts_count/10);

            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new FollowingPage);
            if ($posts_count < self::POSTS_PER_PAGE) {
                $admin->assertCountItems($posts_count, '@post_container');
            } else {
                $admin->assertCountItems(self::POSTS_PER_PAGE, '@post_container');
                for ($i = 1; $i <= $scroll_times; $i++) {
                    $admin->pause(500);
                    $admin->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
                    $admin->waitUntilMissing('.loading-default')
                        ->pause(2000);
                    $i == $scroll_times ? $admin->assertCountItems($posts_count, '@post_container')
                        : $admin->assertCountItems(($i * self::POSTS_PER_PAGE) + self::POSTS_PER_PAGE,
                        '@post_container');
                }
            }
        });
    }
}
