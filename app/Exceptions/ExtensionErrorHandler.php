<?php
namespace App\Exceptions;

use Closure;
use GraphQL\Error\Error;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Nuwave\Lighthouse\Execution\ExtensionErrorHandler as BaseHandler;

class ExtensionErrorHandler extends BaseHandler
{
    public static function handle(Error $error, Closure $next): array
    {
        $underlyingException = $error->getPrevious();
        $errorData = [];

        if ($underlyingException && $underlyingException instanceof AuthenticationException) {
            $errorData = [
                'errorCode' => 401,
                'message' => $error->message,
            ];
        } elseif ($underlyingException && $underlyingException instanceof ValidationException) {
            $errorData = [
                'errorCode' => 422,
                'message' => $error->message,
                'errors' => [
                    $underlyingException->errors(),
                ],
            ];
        } elseif ($underlyingException && $underlyingException instanceof QueryException &&
            app()->environment('production')) {
            $errorData = [
                'errorCode' => 500,
                'message' => 'Database error', // Mask SQL errors for production environments
            ];
        }

        if ($errorData) {
            // Reconstruct the error, passing in the extensions of the underlying exception
            $error = new Error(
                $error->message,
                $error->nodes,
                $error->getSource(),
                $error->getPositions(),
                $error->getPath(),
                $underlyingException,
                $errorData
            );

            return $next($error);
        }

        return parent::handle($error, $next);
    }
}
