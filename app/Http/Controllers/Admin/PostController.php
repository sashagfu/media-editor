<?php

namespace App\Http\Controllers\Admin;

use App\Models\Flag;
use App\Models\Post;
use App\Models\Video;
use Illuminate\Http\Request;

/**
 * Class PostController
 *
 * @package App\Http\Controllers\Admin
 */
class PostController extends Controller
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

    /**
     * @param Request $request
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @return Request
     */
    protected function filterRequest(Request $request)
    {
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
            'media' => Video::pluck('file_path', 'id'),
        ];
    }

    protected function getIndexRelationshipFields()
    {
        return [
            'media',
        ];
    }
}
