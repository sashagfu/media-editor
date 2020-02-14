<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Tests\Browser\Pages\CirclesPage;
use App\Models\Circle;

class CircleTest extends DuskTestCase
{
    const ADMIN_ID = 1;

    public function testUserCanCreateCircle()
    {
        $this->browse(function ($admin) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new CirclesPage)
                ->createCircle('Test if creating works', 'Just easy test');
        });
    }

    public function testCircleCreationValidation()
    {
        $this->browse(function ($admin) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new CirclesPage)
                ->visit(route('front.my_profile'))
                ->waitFor('@create_circle_btn')
                ->click('@create_circle_btn')
                ->waitFor('@create_circle_modal')
                ->assertVisible('@create_circle_modal_title')
                ->assertVisible('@create_circle_modal_description')
                ->assertVisible('@create_circle_modal_type')
                ->assertVisible('@create_circle_modal_cover')
                ->click('@create_circle_modal_btn')
                ->waitForText(trans('validation.required', [
                    'attribute' => 'title',
                ]))
                ->waitForText(trans('validation.required', [
                    'attribute' => 'description',
                ]))
                ->type('@create_circle_modal_title', 'some_title')
                ->click('@create_circle_modal_btn')
                ->pause(1000)
                ->assertDontSee(trans('validation.required', [
                    'attribute' => 'title',
                ]))
                ->waitForText(trans('validation.required', [
                    'attribute' => 'description',
                ]))
                ->type('@create_circle_modal_title', 'some_title')
                ->type('@create_circle_modal_description', 'description')
                ->click('@create_circle_modal_btn')
                ->pause(3000);
            $circle_slug = Circle::latest()->first()->slug;
            $admin->assertPathIs('/circles/'.$circle_slug);
        });
    }

    public function testIfUserCanAddPostToCircleFeed()
    {
        $this->browse(function ($admin) {
            $admin->loginAs(User::find(self::ADMIN_ID))
                ->visit(new CirclesPage)
                ->createCircle('Test if circle post', 'Just adding circle post')
                ->pause(2000)
                ->assertVisible('@feed_form')
                ->type('@content_div', 'Trying To add post to Circle feed')
                ->attach('images', storage_path() . '/uploads/defaults/post_images/default-1.jpg');
            $admin->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $admin->click('@create_post_btn')
                ->waitFor('@post_container', 10);
        });
    }
}
