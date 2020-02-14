<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Invite
 *
 * @property string $id
 * @property string $email
 * @property int $user_id
 * @property int $circle_id
 * @property bool $active
 * @property bool $used
 * @property string $expiration
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Invite whereActive($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Invite whereCircleId($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Invite whereCreatedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Invite whereEmail($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Invite whereExpiration($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Invite whereId($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Invite whereUpdatedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Invite whereUsed($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Invite whereUserId($value)
 * @mixin    \Eloquent
 */

class Invite extends Model
{
    public $incrementing = false;

    const EXPIRATION_DAYS = 7;
    //
}
