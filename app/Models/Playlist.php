<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Playlist extends BaseModel
{
    use SoftDeletes;

    const ACCESS_LEVEL_PUBLIC = 1;

    const ACCESS_LEVEL_PRIVATE = 2;

    const ACCESS_LEVEL_LINK = 3;

    protected $fillable = [
        'name',
        'author_id',
        'access_level'
    ];

    protected $attributes = [
        'access_level' => self::ACCESS_LEVEL_PUBLIC
    ];

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
