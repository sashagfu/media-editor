<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Route;
use App\Models\User;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Admin
 */
class UserController extends Controller
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
            'username' => 'required|min:3|max:255'
        ];

        if (\Request::isMethod('put') || \Request::isMethod('patch')) {
            $user_id = Route::current()->parameter('user');

            $rules['email'] = [
                'required',
                'unique:users,email,' . $user_id,
                'email'
            ];
        } else {
            $rules['email'] = [
                'required',
                'unique:users',
                'email'
            ];
        }

        return $rules;
    }

    /**
     * @param string $searchQuery
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search($searchQuery)
    {
        return User::where('id', '=', $searchQuery)
            ->orWhere('username', 'LIKE', "%$searchQuery%")
            ->orWhere('email', 'LIKE', "%$searchQuery%");
    }
}
