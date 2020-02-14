<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Front\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index()
    {
        return view('front.auth.reset-password');
    }

    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        $response = $this->broker()->reset(
            $this->credentials($request),
            function (
                $user,
                $password
            ) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
            ? trans('auth.reset_pass_success')
            : response()->json(['error' => [trans('common.error_reload')]], 422);
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,15}$/',
        ];
    }

    protected function resetPassword($user, $password)
    {
        $user->forceFill(
            [
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
            ]
        )->save();
    }

    protected function validationErrorMessages()
    {
        return [
            'password.regex' => trans('auth.validation.password_strength'),
        ];
    }
}
