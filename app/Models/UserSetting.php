<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserSetting
 *
 * @property      int $id
 * @property      int $user_id
 * @property      string $name
 * @property      string $value
 * @property      \Carbon\Carbon $created_at
 * @property      \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $user
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\UserSetting whereCreatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\UserSetting whereId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\UserSetting whereName($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\UserSetting whereUpdatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\UserSetting whereUserId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\UserSetting whereValue($value)
 * @mixin         \Eloquent
 */

class UserSetting extends Model
{
    protected $fillable = ['name', 'value'];

    protected $casts = [
        'value' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
