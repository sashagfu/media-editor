<?php

namespace App\Models;

class Report extends BaseModel
{
    const COPYRIGHT_REASON = 6;
    const OTHER_REASON = 10;

    public function reportable()
    {
        return $this->morphTo();
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
