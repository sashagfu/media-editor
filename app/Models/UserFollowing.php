<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserFollowing
 *
 * @property int $id
 * @property int $user_id
 * @property int $follower_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\UserFollowing whereCreatedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\UserFollowing whereFollowerId($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\UserFollowing whereId($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\UserFollowing whereUpdatedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\UserFollowing whereUserId($value)
 * @mixin    \Eloquent
 */

class UserFollowing extends Model
{
    //
}
