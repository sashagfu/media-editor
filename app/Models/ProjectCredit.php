<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCredit extends BaseModel
{
    protected $casts = [
        'details' => 'json',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
