<?php

use App\Models\Video;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */

class VideoModelTest extends TestCase
{
    use DatabaseMigrations;

    //TESTS

    public function test_if_video_can_be_created()
    {
        $video = $this->createVideo();

        $this->assertCount(1, Video::all());

        $this->assertEquals($video->toArray(), Video::first()->toArray());
    }

    public function test_if_video_has_user_relationship()
    {
        $user = $this->createUser();

        $video = $this->createVideo([
            'author_id' => $user->id
        ]);

        $this->assertCount(1, Video::all());

        $this->assertEquals($video->author_id, $video->author->id);
    }

    public function test_if_video_can_be_deleted_by_id()
    {
        $video = $this->createVideo();

        $this->assertTrue(Video::find($video->id)->delete());

        $this->assertEmpty(Video::all());
    }

    public function test_if_user_has_video_relationship()
    {
        $user = $this->createUser();
        $video = $this->createVideo([
            'author_id' => $user->id
        ]);
        $this->assertCount(1, $user->videos);

        $this->assertEquals($video->id, $user->videos->first()->id);
    }

    public function test_if_video_can_be_updated_with_given_fields()
    {
        $video = $this->createVideo();

        $fake_video = $this->makeVideo();

        $updated_at = $video->updated_at;
        sleep(1);

        // Set new fields
        $video->author_id = $fake_video->author_id;
        $video->file_path = $fake_video->file_path;
        $video->is_performance = $fake_video->is_performance;
        $video->save();

        $new_video = Video::find($video->id);

        $this->assertNotEquals($updated_at, $new_video->updated_at);
        $this->assertEquals($video->author_id, $new_video->author_id);
        $this->assertEquals($video->file_path, $new_video->file_path);
    }

    public function test_protect_number_of_tested_fields()
    {
        $table = with(new Video)->getTable();
        $columns = \Schema::getColumnListing($table);

        $this->assertEquals([
            'id',
            'file_path',
            'author_id',
            'is_performance',
            'created_at',
            'updated_at',
            'thumbnail_path',
            'name',         // Not tested yet. Should be tested within media editor
            'sprite_path',  // Not tested yet. Should be tested within media editor
            'time',         // Not tested yet. Should be tested within media editor
            'frames',       // Not tested yet. Should be tested within media editor
        ], $columns);
    }
}

// @codingStandardsIgnoreEnd