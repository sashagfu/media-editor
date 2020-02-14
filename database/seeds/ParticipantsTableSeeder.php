<?php

use Illuminate\Database\Seeder;
use Cmgmyr\Messenger\Models\Participant;

class ParticipantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Participant::create([
            'thread_id' => 1,
            'user_id' => 1
        ]);

        Participant::create([
            'thread_id' => 1,
            'user_id' => 3
        ]);
        factory(Participant::class, 250)->create();
    }
}
