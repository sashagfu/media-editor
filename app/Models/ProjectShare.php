<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectShare extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
    ];
}
