<?php

use Illuminate\Database\Seeder;
use Cmgmyr\Messenger\Models\Message;

class MessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Message::create([
            'thread_id' => 1,
            'user_id'   => 1,
            'body'      => 'Hello world!',
        ]);

        Message::create([
            'thread_id' => 1,
            'user_id'   => 3,
            'body'      => 'I am a tester!',
        ]);

        factory(Message::class, 350)->create();
    }
}
