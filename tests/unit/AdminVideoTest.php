<?php

use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class AdminVideo extends TestCase
{
    use DatabaseMigrations;

    public function test_user_can_edit_video_performance_uncheck()
    {
        $user = $this->login_as_admin();
        $video = $this->createVideo(['is_performance' => true, 'author_id' => $user->id]);
        $template = $this->makeVideo();

        $this->visit(route('videos.edit', ['video' => $video->id]))
             ->type($template->file_path, 'file_path')
             ->select($user->id, 'author_id')
             ->uncheck('#is_performance')
             ->press('Save')
             ->see('The video was updated.')
             ->seePageIs(route('videos.edit', ['video' => $video->id]))
             ->see($template->file_path)
             ->see($template->author->username);

        $created = DB::table('videos')->whereId($video->id)->first();

        $this->assertEquals([
            'id' => 1,
            'file_path' => $template->file_path,
            'author_id' => $user->id,
            'is_performance' => false
        ], [
            'id' => (int) $created->id,
            'file_path' => $created->file_path,
            'author_id' => (int) $created->author_id,
            'is_performance' => (bool) $created->is_performance,
        ]);

        $video = Video::all()->last();
        $this->assertEquals($template->file_path, $video->file_path);
        $this->assertEquals(false, $video->is_performance);
        $this->assertEquals($user->id, $video->id);
    }

    public function test_unauthenticated_exception_handler()
    {
        $this->visit('/admin/videos');
        $this->seePageIs('/login');
    }

    public function test_unauthenticated_exception_handler_json()
    {
        $this->get('/admin/videos', ['X-Requested-With' => 'XMLHttpRequest'])
             ->seeJson(['error' => 'Unauthenticated.']);
    }

    public function test_if_admin_can_list_videos()
    {
        $user = $this->login_as_admin();
        $video = $this->createVideo(['is_performance' => 1]);
        $video->author()->associate($user);
        $video->save();

        $this->visit(route('videos.index'))
             ->see('Videos')
             ->see('ID')
             ->see('Thumbnail')
             ->see('Author')
             ->see('Performance')
             ->see('Actions')
             ->see($video->id)
             ->see($video->thumbnail_path)
             ->see($video->author->username)
             ->see('<i class="fa fa-star" title="Performance"></i>')
             ->seeLink('', route('videos.edit', ['video' => $video->id]))
        ;
    }

    public function test_user_can_see_video_edit_page()
    {
        $this->login_as_admin();
        $video = $this->createVideo();

        // TODO: Investigate seeInElement error
        $this->visit(route('videos.edit', ['video' => $video->id]))
            ->see($video->id)
            ->see($video->file_path)
            ->see($video->thumbnail_path)
            ->see($video->author->username)
            ->see('Edit Video')
            ->seeInField('file_path', $video->file_path)
            ->seeInField('thumbnail_path', $video->thumbnail_path)
            ->see('Save')
        ;
    }

    public function test_user_can_edit_video_performance()
    {
        $user = $this->login_as_admin();
        $video = $this->createVideo(['author_id' => $user->id]);
        $template = $this->makeVideo();

        $this->visit(route('videos.edit', ['video' => $video->id]))
             ->type($template->file_path, 'file_path')
             ->type($template->thumbnail_path, 'thumbnail_path')
             ->select($user->id, 'author_id')
             ->check('is_performance')
             ->press('Save')
             ->see('The video was updated.')
             ->seePageIs(route('videos.edit', ['video' => $video->id]))
             ->see($template->file_path)
             ->see($template->author->username);

        /** @var Video $created */
        $created = DB::table('videos')->whereId($video->id)->first();

        $this->assertEquals([
            'id' => 1,
            'file_path' => $template->file_path,
            'thumbnail_path' => $template->thumbnail_path,
            'author_id' => $user->id,
            'is_performance' => true
        ], [
            'id' => (int) $created->id,
            'file_path' => $created->file_path,
            'thumbnail_path' => $created->thumbnail_path,
            'author_id' => (int) $created->author_id,
            'is_performance' => (bool) $created->is_performance,
        ]);

        $video = Video::all()->last();
        $this->assertEquals($template->file_path, $video->file_path);
        $this->assertEquals($template->thumbnail_path, $video->thumbnail_path);
        $this->assertEquals(1, $video->is_performance);
        $this->assertEquals($user->id, $video->id);
    }

    public function test_user_can_edit_video_not_performance()
    {
        $user = $this->login_as_admin();
        $video = $this->createVideo(['is_performance' => false, 'author_id' => $user->id]);
        $template = $this->makeVideo();

        $this->visit(route('videos.edit', ['video' => $video->id]))
             ->type($template->file_path, 'file_path')
             ->type($template->thumbnail_path, 'thumbnail_path')
             ->select($user->id, 'author_id')
             ->press('Save')
             ->see('The video was updated.')
             ->seePageIs(route('videos.edit', ['video' => $video->id]))
             ->see($template->file_path)
             ->see($template->author->username);

        $created = DB::table('videos')->whereId($video->id)->first();

        $this->assertEquals([
            'id' => 1,
            'file_path' => $template->file_path,
            'thumbnail_path' => $template->thumbnail_path,
            'author_id' => $user->id,
            'is_performance' => false
        ], [
            'id' => (int) $created->id,
            'file_path' => $created->file_path,
            'thumbnail_path' => $created->thumbnail_path,
            'author_id' => (int) $created->author_id,
            'is_performance' => (bool) $created->is_performance,
        ]);

        $video = Video::all()->last();
        $this->assertEquals($template->file_path, $video->file_path);
        $this->assertEquals($template->thumbnail_path, $video->thumbnail_path);
        $this->assertEquals(false, $video->is_performance);
        $this->assertEquals($user->id, $video->id);
    }

    public function test_user_can_delete_video()
    {
        $user = $this->login_as_admin();
        $other_user = $this->createUser(['username' => 'Other User 12345']);

        $video = $this->createVideo();
        $target = $this->createVideo();

        $target->author()->associate($other_user);
        $target->save();

        $video->author()->associate($user);
        $video->save();

        $this->visit(route('videos.index'))
             ->see($target->thumbnail_path)
             ->see($target->author->username)
             ->seeInDatabase('videos', ['file_path' => $target->file_path, 'thumbnail_path' => $target->thumbnail_path, 'author_id' => $target->author->id]);

        $this->delete(route('videos.destroy', ['video' => $target->id]))
             ->assertRedirectedToRoute('videos.index')
             ->assertResponseStatus(302);

        $this->followRedirects()
             ->see('The video was deleted.')
             ->seePageIs(route('videos.index'))
             ->dontSee($target->thumbnail_path)
             ->dontSee($target->author->username)
             ->see($video->thumbnail_path)
             ->see($video->author->username)
             ->dontSeeInDatabase('videos', ['file_path' => $target->file_path, 'thumbnail_path' => $target->thumbnail_path, 'author_id' => $target->author->id])
             ->seeInDatabase('videos', ['file_path' => $video->file_path, 'thumbnail_path' => $video->thumbnail_path, 'author_id' => $video->author->id])
        ;

        $this->assertCount(1, Video::all());
    }

    public function test_left_menu_videos()
    {
        // TODO: Tweak menu-item.blade.php and fixclass support for menu items
        /*
        $this->login_as_admin();
        $this->click('menu-item-videos')
            ->see('Videos')
            ->seePageIs(route('videos.index'));
        */
    }

    /*
     * Turned Off Due to "java.util.ArrayList cannot be cast to java.util.Map" error
     * @url http://codeception.com/11-12-2013/working-with-phpunit-and-selenium-webdriver.html
     * @url https://habrahabr.ru/post/239645/
     * @url https://www.browserstack.com/automate/php
     * @url https://www.gridlastic.com/php-webdriver-example.html
     * @url https://toster.ru/q/212094
     *
     *
    public function test_search()
    {
        $this->webDriver->get($this->baseUrl);
        // find search field by its id
        $search = $this->webDriver->findElement(WebDriverBy::id('js-command-bar-field'));
        $search->click();
    }
    */
}

// @codingStandardsIgnoreEnd