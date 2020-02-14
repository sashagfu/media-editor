<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ManagesCRUD {
        index as ManagesCRUDIndex;
    }

    public function users(Request $request)
    {
        return $this->ManagesCRUDIndex($request);
    }

    protected function getEntityName()
    {
        return 'reports.users';
    }
}
