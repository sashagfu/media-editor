<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Post;
use Tests\Browser\Pages\PerformancePage;


class PerformancesPageTest extends DuskTestCase
{
    const ADMIN_ID = 1;
    const POSTS_PER_PAGE = 10;

    public function testPerformancesPage()
    {
        $this->browse(function ($admin) {
            $user = User::find(self::ADMIN_ID);
            $posts_count = Post::performancePosts()
                ->skipFlagged($user)
                ->count();
            $scroll_times = floor($posts_count/10);

            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new PerformancePage);
            if ($posts_count < self::POSTS_PER_PAGE) {
                $admin->assertCountItems($posts_count, '@post_container');
            } else {
                $admin->assertCountItems(self::POSTS_PER_PAGE, '@post_container');
                for ($i = 1; $i <= $scroll_times; $i++) {
                    $admin->pause(500);
                    $admin->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
                    $admin->waitUntilMissing('.loading-default')
                            ->pause(8000);
                    $i == $scroll_times ?
                        $admin->assertCountItems($posts_count, '@post_container')
                        : $admin->assertCountItems(($i * self::POSTS_PER_PAGE) + self::POSTS_PER_PAGE,
                        '@post_container');
                }
            }
        });
    }
}
