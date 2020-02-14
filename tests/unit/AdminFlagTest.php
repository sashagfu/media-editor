<?php

use App\Models\Flag;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class AdminFlagTest extends TestCase
{
    use DatabaseMigrations;

    public function test_unauthenticated_exception_handler()
    {
        $this->visit('/admin/flags');
        $this->seePageIs('/login');
    }

    public function test_unauthenticated_exception_handler_json()
    {
        $this->get('/admin/flags', ['X-Requested-With' => 'XMLHttpRequest'])
             ->seeJson(['error' => 'Unauthenticated.']);
    }

    public function test_if_admin_can_list_flags()
    {
        $user = $this->login_as_admin();
        $flag = $this->createFlag(['is_verified' => true]);
        $flag->author()->associate($user);
        $flag->save();

        $this->visit(route('flags.index'))
             ->see('Flags')
             ->see('ID')
             ->see('Flagged Item')
             ->see('Flagged By')
             ->see('Description')
             ->see('Flagged At')
             ->see('Actions')
             ->see($flag->id)
             ->see($flag->flaggable->title)
             ->see(str_limit($flag->description))
             ->see($flag->flaggable->id)
             ->see($flag->author->username)
             ->see($flag->created_at->diffForHumans())
             ->dontSee('<i class="fa fa-check-circle-o" title="Verify"></i>')
        ;
    }

    public function test_if_admin_can_list_flags_not_verified()
    {
        $user = $this->login_as_admin();
        $flag = $this->createFlag(['is_verified' => false]);
        $flag->author()->associate($user);
        $flag->save();

        $this->visit(route('flags.index'))
             ->see('Flags')
             ->see('ID')
             ->see('Flagged Item')
             ->see('Flagged By')
             ->see('Description')
             ->see('Flagged At')
             ->see('Actions')
             ->see($flag->id)
             ->see(str_limit($flag->description))
             ->see($flag->flaggable->title)
             ->see($flag->flaggable->id)
             ->see($flag->author->username)
             ->see($flag->created_at->diffForHumans())
             ->see('<i class="fa fa-check-circle-o" title="Verify"></i>')
        ;
    }

    public function test_user_can_verify_content()
    {
        $user = $this->login_as_admin();
        $flag = $this->createFlag(['is_verified' => false]);
        $flag->author()->associate($user);
        $flag->save();

        $this->visit(route('flags.index'))
            ->see('Flags')
            ->see('ID')
            ->see('Flagged Item')
            ->see('Flagged By')
            ->see('Description')
            ->see('Flagged At')
            ->see('Actions')
            ->see($flag->id)
            ->see($flag->flaggable->title)
            ->see(str_limit($flag->description))
            ->see($flag->flaggable->id)
            ->see($flag->author->username)
            ->see($flag->created_at->diffForHumans())
            ->see('<i class="fa fa-check-circle-o" title="Verify"></i>')
            ->patch(route('flags.verify', ['id' => $flag->id]))
            ->seeStatusCode(302)
            ->followRedirects()
            ->seePageIs(route('flags.index'))
            ->see('The content was marked as verified.')
            ->see('Flags')
            ->see('ID')
            ->see('Flagged Item')
            ->see('Flagged By')
            ->see('Description')
            ->see('Flagged At')
            ->see('Actions')
            ->see($flag->id)
            ->see($flag->flaggable->title)
            ->see(str_limit($flag->description))
            ->see($flag->flaggable->id)
            ->see($flag->author->username)
            ->see($flag->created_at->diffForHumans())
            ->dontSee('<i class="fa fa-check-circle-o" title="Verify"></i>')
        ;
    }

    public function test_user_can_delete_flag()
    {
        $this->login_as_admin();
        $some_user = $this->createUser(['username' => 'Some user']);
        $another_user = $this->createUser(['username' => 'Super another user 11!']);

        $flag = $this->createFlag();
        $flag->author()->associate($another_user);
        $flag->save();

        $target = $this->createFlag();
        $target->author()->associate($some_user);
        $target->save();

        $this->visit(route('flags.index'))
            ->see($flag->id)
            ->see($flag->flaggable->title)
            ->see(str_limit($flag->description))
            ->see($flag->flaggable->id)
            ->see($flag->author->username)
            ->see($target->flaggable->title)
            ->see(str_limit($target->description))
            ->see($target->flaggable->id)
            ->see($target->author->username)
            ->seeInDatabase('flags', ['flaggable_id' => $target->flaggable->id, 'flaggable_type' => 'post', 'description' => $target->description, 'is_verified' => $target->is_verified])
            ->delete(route('flags.destroy', ['flag' => $target->id]))
            ->assertRedirectedToRoute('flags.index')
            ->assertResponseStatus(302)
            ->followRedirects()
            ->see('The flag was deleted.')
            ->seePageIs(route('flags.index'))
            ->dontSee(str_limit($target->description))
            ->dontSee($target->author->username)
            ->see(str_limit($flag->description))
            ->see($flag->author->username)
            ->seeInDatabase('flags', ['flaggable_id' => $flag->flaggable->id, 'flaggable_type' => 'post', 'description' => $flag->description, 'is_verified' => $flag->is_verified])
            ->dontSeeInDatabase('flags', ['flaggable_id' => $target->flaggable->id, 'flaggable_type' => 'post', 'description' => $target->description, 'is_verified' => $target->is_verified])
        ;

        $this->assertCount(1, Flag::all());
    }

    public function test_user_can_view_post_flags()
    {
        $user = $this->login_as_admin();

        $post = $this->createPost();

        $flag1 = $this->makeFlag();
        $flag1->author()->associate($user);
        $flag1->flaggable()->associate($post);
        $flag1->save();

        $flag2 = $this->makeFlag();
        $flag2->author()->associate($user);
        $flag2->flaggable()->associate($post);
        $flag2->save();

        $flag3 = $this->makeFlag();
        $flag3->author()->associate($user);
        $flag3->flaggable()->associate($post);
        $flag3->save();

        $this->visit(route('posts.flags', ['post' => $post->id]))
            ->see('Flags
                     for')
            ->see($post->title)
            ->see('Flagged By')
            ->see('Flagged At')
            ->see('Description')
            ->see('Actions')
            ->see('All Flags')
            ->see($post->flaggable->get(0)->id)
            ->see($post->flaggable->get(1)->id)
            ->see($post->flaggable->get(2)->id)
            ->see(str_limit($post->flaggable->get(0)->description))
            ->see(str_limit($post->flaggable->get(1)->description))
            ->see(str_limit($post->flaggable->get(2)->description))
        ;
    }

}

// @codingStandardsIgnoreEnd