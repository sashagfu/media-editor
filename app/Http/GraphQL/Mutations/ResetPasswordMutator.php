<?php

namespace App\Http\GraphQL\Mutations;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ResetPasswordMutator
{
    use SendsPasswordResetEmails;

    public function sendResetPassword($root, $args)
    {
        $response = $this->broker()->sendResetLink(
            [
                'email' =>  $args['email']
            ]
        );

        if ($response == Password::RESET_LINK_SENT) {
            return response()->json([
                'message'    => trans('auth.reset_pass_check_email'),
                'statusCode' => 200
            ]);
        }

        throw new \Exception(trans('auth.failed'));
    }

    public function reset($root, $args, $context)
    {
        Validator::make(
            $args,
            [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,15}$/',
            ]
        )->validate();

        $credentials = [
            'email' => $args['email'],
            'password' => $args['password'],
            'password_confirmation' => $args['passwordConfirmation'],
            'token' => $args['token']
        ];

        $response = $this->broker()->reset(
            $credentials,
            function (
                $user,
                $password
            ) {
                $this->resetPassword($user, $password);
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return response()->json([
                'message'    => trans('auth.reset_pass_success'),
                'statusCode' => 200
            ]);
        }

        throw new \Exception(trans('common.error_reload'));
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
}
