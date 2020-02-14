<?php

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Video::create([
            'thumbnail_path' => 'http://lorempixel.com/640/480/?64210',
            'file_path'      => 'http://lorempixel.com/640/480/?71068',
            'author_id'      => 1,
            'is_performance' => true,
            'sprite_path'    => 'http://lorempixel.com/640/480/?64210',
            'name'           => 'Some dummy name 1'
        ]);

        Video::create([
            'thumbnail_path' => 'http://lorempixel.com/640/480/?71068',
            'file_path'      => 'https://voxtobox-test.s3.amazonaws.com/colorful_1.jpg',
            'author_id'      => 1,
            'is_performance' => false,
            'sprite_path'    => 'http://lorempixel.com/640/480/?71068',
            'name'           => 'Some dummy name 2'
        ]);

        Video::create([
            'thumbnail_path' => 'http://lorempixel.com/160/120/?71069',
            'file_path'      => 'http://lorempixel.com/640/480/?71069',
            'author_id'      => 1,
            'is_performance' => false,
            'sprite_path'    => 'http://lorempixel.com/160/120/?71069',
            'name'           => 'Some dummy name 3'
        ]);

        factory(Video::class, 150)->create();
    }
}
