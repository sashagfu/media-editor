<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\RegistrationPage;
use Tests\Browser\Pages\LoginPage;

class RegistrationTest extends DuskTestCase
{
    const USER_EMAIL = 'uniqueEmail1132@test.com';
    const USERNAME = 'Username1132';
    const DISPLAY_NAME = 'DisplayName1123';
    const TALENT = 'Talent1123';
    const PASSWORD = 'Password1!';

    public function testIfUserCanEnterHisEmail()
    {
        $this->browse(function ($browser) {
            $browser->visit(new RegistrationPage)
                ->assertVisible('@register_btn_switch')
                ->click('@register_btn_switch')
                ->waitFor('@register_box')
                ->assertVisible('@register_email')
                ->assertVisible('@terms_conditions')
                ->type('@register_email', self::USER_EMAIL)
                ->check('@terms_conditions')
                ->click('@register_btn')
                ->waitForText(trans('auth.check_mail'));
        });
    }

    public function testIfUserRegistrationValidation()
    {
        $this->browse(function ($browser) {
            $browser->visit(new RegistrationPage)
                ->assertVisible('@register_btn_switch')
                ->click('@register_btn_switch')
                ->waitFor('@register_box')
                ->assertVisible('@register_email')
                ->assertVisible('@terms_conditions')
                ->click('@register_btn')
                ->waitForText(trans('validation.required', ['attribute' => 'email']))
                ->waitForText(trans('validation.accepted', ['attribute' => 'terms conditions']))
                ->type('@register_email', self::USER_EMAIL)
                ->click('@register_btn')
                ->waitForText(trans('validation.unique', ['attribute' => 'email']))
                ->waitForText(trans('validation.accepted', ['attribute' => 'terms conditions']))
                ->check('@terms_conditions')
                ->click('@register_btn')
                ->pause(500)
                ->assertSee(trans('validation.unique', ['attribute' => 'email']))
                ->assertDontSee(trans('validation.accepted', ['attribute' => 'terms conditions']));
        });
    }

    public function testIfUserCannotAccessSiteWithoutCompleteRegistration()
    {
        $this->browse(function ($browser) {
            $user = \User::whereEmail(self::USER_EMAIL)->first();
            $browser->visit(new LoginPage())
                ->type('@login_email', self::USER_EMAIL)
                ->type('@login_password', $user->password)
                ->click('@login_btn')
                ->waitForText(trans('auth.failed'));
        });
    }

    public function testIfUserCanCompleteHisRegistration()
    {
        $this->browse(function ($browser) {
            $user = \User::whereEmail(self::USER_EMAIL)->first();
            $browser->visit(new RegistrationPage)
                ->visit(route('auth.verify', [
                    'user_email' => $user->email,
                    'verification_code' => $user->verification_code
                    ]))
                ->assertSee(trans('auth.verify_text'))
                // Now check validation
                ->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $browser->click('@register_btn')
                ->waitForText(trans('validation.required', ['attribute' => 'username']))
                ->waitForText(trans('validation.required', ['attribute' => 'display name']))
                ->waitForText(trans('validation.required', ['attribute' => 'talent']))
                ->waitForText(trans('validation.required', ['attribute' => 'password']))
                ->type('@verify_username', self::USERNAME)
                ->click('@register_btn')
                ->pause(500)
                ->assertDontSee(trans('validation.required', ['attribute' => 'username']))
                ->assertSee(trans('validation.required', ['attribute' => 'display name']))
                ->assertSee(trans('validation.required', ['attribute' => 'talent']))
                ->assertSee(trans('validation.required', ['attribute' => 'password']))
                ->type('@verify_display_name', self::DISPLAY_NAME)
                ->click('@register_btn')
                ->pause(500)
                ->assertDontSee(trans('validation.required', ['attribute' => 'username']))
                ->assertDontSee(trans('validation.required', ['attribute' => 'display name']))
                ->assertSee(trans('validation.required', ['attribute' => 'talent']))
                ->assertSee(trans('validation.required', ['attribute' => 'password']))
                ->type('@verify_talent', self::TALENT)
                ->click('@register_btn')
                ->pause(500)
                ->assertDontSee(trans('validation.required', ['attribute' => 'username']))
                ->assertDontSee(trans('validation.required', ['attribute' => 'display name']))
                ->assertDontSee(trans('validation.required', ['attribute' => 'talent']))
                ->assertSee(trans('validation.required', ['attribute' => 'password']))
                ->type('@verify_password', 'not a password')
                ->click('@register_btn')
                ->pause(500)
                ->assertDontSee(trans('validation.required', ['attribute' => 'username']))
                ->assertDontSee(trans('validation.required', ['attribute' => 'display name']))
                ->assertDontSee(trans('validation.required', ['attribute' => 'talent']))
                ->assertSee(trans('auth.password_mismatch'))
                ->type('@verify_password', self::PASSWORD)
                ->type('.password_confirm-box-form__password_confirm', self::PASSWORD)
                // Here validations tests finishes
                ->click('@register_btn')
                ->waitFor('@welcome_redirect_link')
                ->assertPathIs('/welcome')
                ->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $browser->click('@welcome_redirect_link')
                ->waitFor('@top_bar')
                ->assertPathIs('/following')
                ->logout();
        });
    }

    public function testIfUserCannotVisitConfirmationPageOnceAgain()
    {
        $this->browse(function ($browser) {
            $user = \User::whereEmail(self::USER_EMAIL)->first();
            $browser->visit(new RegistrationPage)
                ->visit(route('auth.verify', [
                    'user_email'        => $user->email,
                    'verification_code' => $user->verification_code,
                ]))
                ->assertVisible('@menu_box');
        });
    }

    public function testIfUserCanVisitWelcomePageOnceAgain()
    {
        $this->browse(function ($browser) {
            $user = \User::whereEmail(self::USER_EMAIL)->first();
            $browser->visit(new RegistrationPage)
                ->loginAs($user->id)
                ->visit(route('front.welcome'))
                ->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $browser->assertVisible('@welcome_redirect_link');
        });
    }
}
