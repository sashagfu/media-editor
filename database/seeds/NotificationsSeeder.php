<?php

use Illuminate\Database\Seeder;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(DatabaseNotification::class, 1000)->create();
    }
}
