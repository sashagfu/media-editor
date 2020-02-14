<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    // TESTS
    public function test_user_can_get_reset_password_page()
    {
        $this->visit('/password/reset');
    }

    public function test_user_can_get_login_page()
    {
        $this->visit('/login');
    }

    public function test_user_can_get_register_page()
    {
        $this->visit('/register');
    }

    public function test_user_cannot_type_empty_email()
    {
        $this->visit('/register');
        $this->press('Register');
        $this->see('The email field is required');
    }

    public function test_terms_conditions_validation_works()
    {
        $this->visit('/register');
        $this->press('Register');
        $this->see('The terms conditions must be accepted.');
    }

    public function test_user_created_successfully_after_register()
    {
        $this->visit('/register');
        $this->type($this->faker->email, 'email');
        $this->check('terms_conditions');
        $this->press('Register');

        $this->assertCount(1, User::all());
    }
    public function test_if_user_can_visit_confirmation_page()
    {
        $email = $this->faker->email;
        $this->visit('/register');
        $this->type($email, 'email');
        $this->check('terms_conditions');
        $this->press('Register');

        $user = User::all()->last();
        $this->actingAs($user);
        $this->visit('/users/' . $email . '/verify/' . $user->verification_code);
    }

    public function test_if_user_will_be_updated()
    {
        $email = $this->faker->email;
        $this->visit('/register');
        $this->type($email, 'email');
        $this->check('terms_conditions');
        $this->press('Register');

        $user = User::all()->last();

        $this->visit('/users/' . $email . '/verify/' . $user->verification_code);
        $this->type('Qwerty123!', 'password');
        $this->press('Save Info');

        $this->assertEquals(1, $user->fresh()->is_verified);
        $this->seePageIs('welcome');
    }

    public function test_user_cannot_visit_confirmation_account_page_again()
    {
        $email = $this->faker->email;
        $this->visit('/register');
        $this->type($email, 'email');
        $this->check('terms_conditions');
        $this->press('Register');

        $user = User::all()->last();

        $this->visit('/users/' . $email . '/verify/' . $user->verification_code);
        $this->type('Qwerty123!', 'password');
        $this->press('Save Info');

        $this->assertEquals(1, $user->fresh()->is_verified);

        $this->visit('/users/' . $email . '/verify/' . $user->verification_code);
        $this->see('Your email has already been verified!');
    }

    public function test_user_is_redirected_from_login_if_already_logged_in()
    {
        $this->login_as_admin();

        $this->visit('/login')
            ->seePageIs('/following');
    }

    public function test_user_is_redirected_from_password_reset_if_already_logged_in()
    {
        $this->login_as_admin();

        $this->visit('/password/reset')
            ->seePageIs('/following');
    }

    public function test_user_can_see_password_restore_page()
    {
        $this->visit('/password/reset')
            ->see('Reset Password')
            ->see('Send Password Reset Link');
    }

    public function test_unregisterd_user_can_not_visit_following_page()
    {
        $this->visit('/following');
        $this->seePageIs('/login');
    }

    public function test_unregistered_user_can_visit_home_page()
    {
        $this->visit('/');
        $this->seePageIs('/');
    }

    public function test_registered_user_can_not_visit_home_page()
    {
        $this->login_as_admin();
        $this->visit('/');
        $this->seePageIs('/following');
    }

    public function test_loggined_user_will_not_see_js_lang_file()
    {
        $user = $this->createUser([
            'password' => bcrypt('Password!'),
            'is_verified' => 1
        ]);
        $this->visit('/login');
        $this->type($user->email, 'email');
        $this->type('Password!', 'password');
        $this->press('Sign In');
        $this->seePageIs('/following');
    }
}

// @codingStandardsIgnoreEnd