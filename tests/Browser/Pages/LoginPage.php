<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class LoginPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/';
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
            '@login_box'      => '.login-box',
            '@login_email'    => '#login-box-form__e-mail',
            '@login_password' => '#login-box-form__password',
            '@login_btn'      => '.login-button-box__button',
            '@top_bar'        => '.top-bar',
        ];
    }
}
