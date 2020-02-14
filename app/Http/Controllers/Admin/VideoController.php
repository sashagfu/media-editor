<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;

/**
 * Class VideoController
 *
 * @package App\Http\Controllers\Admin
 */
class VideoController extends Controller
{
    use ManagesCRUD {
        index as ManagesCRUDIndex;
    }

    public function index(Request $request)
    {
        return $this->ManagesCRUDIndex($request);
    }

    protected $paginateCount = 25;

    /**
     * Defines model validation rules for storing and updating data
     *
     * @return array
     */
    protected function getValidationRules()
    {
        $rules = [
            'file_path' => 'required|min:3|max:255',
            'thumbnail_path' => 'required|min:3|max:255',
        ];

        return $rules;
    }

    /**
     * @param Request $request
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @return Request
     */
    protected function filterRequest(Request $request)
    {
        if (!isset($request->is_performance)) {
            $request->merge(['is_performance' => false]);
        }

        return $request;
    }

    /**
     * Defines Model relationships
     *
     * @return array
     */
    protected function getRelationshipFields()
    {
        return [
            'authors' => User::pluck('username', 'id'),
        ];
    }

    protected function getIndexRelationshipFields()
    {
        return [
            'author',
        ];
    }
}
