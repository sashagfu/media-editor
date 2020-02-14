<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Front\Controller;
use App\Models\User;
use App\Traits\PassportToken;
use Illuminate\Auth\Events\Registered;
use Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use App\Models\Invite;
use App\Models\Circle;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, PassportToken;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
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
        $this->middleware('guest', ['except' => ['verify', 'update']]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
            'email' => 'required|email|max:255|unique:users,email',
            'terms_conditions' => 'accepted'
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        $user =  User::create(
            [
            'username' => strstr($data['email'], '@', true),
            'email' => $data['email'],
            'verification_code' => str_random(20),
            'password' => bcrypt(str_random(16)),
            ]
        );

        $default_settings = config('user.default_settings');

        $user->setSetting('privacy', $default_settings['privacy']);

        return $user;
    }

    public function verify($user_email, $verification_code)
    {
        $user = User::whereEmail($user_email)
            ->whereVerificationCode($verification_code)
            ->firstOrFail();

        if ($user->is_verified || \Auth::check()) {
            return redirect(route('feed.index'));
        }

        return view('user/verify', compact('user'));
    }

    public function verifyCheck($user_id, Request $request)
    {

        $messages = [
            'password.regex' => trans('auth.validation.password_strength'),
        ];

        $this->validate(
            $request,
            [
            'username' => 'required|max:20|unique:users,username,'.$user_id,
            'password' => 'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,20}$/',
            'display_name' => 'required|max:20',
            ],
            $messages
        );


        $user = User::findOrFail($user_id);

        $user->is_verified = 1;
        $user->password = bcrypt($request->input('password'));
        $user->username = $request->input('username');
        $user->display_name = $request->display_name;

        $user->save();

        $invite = Invite::whereEmail($user->email)
            ->whereUsed(false)
            ->whereActive(true)
            ->first();

        if ($invite) {
            $circle_id = $invite->circle_id;
            $circle = Circle::find($circle_id);
            $circle->members()->attach($user, ['status' => Circle::STATUS_MEMBER]);
        }

        \Auth::login($user);

        $client_id = config('auth.passport.client_id');

        $token = $this->getBearerTokenByUser($user, $client_id);

        return response()->json($token, 200);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        event(new Registered($user));

        return trans('auth.check_mail');
    }

    public function circleInvite($encoded_invite)
    {
        $decoded_invite = base64_decode($encoded_invite);
        $invite_info = json_decode($decoded_invite);

        $invite_email = $invite_info->email;
        $invite_id = $invite_info->invite_id;
        $user = User::whereEmail($invite_email)->first();
        $invite_check = !Invite::whereId($invite_id)
            ->whereUsed(false)
            ->whereActive(true)
            ->exists();

        if ($invite_check) {
            return redirect(route('index'));
        }

        if ($user && $user->is_verified == false) {
            return redirect(
                route(
                    'auth.verify',
                    [
                    'user_email' => $invite_email,
                    'verification_code' => $user->verification_code,
                    ]
                )
            );
        } else {
            $user = $this->create(['email' => $invite_email]);
            return redirect(
                route(
                    'auth.verify',
                    [
                    'user_email' => $invite_email,
                    'verification_code' => $user->verification_code,
                    ]
                )
            );
        }
    }
}
