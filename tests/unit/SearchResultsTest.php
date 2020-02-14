<?php

use App\Models\SearchResult;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SearchResultTest extends TestCase
{

    use DatabaseMigrations;

    public function test_if_search_result_can_be_created_properly()
    {
        $search_result = $this->createSearchResult();

        // Existence check
        $this->assertCount(1, SearchResult::all());

        // Exact array check
        $this->assertEquals($search_result->toArray(), SearchResult::first()->toArray());
    }

    public function test_if_star_can_be_deleted_by_id()
    {
        $search_result = $this->createSearchResult();

        // Delete should return true
        $this->assertTrue(SearchResult::find($search_result->id)->delete());

        // No results are expected
        $this->assertEmpty(SearchResult::all());
    }

    public function test_protect_number_of_tested_fields()
    {
        $table = with(new SearchResult)->getTable();
        $columns = \Schema::getColumnListing($table);

        $this->assertEquals([
            'id',
            'search_term',
            'total_search',
            'created_at',
            'updated_at',
        ], $columns);
    }
}
