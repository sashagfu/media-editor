<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Front\Controller;
use App\Traits\PassportToken;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, PassportToken;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('front.home');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function credentials(Request $request)
    {
        return [
          'email' => $request->email,
          'password' => $request->password,
          'is_verified' => 1
        ];
    }

    public function authenticated(Request $request, $user)
    {
        $client_id = config('auth.passport.client_id');

        $token = $this->getBearerTokenByUser($user, $client_id);

        return response()->json($token, 200);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => [trans('auth.failed')]];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }
}
