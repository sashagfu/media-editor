<?php

namespace App\Http\GraphQL\Mutations;

use App\Models\User;
use App\Traits\PassportToken;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class LoginMutator
{
    use AuthenticatesUsers, PassportToken, ValidatesRequests;

    public function loginUser($root, $args, $context)
    {
        Validator::make($args, [
            'email'       => 'required|string|email|exists:users',
            'password'    => 'required|string',
            'rememberMe' => 'boolean',
        ])->validate();

        $credentials = ['email' => $args['email'], 'password' => $args['password']];

        if (!Auth::attempt($credentials)) {
            $messages = [
                'password' => 'Invalid password',
            ];
            throw ValidationException::withMessages($messages);
        }

        $user = User::where('email', $args['email'])->first();

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

    public function logoutUser()
    {
        Auth::logout();

        return [
            'message' => 'Unauthenticated',
            'statusCode' => 200
        ];
    }

    public function authenticated(Request $request, $user)
    {
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

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'variables.' . $this->username() => 'required|string',
            'variables.password' => 'required|string',
        ]);
    }

    protected function credentials(Request $request)
    {
        return collect($request->variables)->only($this->username(), 'password')->toArray();
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }
}
