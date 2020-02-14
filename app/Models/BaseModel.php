<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Foundation\Auth\User as Authenticatable;

class BaseModel extends Authenticatable
{

    /**
     * Boot function from laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(
            function ($model) {
                if (empty($model->uuid)) {
                    $model->uuid = Uuid::uuid4()->toString();
                }
            }
        );
    }


    // HELPERS

    /**
     * @see vendor/laravel/passport/src/Bridge/UserRepository.php:L42
     *
     * @param string $username
     *
     * @return User
     */
    public function findForPassport(string $username): User
    {
        return User::where('email', $username)->first();
    }
}
