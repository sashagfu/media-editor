<?php

use Illuminate\Database\Seeder;
use App\Models\UserFollowing;

class UserFollowingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(UserFollowing::class, 400)->create();

        UserFollowing::create([
            'user_id' => 1,
            'follower_id' => 2,
        ]);

        UserFollowing::create([
            'user_id' => 1,
            'follower_id' => 3,
        ]);
    }
}
