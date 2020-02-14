<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use Tests\DuskTestCase;

class UsersFeed extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/profile';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@feed_main'            => '.create-post',
            '@post_container'       => '.timeline__box .post',
            '@content_div'          => '.create-post__contenteditable',
            '@images_input'         => '.create-post__footer input[name=images]',
            '@videos_btn_selection' => '.create-post__footer > a:nth-child(2) > i.fa-film',
            '@videos_modal_content' => '.modal-content.videos-content',
            '@videos_modal_item'    => '.videos-content__item',
            '@video_select_btn'     => 'a.videos-content__select-btn',
            '@create_post_btn'      => '.create-post__footer .create-post__button.create-post__button-right',
            '@loader'               => '.create-post__footer .create-post__button.create-post__button-right > i.fa-fw',
            '@close'                => '.videos-modal .modal-close'
        ];
    }

    public function createPost(Browser $browser, $content, $media_type)
    {
        $browser->visit(route('front.my_profile'));
        $browser->driver->executeScript('window.scrollTo(0, 500);');
        $browser->assertVisible('@feed_main')
            ->type('@content_div', $content);
        if ($media_type == 'image') {
            $browser->attach('@images_input', storage_path() . '/uploads/defaults/post_images/default-1.jpg');
        }
        if ($media_type == 'video') {
            $browser->click('@videos_btn_selection')
                ->assertVisible('@videos_modal_content')
                ->waitFor('@videos_modal_item:first-of-type')
                ->mouseover('@videos_modal_item:first-of-type')
                ->assertVisible('@video_select_btn')
                ->click('@video_select_btn')
                ->click('@close');
        }
        $browser->click('@create_post_btn')
            ->waitFor('@post_container')
            ->assertVisible('@post_container');
    }
}
