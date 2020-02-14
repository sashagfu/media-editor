<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 *
 * @package App\Http\Controllers\Admin
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Sets Session flash variables
     *
     * @param string $message A message to be shown
     * @param string $class   A message class
     *
     * @return void
     */
    public function setFlashMessage($message, $class)
    {
        \Request::session()->flash('message', $message);
        \Request::session()->flash('alert-class', $class);
    }
}
