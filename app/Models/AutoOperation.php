<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoOperation extends BaseModel
{
    const STATUS_ALWAYS_DECLINE = 0;

    const STATUS_ALWAYS_ACCEPT = 1;

    protected $fillable = [
        'initiator_id',
        'subject_id',
        'status'
    ];
}
