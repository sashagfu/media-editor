<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\LoginPage;

class LoginTest extends DuskTestCase
{
    const USER_EMAIL = 'admin@yuustar.com';
    const USER_PASS = 'password!';

    public function testIfUserCanSeeLoginForm()
    {
        $this->browse(function ($user) {
            $user->visit(new LoginPage())
                ->assertVisible('@login_box');
        });
    }

    public function testLoginValidation()
    {
        $this->browse(function ($user) {
            $user->visit(new LoginPage())
                ->assertVisible('@login_box')
                ->click('@login_btn')
                ->waitForText(trans('validation.required', ['attribute' => 'email']))
                ->waitForText(trans('validation.required', ['attribute' => 'password']))
                ->type('@login_email', self::USER_EMAIL)
                ->click('@login_btn')
                ->pause(500)
                ->assertDontSee(trans('validation.required', ['attribute' => 'email']))
                ->assertSee(trans('validation.required', ['attribute' => 'password']))
                ->type('@login_password', self::USER_PASS)
                ->click('@login_btn')
                ->waitFor('@top_bar')
                ->assertPathIs('/following')
                ->logout();
        });
    }

    public function testIfUserCanSLogin()
    {
        $this->browse(function ($user) {
            $user->visit(new LoginPage())
                ->assertVisible('@login_box')
                ->type('@login_email', self::USER_EMAIL)
                ->type('@login_password', self::USER_PASS)
                ->click('@login_btn')
                ->waitFor('@top_bar')
                ->assertPathIs('/following');
        });
    }
}
