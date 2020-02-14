<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */

class UserModelTest extends TestCase
{
    use DatabaseMigrations;

    public function test_if_user_can_be_created_properly()
    {
        $user = $this->createUser();

        // Dup/Existence check
        $this->assertCount(1, User::all());

        // Exact check
        $this->assertEquals($user->toArray(), User::first()->toArray());
    }

    public function test_if_user_can_be_deleted_by_id()
    {
        $user = $this->createUser();

        // Delete should return true
        $this->assertTrue(User::find($user->id)->delete());

        // No results are expected
        $this->assertEmpty(User::all());
    }

    public function test_if_user_can_be_updated_with_given_fields()
    {
        // Create a user in DB
        $user = $this->createUser();

        // Create a template
        $fake_user = $this->makeUser();

        // Let's sleep for a while to allow updated_at be different
        $updated_at = $user->updated_at;
        sleep(1);

        // Set new fields
        $user->username = $fake_user->username;
        $user->email = $fake_user->email;
        $user->password = $fake_user->password;
        $user->talent = $fake_user->talent;
        $user->display_name = $fake_user->display_name;
        $user->quote = $fake_user->quote;
        $user->is_verified = $fake_user->is_verified;
        $user->save();

        $fresh = User::find($user->id);

        $this->assertNotEquals($updated_at, $fresh->updated_at);
        $this->assertEquals($user->username, $fresh->username);
        $this->assertEquals($user->email, $fresh->email);
        $this->assertEquals($user->password, $fresh->password);
        $this->assertEquals($user->is_verified, $fresh->is_verified);
    }

    public function test_if_email_is_unique()
    {
        $this->createUser([
            'email' => 'john.doe@example.com',
        ]);

        try {
            $this->createUser([
                'email' => 'john.doe@example.com',
            ]);
        } catch (Exception $e) {
            // UNIQUE constraint failed error code
            $this->assertEquals('23000', $e->getCode());
        }

        $this->assertCount(1, User::all());
    }

    public function test_if_nickname_is_unique()
    {
        $this->createUser([
            'username' => 'john.doe',
        ]);

        try {
            $this->createUser([
                'username' => 'john.doe',
            ]);
        } catch (Exception $e) {
            // UNIQUE constraint failed error code
            $this->assertEquals('23000', $e->getCode());
        }

        $this->assertCount(1, User::all());
    }

    public function test_protect_number_of_tested_fields()
    {
        $table = with(new User)->getTable();
        $columns = \Schema::getColumnListing($table);

        $this->assertEquals([
            'id',
            'username',
            'email',
            'password',
            'is_verified',
            'verification_code',
            'total_likes',
            'total_stars',
            'remember_token',
            'created_at',
            'updated_at',
            'talent',
            'display_name',
            'quote',
        ], $columns);
    }
}

// @codingStandardsIgnoreEnd