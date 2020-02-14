<?php

use Illuminate\Database\Seeder;
use App\Models\Invite;

class InvitesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Invite::class, 60)->create();
    }
}
