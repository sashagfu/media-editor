<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class RegistrationPage extends BasePage
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
            '@menu_box'                   => '.menu-box',
            '@register_btn_switch'        => '.button-box--register',
            '@register_box'               => '.register-box',
            '@register_email'             => '#register-box-form__e-mail',
            '@terms_conditions'           => '.accept-box-form__label',
            '@register_btn'               => '.register-button-box__button',
            '@verify_username'            => '#username-box-form__username',
            '@verify_display_name'        => '#display_name-box-form__display_name',
            '@verify_talent'              => '#talent-box-form__talent',
            '@verify_password'            => '#password-box-form__password',
            '@verify_password_confirm'    => '.password_confirm-box-form__password_confirm',
            '@toggle_password_visibility' => '.password_visibility-form__checkbox',
            '@welcome_redirect_link'      => '.welcome-redirect',
            '@top_bar'                    => '.top-bar',
        ];
    }
}
