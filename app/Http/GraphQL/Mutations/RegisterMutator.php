<?php

namespace App\Http\GraphQL\Mutations;

use App\Models\Circle;
use App\Models\Invite;
use App\Models\User;
use App\Traits\PassportToken;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Http\Request;

class RegisterMutator
{
    use RegistersUsers, ValidatesRequests, PassportToken;

    public function registerUser($root, $args, $context)
    {
        Validator::make(
            $args,
            [
                'email' => 'required|email|max:255|unique:users,email',
                'termsConditions' => 'accepted'
            ]
        )->validate();

        $user = new User();
        $user->username = strstr($args['email'], '@', true);
        $user->email = $args['email'];
        $user->verification_code = str_random(20);
        $user->password = bcrypt(str_random(16));
        $user->save();

        event(new Registered($user));

        $message = trans('auth.check_mail');

        return [
            'message' => $message,
            'statusCode' => 200
        ];
    }

    public function verifyUser($root, $args)
    {
        $user = User::whereEmail($args['email'])
            ->whereVerificationCode($args['verificationCode'])
            ->firstOrFail();

        if ($user->is_verified || \Auth::check()) {
            return response()->json([
                'message' => 'User already verified.',
                'statusCode' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'User verified.',
            'statusCode' => 200,
        ], 200);
    }

    public function createUser($root, $args, $context)
    {
        $messages = [
            'password.regex' => trans('auth.validation.password_strength'),
        ];

        Validator::make(
            $args,
            [
                'username' => 'required|max:20|unique:users,username',
                'password' => 'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,20}$/',
                'displayName' => 'required|max:20',
            ],
            $messages
        )->validate();

        $user = User::whereEmail($args['email'])->firstOrFail();

        $user->is_verified = 1;
        $user->password = bcrypt($args['password']);
        $user->username = $args['username'];
        $user->display_name = $args['displayName'];
        $user->save();

        $default_settings = config('user.default_settings');

        $user->setSetting('privacy', $default_settings['privacy']);

        $invite = Invite::whereEmail($user->email)
                        ->whereUsed(false)
                        ->whereActive(true)
                        ->first();

        if ($invite) {
            $circle_id = $invite->circle_id;
            $circle = Circle::find($circle_id);
            $circle->members()->attach($user, ['status' => Circle::STATUS_MEMBER]);
        }

        $client_id = config('auth.passport.client_id');

        $token = $this->getBearerTokenByUser($user, $client_id);

        $token_data = [
            'tokenType' => $token['token_type'],
            'accessToken' => $token['access_token'],
            'refreshToken' => $token['refresh_token'],
            'expiresIn' => $token['expires_in']
        ];

        return $token_data;
    }
}
