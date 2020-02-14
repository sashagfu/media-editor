<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Star
 *
 * @property      int $id
 * @property      int $user_id
 * @property      int $starable_id
 * @property      string $starable_type
 * @property      \Carbon\Carbon $created_at
 * @property      \Carbon\Carbon $updated_at
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Star whereCreatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Star whereId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Star whereStarableId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Star whereStarableType($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Star whereUpdatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Star whereUserId($value)
 * @mixin         \Eloquent
 * @property-read \App\Models\User $user
 */

class Star extends BaseModel
{
    const MORPH_TYPE = 'star';

    protected $appends = ['user_id', 'starable_id', 'starable_type'];

    protected $fillable = [
        'user_id',
        'starable_id',
        'starable_type',
    ];

    /**
     * Get all of the owning starable models.
     */
    public function starable()
    {
        return $this->morphTo('starable');
    }

    /**
     * Star belongs to user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
