<?php

namespace Tests\Browser\Pages;

use App\Models\Circle;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class CirclesPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/circles';
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
            '@create_circle_modal'             => '.circle-create-popup',
            '@create_circle_modal_title'       => '.circle-create-popup #input-box__title',
            '@create_circle_modal_description' => '.circle-create-popup #input-box__description',
            '@create_circle_modal_type'        => '.circle-create-popup #input-box__type',
            '@create_circle_modal_cover'       => '.circle-create-popup #input-box__cover',
            '@create_circle_modal_btn'         => '.circle-create-popup .btn.btn--primary',
            '@circle_create_form'              => '.create-circle__form',
            '@circle_create_loader'            => '.circle-create__loader',
            '@create_circle_btn'               => '.profile-action__btn.profile-action__btn--circle',
            '@feed_form'                       => '.create-post',
            '@content_div'                     => '.create-post__contenteditable',
            '@images_input'                    => '.create-post__footer input[name=images]',
            '@create_post_btn'                 => '.create-post__footer .create-post__button.create-post__button-right',
            '@post_container'                  => '.timeline__box .post',
        ];
    }

    public function createCircle(Browser $browser, $title, $description)
    {
        $browser->visit(route('front.my_profile'))
            ->waitFor('@create_circle_btn')
            ->click('@create_circle_btn')
            ->waitFor('@create_circle_modal')
            ->assertVisible('@create_circle_modal_title')
            ->assertVisible('@create_circle_modal_description')
            ->assertVisible('@create_circle_modal_type')
            ->assertVisible('@create_circle_modal_cover')
            ->type('@create_circle_modal_title', $title)
            ->type('@create_circle_modal_description', $description)
            ->click('@create_circle_modal_btn')
            ->pause(3000);
        $circle_slug = Circle::latest()->first()->slug;
        $browser->assertPathIs('/circles/' . $circle_slug);
    }
}
