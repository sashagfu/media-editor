<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SearchResult
 *
 * @property int $id
 * @property string $search_term
 * @property int $total_search
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\SearchResult whereCreatedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\SearchResult whereId($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\SearchResult whereSearchTerm($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\SearchResult whereTotalSearch($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\SearchResult whereUpdatedAt($value)
 * @mixin    \Eloquent
 */

class SearchResult extends Model
{
    //
}
