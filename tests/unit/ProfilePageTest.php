<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Image;

class ProfilePageTest extends TestCase
{
    use DatabaseMigrations;

    public function test_if_use_can_visit_profile_page()
    {
        $user = $this->login_as_admin();
        $this->visit(route('front.my_profile'));
        $this->see($user->display_name);
    }

    public function test_if_profile_will_be_updated()
    {
        $user = $this->login_as_admin();
        $this->visit(route('front.my_profile'));
        $this->see($user->display_name);

        $this->type('username_new', 'username');
        $this->type('display_name_new', 'display_name');
        $this->type('talent_new', 'talent');
        $this->type('quote_new', 'quote');
        $this->attach(storage_path().'/uploads/defaults/default-avatar.png', 'avatar');

        $this->press('Save');

        $this->assertEquals($user->username, 'username_new');
        $this->assertEquals($user->display_name, 'display_name_new');
        $this->assertEquals($user->talent, 'talent_new');
        $this->assertEquals($user->quote, 'quote_new');
        $this->assertEquals($user->avatars->last()->attributes, Image::find(1)->attributes);
    }

    public function test_if_user_visit_correct_link_for_his_profile()
    {
        $user = $this->login_as_admin();
        $this->visit(route('front.another_profile', ['username' => $user->username]));
        $this->see('/profile');
    }

    public function test_if_user_visit_other_users_profile()
    {
        $this->login_as_admin();
        $another_user = $this->createUser();

        $this->visit(route('front.another_profile', ['username' => $another_user->username]));
        $this->see($another_user->username);
    }

    public function test_if_user_can_follow_user()
    {
        Session::start();
        $admin = $this->login_as_admin();
        $toFollow = $this->createUser();
        $this->visit(route('front.another_profile', ['username' => $toFollow->username]));
        $this->see('Follow');

        $this->patch(route('api.follow.user'), ['follower_id' => $toFollow->id, '_token' => csrf_token()]);

        $this->seeInDatabase('user_followings', ['user_id' => $admin->id, 'follower_id' => $toFollow->id]);
    }
}
