<?php

use App\Models\Star;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StarModelTest extends TestCase
{

    use DatabaseMigrations;

    public function test_if_star_can_be_created_properly()
    {
        $star = $this->createStar();

        // Existence check
        $this->assertCount(1, Star::all());

        // Exact array check
        $this->assertEquals($star->toArray(), Star::first()->toArray());
    }

    public function test_if_star_can_be_deleted_by_id()
    {
        $star = $this->createStar();

        // Delete should return true
        $this->assertTrue(Star::find($star->id)->delete());

        // No results are expected
        $this->assertEmpty(Star::all());
    }

    public function test_protect_number_of_tested_fields()
    {
        $table = with(new Star)->getTable();
        $columns = \Schema::getColumnListing($table);

        $this->assertEquals([
            'id',
            'user_id',
            'starable_id',
            'starable_type',
            'created_at',
            'updated_at',
        ], $columns);
    }
}
