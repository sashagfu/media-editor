<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\UserFollowing;

class UserFollowingModelTest extends TestCase
{
    public function test_if_user_following_can_be_created_properly()
    {
        $user_following = $this->createUserFollowing();

        // Existence check
        $this->assertCount(1, UserFollowing::all());

        // Exact array check
        $this->assertEquals($user_following->toArray(), UserFollowing::first()->toArray());
    }

    public function test_if_user_following_can_be_deleted_by_id()
    {
        $user_following = $this->createUserFollowing();

        // Delete should return true
        $this->assertTrue(UserFollowing::find($user_following->id)->delete());

        // No results are expected
        $this->assertEmpty(UserFollowing::all());
    }

    public function test_protect_number_of_tested_fields()
    {
        $table = with(new UserFollowing())->getTable();
        $columns = \Schema::getColumnListing($table);

        $this->assertEquals([
            'id',
            'user_id',
            'follower_id',
            'created_at',
            'updated_at',
        ], $columns);
    }
}
