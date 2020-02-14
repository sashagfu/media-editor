<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *  ProjectProcess model. Contain information about attempts of Project rendering
 *
 * @property int $id
 * @property int $project_id
 * @property int|null $job_id
 * @property int $status
 * @property array|null $outputs
 * @property string $status_name
 */
class ProjectProcess extends BaseModel
{
    const STATUS_WAITING = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_COMPLETE = 2;
    const STATUS_ERROR = 3;
    const STATUS_CANCELED = 4;

    protected $fillable = [
        'project_id',
        'job_id',
        'outputs',
        'status',
    ];

    protected $casts = [
        'outputs' => 'json',
    ];

    public static $statuses = [
        self::STATUS_WAITING => 'Waiting',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_COMPLETE => 'Complete',
        self::STATUS_ERROR => 'Error',
        self::STATUS_CANCELED => 'Canceled',
    ];

    // RELATIONSHIPS

    /**
     *  ProjectProcess belongs to Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // SCOPES

    // ACCESSORS

    /**
     *  Get status name
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return self::$statuses[$this->status];
    }
}
