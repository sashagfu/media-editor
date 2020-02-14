<?php

namespace App\Http\Controllers\Admin;

/**
 * Class FlagReasonController
 *
 * @package App\Http\Controllers\Admin
 */
class FlagReasonController extends Controller
{
    use ManagesCRUD;

    /**
     * Defines model validation rules for storing and updating data
     *
     * @return array
     */
    protected function getValidationRules()
    {
        $rules = [
            'title' => 'required|min:3|max:255',
        ];

        return $rules;
    }
}
