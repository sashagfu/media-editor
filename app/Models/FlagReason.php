<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FlagReason
 *
 * @property int $id
 * @property string $title
 * @property bool $enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\FlagReason enabled()
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\FlagReason whereCreatedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\FlagReason whereEnabled($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\FlagReason whereId($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\FlagReason whereTitle($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\FlagReason whereUpdatedAt($value)
 * @mixin    \Eloquent
 */

class FlagReason extends BaseModel
{
    protected $fillable = ['title', 'enabled'];

    public $casts = [
        'enabled' => 'boolean',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }
}
