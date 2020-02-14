<?php

use App\Models\Circle;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class CircleModelTest extends TestCase
{
    use DatabaseMigrations;

    public function test_if_circle_can_be_created_properly()
    {
        $circle = $this->createCircle();

        // Existence check
        $this->assertCount(1, Circle::all());

        // Exact array check
        $this->assertEquals($circle->toArray(), Circle::first()->toArray());
    }

    public function test_if_circle_can_be_deleted_by_id()
    {
        $circle = $this->createCircle();

        // Delete should return true
        $this->assertTrue(Circle::find($circle->id)->delete());

        // No results are expected
        $this->assertEmpty(Circle::all());
    }

    public function test_protect_number_of_tested_fields()
    {
        $table = with(new Circle)->getTable();
        $columns = \Schema::getColumnListing($table);

        $this->assertEquals([
            'id',
            'title',
            'slug',
            'description',
            'creator_id',
            'type',
            'post_adding_privacy',
            'created_at',
            'updated_at',
        ], $columns);
    }
}
