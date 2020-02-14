<?php

use Illuminate\Database\Seeder;
use App\Models\FlagReason;

class FlagReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
          ['title' => 'This content is annoying or is not relevant', 'enabled' => true],
          ['title' => 'I think a content like this shouldn\'t be here', 'enabled' => true],
          ['title' => 'It\'s a spam', 'enabled' => true],
          ['title' => 'The quality of this content is pretty low'],
        ];

        foreach ($items as $item) {
            factory(FlagReason::class)->create($item);
        }
    }
}
