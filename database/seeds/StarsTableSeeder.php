<?php

use App\Models\Star;
use Illuminate\Database\Seeder;

class StarsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Star::class, 350)->create();
    }
}
