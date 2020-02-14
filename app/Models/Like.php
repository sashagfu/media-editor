<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Like
 *
 * @property      int $id
 * @property      int $user_id
 * @property      int $likeable_id
 * @property      string $likeable_type
 * @property      \Carbon\Carbon $created_at
 * @property      \Carbon\Carbon $updated_at
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Like whereCreatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Like whereId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Like whereLikeableId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Like whereLikeableType($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Like whereUpdatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Like whereUserId($value)
 * @mixin         \Eloquent
 * @property-read \App\Models\User $user
 */

class Like extends BaseModel
{
    protected $appends = ['user_id', 'likeable_id', 'likeable_type'];

    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
    ];
    /**
     * Like belongs to user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
