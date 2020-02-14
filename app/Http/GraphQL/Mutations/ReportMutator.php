<?php

namespace App\Http\GraphQL\Mutations;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportMutator
{
    public function createReport($rootValue, array $args)
    {
        $reporter = Auth::user();

        foreach ($args['report']['reasons'] as $reason) {
            $report = new Report();

            $report_data = $args['report'];

            $report->reporter_id = $reporter->id;
            $report->reportable_type = $report_data['reportableType'];
            $report->reportable_id = $report_data['reportableId'];
            $report->reason = $reason;
            $report->description = $report_data['description'];
            $report->save();
        }

        return $report;
    }
}
