<?php

use App\Models\FlagReason;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */

class FlagReasonModelTest extends TestCase
{
    public function test_if_flag_reason_can_be_created_properly()
    {
        $flag_reason = $this->createFlagReason();

        // Existence check
        $this->assertCount(1, FlagReason::all());

        // Exact array check
        $this->assertEquals($flag_reason->toArray(), FlagReason::first()->toArray());
    }

    public function test_if_like_can_be_deleted_by_id()
    {
        $flag_reason = $this->createFlagReason();

        // Delete should return true
        $this->assertTrue(FlagReason::find($flag_reason->id)->delete());

        // No results are expected
        $this->assertEmpty(FlagReason::all());
    }

    public function test_protect_number_of_tested_fields()
    {
        $table = with(new FlagReason())->getTable();
        $columns = \Schema::getColumnListing($table);

        $this->assertEquals([
            'id',
            'title',
            'enabled',
            'created_at',
            'updated_at',
        ], $columns);
    }
}

// @codingStandardsIgnoreEnd
