<?php

use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users[] = User::create([
            'username'   => 'actionlime.admin',
            'email'      => 'admin@actionlime.com',
            'password'   => bcrypt('password!'),
            'talent'     => 'Project Management',
            'display_name' => 'Actionlime Admin',
            'quote'      => 'You only live once, but if you do it right, once is enough. To live is the rarest thing in the world. Most people exist, that is all.',
            'is_verified'=> 1
        ]);

        $users[] = User::create([
            'username'   => 'homer.simpson',
            'email'      => 'homer@simpson.com',
            'password'   => bcrypt('password!'),
            'talent'     => 'Drinking',
            'display_name' => 'Homer Simpson',
            'quote'      => 'You only live once, but if you do it right, once is enough. To live is the rarest thing in the world. Most people exist, that is all.',
            'is_verified'=> 1
        ]);

        $users[] = User::create([
            'username'   => 'experienced.tester',
            'email'      => 'tester@test.com',
            'password'   => bcrypt('password!'),
            'talent'     => 'Testing with laravel',
            'display_name' => 'Dummy tester',
            'quote'      => 'You can see a lot by just looking.',
            'is_verified'=> 1
        ]);

        $users[] = User::create([
            'username'   => 'chat.tester',
            'email'      => 'chat@test.com',
            'password'   => bcrypt('password!'),
            'talent'     => 'Testing chat',
            'display_name' => 'Chat tester',
            'quote'      => 'You can see a lot by just looking.',
            'is_verified'=> 1
        ]);

        foreach ($users as $user) {
            $default_settings = config('user.default_settings');

            $user->setSetting('privacy', $default_settings['privacy']);
        }

        factory(User::class, 10)->create();
    }
}
