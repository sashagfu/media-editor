<?php

use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class AdminPost extends TestCase
{
    use DatabaseMigrations;

    public function test_unauthenticated_exception_handler()
    {
        $this->visit('/admin/posts');
        $this->seePageIs('/login');
    }

    public function test_unauthenticated_exception_handler_json()
    {
        $this->get('/admin/posts', ['X-Requested-With' => 'XMLHttpRequest'])
             ->seeJson(['error' => 'Unauthenticated.']);
    }

    // TODO Rewrite admin part and these tests

    /*public function test_if_admin_can_list_posts()
    {
        $this->login_as_admin();
        $post = $this->createPost();
        $video = $this->createVideo(['thumbnail_path' => 'http://example.com/some-unusual-path']);
        $post->media()->associate($video);
        $post->save();

        $this->visit(route('posts.index'))
             ->see('Posts')
             ->see('ID')
             ->see('Title')
             ->see('Description')
             ->see('Media')
             ->see('Actions')
             ->see($post->id)
             ->see($post->title)
             ->see(str_limit($post->description))
             ->see($post->media->thumbnail_path)
             ->seeLink('', route('posts.edit', ['post' => $post->id]))
        ;
    }

    public function test_user_can_see_post_edit_page()
    {
        $this->login_as_admin();
        $post = $this->createPost();

        // TODO: Investigate seeInElement error
        $this->visit(route('posts.edit', ['post' => $post->id]))
             ->see($post->id)
             ->see($post->title)
             ->see($post->description)
             ->see($post->media->file_path)
             ->see('Edit Post')
             ->seeInField('title', $post->title)
             ->seeInField('description', $post->description)
             ->seeIsSelected('media_id', $post->media->id)
             ->see('Save')
        ;
    }

    public function test_user_can_delete_post()
    {
        $this->login_as_admin();
        $post = $this->createPost(['title' => 'Some Post 1', 'description' => 'Description 1']);
        $target = $this->createPost(['title' => 'Some Post 2', 'description' => 'Description 2']);

        $target_type = strtolower(str_replace('App\\Models\\', '', get_class($target->media)));
        $post_type = strtolower(str_replace('App\\Models\\', '', get_class($post->media)));

        $this->visit(route('posts.index'))
             ->see($target->title)
             ->see(str_limit($target->description))
             ->see($target->media->thumbnail_path)
             ->seeInDatabase('posts', ['title' => $target->title, 'description' => $target->description, 'media_id' => $target->media->id, 'media_type' => $target_type]);

        $this->delete(route('posts.destroy', ['post' => $target->id]))
             ->assertRedirectedToRoute('posts.index')
             ->assertResponseStatus(302);

        $this->followRedirects()
             ->see('The post was deleted.')
             ->seePageIs(route('posts.index'))
             ->dontSee($target->title)
             ->dontSee(str_limit($target->description))
             ->see($post->title)
             ->see(str_limit($post->description))
             ->dontSeeInDatabase('posts', ['title' => $target->title, 'description' => $target->description, 'media_id' => $target->media->id, 'media_type' => $target_type])
             ->seeInDatabase('posts', ['title' => $post->title, 'description' => $post->description, 'media_id' => $post->media->id, 'media_type' => $post_type]);
        ;

        $this->assertCount(1, Post::all());
    }

    public function test_left_menu_posts()
    {
        // TODO: Tweak menu-item.blade.php and fixclass support for menu items
        /*
        $this->login_as_admin();
        $this->click('menu-item-posts')
            ->see('Posts')
            ->seePageIs(route('posts.index'));

    }*/

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