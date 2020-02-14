<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Flag
 *
 * @property      int $id
 * @property      int $flaggable_id
 * @property      string $flaggable_type
 * @property      string $description
 * @property      bool $is_verified
 * @property      int $author_id
 * @property      \Carbon\Carbon $created_at
 * @property      \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $author
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $flaggable
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Flag whereAuthorId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Flag whereCreatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Flag whereDescription($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Flag whereFlaggableId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Flag whereFlaggableType($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Flag whereId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Flag whereIsVerified($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Flag whereUpdatedAt($value)
 * @mixin         \Eloquent
 */

class Flag extends BaseModel
{
    public $fillable = ['description'];

    public function flaggable()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
