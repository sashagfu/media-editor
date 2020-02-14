<?php

use Illuminate\Database\Seeder;
use App\Models\Image;

class ImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $images_count = 7;
        $default_width = 615;
        $default_height = 400;
        $default_size = 100000;

        for ($i = 1; $i <= $images_count; $i++) {
            Image::create([
                'file_name' => 'default-' . $i .'.jpg',
                'width'     => $default_width,
                'height'    => $default_height,
                'file_size' => $default_size,
            ]);
        }
    }
}
