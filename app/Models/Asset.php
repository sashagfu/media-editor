<?php

namespace App\Models;

use App\Models\Helpers\S3File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends BaseModel
{
    use SoftDeletes, S3File;

    const MORPH_TYPE = 'asset';

    const VIDEO_TYPE = 'VIDEO';

    const AUDIO_TYPE = 'AUDIO';

    const FULL_TYPE = 'FULL';

    const VIDEO_EXT = 'mp4';

    const AUDIO_EXT = 'mp3';

    const SPRITE_NAME = 'sprite.png';

    protected $assets_path;

    protected $storage_base;

    protected $fillable = [
        'project_id',
        'type',
        'version',
        'file_path',
        'time',
        'frames',
        'width',
        'height',
    ];

    protected $appends = [
        'fileType',
        'thumbPath',
        'spritePath',
        'waveformData',
        's3_path',
        'enableToSave',
        'credits',
    ];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->assets_path = config('filesystems.disks.s3.assets_path');
        $this->storage_base = config('filesystems.disks.s3.storage_base');
    }

    public function project()
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'saved_assets');
    }

    public function getThumbPathAttribute()
    {
        $project = $this->project;

        $projectPath = $this->storage_base . $project->path;

        return $projectPath . $this->assets_path . $this->version . '/' . 'thumb.jpg';
    }

    public function getWaveformDataAttribute()
    {
        $project = $this->project;

        $projectPath = $this->storage_base . $project->path;

        return $projectPath . $this->assets_path . $this->version . '/' . 'waveform.json';
    }

    public function getSpritePathAttribute()
    {
        $project = $this->project;

        $projectPath = $this->storage_base . $project->path;

        return $projectPath . $this->assets_path . $this->version . '/' . self::SPRITE_NAME;
    }

    public function getFileTypeAttribute()
    {
        return self::MORPH_TYPE;
    }

    public function getEnableToSaveAttribute()
    {
        if ($this->type == Asset::FULL_TYPE) {
            return $this->project->usedAssetTypesCount(Asset::VIDEO_TYPE) == 0 &&
                   $this->project->usedAssetTypesCount(Asset::AUDIO_TYPE) == 0 &&
                   $this->project->usedAssetTypesCount(Asset::FULL_TYPE) == 0;
        }

        if ($this->type == Asset::VIDEO_TYPE) {
            return $this->project->usedAssetTypesCount(Asset::VIDEO_TYPE) == 0 &&
                   $this->project->usedAssetTypesCount(Asset::FULL_TYPE) == 0;
        }

        if ($this->type == Asset::AUDIO_TYPE) {
            return $this->project->usedAssetTypesCount(Asset::AUDIO_TYPE) == 0 &&
                   $this->project->usedAssetTypesCount(Asset::FULL_TYPE) == 0;
        }

        return null;
    }

    public function getCreditsAttribute()
    {
        $credits = [];

        foreach ($this->project->usedAssets as $asset) {
            $credits[] = $this->project->author;
        }

        return $credits;
    }

    public function clips()
    {
        return $this->belongsToMany(User::class, 'saved_assets');
    }
}
