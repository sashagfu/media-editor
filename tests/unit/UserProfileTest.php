<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */

class UserProfileTest extends TestCase
{
    use DatabaseMigrations;

    public function test_if_profile_has_mandatory_fields()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $this->visit('/profile')
            ->see($user->display_name)
            ->see($user->quote)
            ->see($user->username);

        $this->markTestIncomplete('This test class is incomplete. TODO: Add more tests.');

    }
}

// @codingStandardsIgnoreEnd