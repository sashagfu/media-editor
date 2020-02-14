<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class AdminUser extends TestCase
{
    use DatabaseMigrations;

    public function test_unauthenticated_exception_handler()
    {
        $this->visit('/admin');
        $this->seePageIs('/login');
    }

    public function test_unauthenticated_exception_handler_json()
    {
        $this->get('/admin', ['X-Requested-With' => 'XMLHttpRequest'])
            ->seeJson(['error' => 'Unauthenticated.']);
    }

    public function test_if_admin_can_see_index()
    {
        $this->login_as_admin();

        $this->visit('/admin')
            ->see('Dashboard')
            ->see('<b>Yuustar</b> Admin')
            ->see('Welcome to this beautiful admin panel');

    }

    public function test_if_admin_can_list_users()
    {
        $user = $this->login_as_admin();

        $this->visit('/admin/users')
             ->see('Users')
             ->see('<b>Yuustar</b> Admin')
             ->see('ID')
             ->see('User Name')
             ->see('E-Mail')
             ->see('Actions')
             ->see($user->id)
             ->see($user->username)
             ->see($user->email)
             ->seeLink('', route('users.edit', ['user' => $user->id]))
        ;
    }

    public function test_if_admin_can_search_users()
    {
        $user1 = factory(User::class)->create([
            'username' => 'johndoe',
            'email' => 'johndoe@example.com'
        ]);

        $user2 = factory(User::class)->create([
            'username' => 'jeaniedoe',
            'email' => 'jeaniedoe@example.com',
        ]);

        $this->be($user1);

        $this->visit('/admin/users?q=jeanie')
            ->see($user2->id)
            ->see($user2->username)
            ->see($user2->email)
            ->dontSee($user1->username)
            ->dontSee($user1->email)
            ->seeLink('', route('users.edit', ['user' => $user2->id]))
        ;
    }

    public function test_user_can_see_edit_page()
    {
        $user = $this->login_as_admin();

        // TODO: Investigate seeInElement error
        $this->visit(route('users.edit', ['user' => $user->id]))
            ->see($user->id)
            ->see($user->username)
            ->see($user->email)
            ->see('Edit User')
            //->seeInElement('username', $user->username)
            //->seeInElement('email', $user->emailusername)
            //->seeInElement('password', '')
            ->see('Save')
        ;
    }

    public function test_user_can_edit_user()
    {
        $user = $this->login_as_admin();
        $template = factory(User::class)->make();

        $this->visit(route('users.edit', ['user' => $user->id]))
            ->type($template->email, 'email')
            ->type($template->username, 'username')
            ->press('Save')
            ->see('The user was updated.')
            ->seePageIs(route('users.edit', ['user' => $user->id]))
            ->see($template->email)
            ->see($template->username)
            ->seeInDatabase('users', ['email' => $template->email, 'username' => $template->username]);

        $user = User::all()->last();
        $this->assertEquals($template->username, $user->username);
        $this->assertEquals($template->email, $user->email);
    }

    public function test_user_can_delete_user()
    {
        $user = $this->login_as_admin();
        $target = factory(User::class)->create();

        $this->visit(route('users.index'))
            ->see($target->email)
            ->see($target->username)
            ->seeInDatabase('users', ['email' => $target->email, 'username' => $target->username]);

        $this->delete(route('users.destroy', ['user' => $target->id]))
            ->assertRedirectedToRoute('users.index')
            ->assertResponseStatus(302);

        $this->followRedirects()
            ->see('The user was deleted.')
            ->seePageIs(route('users.index'))
            ->dontSee($target->email)
            ->dontSee($target->username)
            ->see($user->email)
            ->see($user->username)
            ->dontSeeInDatabase('users', ['email' => $target->email, 'username' => $target->username])
            ->seeInDatabase('users', ['email' => $user->email, 'username' => $user->username])
        ;

        $this->assertCount(1, User::all());
    }

    public function test_left_menu_users()
    {
        // TODO: Tweak menu-item.blade.php and fixclass support for menu items
        /*
        $this->login_as_admin();
        $this->click('menu-item-users')
            ->see('Users')
            ->seePageIs(route('users.index'));
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