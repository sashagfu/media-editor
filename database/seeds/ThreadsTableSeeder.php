<?php

use Illuminate\Database\Seeder;
use App\Models\Thread;

class ThreadsTableSeeder extends Seeder
{

    protected $faker;

    public function __construct(Faker\Generator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $max_threads = 120;
        $max_existing_members = 10;

        factory(Thread::class, $max_threads)->create()->each(function ($thread) use (
            $max_existing_members
        ) {
            $existing_member_count = $this->faker->numberBetween(2, $max_existing_members);
            User::inRandomOrder()->limit($existing_member_count)->get()->each(function ($member) use ($thread) {
                $thread->addParticipant($member->id);
            });
        });
    }
}
