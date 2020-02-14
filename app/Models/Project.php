<?php

namespace App\Models;

use Codeception\Lib\Console\Output;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use App\Models\ProjectCredit;

/**
 * App\Models\Project
 *
 * @property      int $id
 * @property      int $author_id
 * @property      string $path
 * @property      string $title
 * @property      string $status
 * @property      array $layers
 * @property      float $length
 * @property      \Carbon\Carbon|null $created_at
 * @property      \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Audio[] $projectAudio
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $projectImages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $projectVideos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProjectInput[] $inputs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProjectProcess[] $processes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Text[] $texts
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereAuthorId($value)
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereCreatedAt($value)
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereId($value)
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereUpdatedAt($value)
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereValue($value)
 * @mixin         \Eloquent
 */
class Project extends BaseModel
{
    use SoftDeletes;

    const MORPH_TYPE = 'project';

    const DEFAULT_PROJECT_NAME = 'My Project';

    const DEFAULT_STATUS = 'New';

    const STATUS_DRAFT = 1;

    const STATUS_PROCESSING = 2;

    const STATUS_PUBLISHED = 3;

    const STATUS_FAILED = 4;

    const STATUS_RENDERED = 5;

    protected $hidden = ['author'];

    protected $fillable = [
        'value',
        'author_id',
        'layers',
        'title',
        'description',
        'version',
        'last_rendered_version',
        'status',
        'progress',
        'thumb_path',
    ];

    protected $casts = [
        'value' => 'json',
        'layers' => 'json',
    ];

    protected $attributes = [
        'layers' => '[{"id": 1, "volume": 0.8, "height": 1.0}, {"id": 2, "volume": 0.8, "height": 1.0}, {"id": 3, "volume": 0.8, "height": 1.0}]', // @codingStandardsIgnoreEnd
    ];

    protected $appends = [
        'isDraft',
        'isPublished',
        'isRendered',
        'isFailed',
        'isProcessing',
        'isPerformance',
        'userReaction',
        'authorId',
        'createdAt',
        'updatedAt',
        'thumbTime',
        'thumbUrl',
        'clipsCount',
        'viewsCount',
        'foreignCredits',
        'duration',
    ];

    // RELATIONS

    /**
     *  Project author
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get All project related images
     *
     * @return mixed
     */
    public function projectImages()
    {
        return $this->morphMany(Image::class, 'imageable')->where('album_id', Image::MEDIA_EDITOR_IMAGES);
    }

    /**
     * Get All project related audio
     *
     * @return mixed
     */
    public function projectAudio()
    {
        return $this->morphMany(Audio::class, 'audioable');
    }

    /**
     * Get All project related video
     *
     * @return mixed
     */
    public function projectVideos()
    {
        return $this->morphMany(Video::class, 'videoable');
    }

    public function texts()
    {
        return $this->hasMany(Text::class);
    }

    public function slides()
    {
        return $this->hasMany(Slide::class);
    }

    public function thumb()
    {
        return $this->belongsTo(Image::class, 'thumb_id');
    }

    /**
     *  Inputs relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inputs()
    {
        return $this->hasMany(ProjectInput::class);
    }

    /**
     *  Inputs relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function videoInputs()
    {
        return $this->hasMany(ProjectInput::class)->where('type', Video::MORPH_TYPE);
    }

    /**
     *  Inputs relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function imageInputs()
    {
        return $this->hasMany(ProjectInput::class)->where('type', Image::MORPH_TYPE);
    }

    /**
     *  Inputs relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function audioInputs()
    {
        return $this->hasMany(ProjectInput::class)->where('type', Audio::MORPH_TYPE);
    }

    /**
     *  Inputs relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function textInputs()
    {
        return $this->hasMany(ProjectInput::class)
                    ->where('type', Text::MORPH_TYPE);
    }

    public function slideInputs()
    {
        return $this->hasMany(ProjectInput::class)
                    ->where('type', Slide::MORPH_TYPE);
    }

    /**
     *  Project rendering processes
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function processes()
    {
        return $this->hasMany(ProjectProcess::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function usedAssets()
    {
        return $this->belongsToMany(Asset::class, 'asset_project', 'project_id', 'asset_id');
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class);
    }

    /**
     *  Inputs relation
     *
     */
    public function credits()
    {
        return $this->hasMany(ProjectCredit::class);
    }

    public function shares()
    {
        return $this->hasMany(ProjectShare::class);
    }

    public function views()
    {
        return $this->hasMany(ProjectView::class);
    }

    // ACCESSORS

    public function getAuthorIdAttribute()
    {
        return $this->getOriginal('author_id');
    }

    public function getCreatedAtAttribute()
    {
        return $this->getOriginal('created_at');
    }

    public function getUpdatedAtAttribute()
    {
        return $this->getOriginal('updated_at');
    }

    public function getThumbUrlAttribute()
    {
        return $this->thumb ? $this->thumb->file_path : null;
    }

    public function getThumbTimeAttribute()
    {
        return $this->getOriginal('thumb_time');
    }

    /**
     *  Path to project files
     *
     * @return string
     */
    public function getPathAttribute()
    {
        return "users/$this->author_id/projects/$this->id/";
    }

    /**
     *  Project length
     * @return float
     */
    public function getLengthAttribute()
    {
        /** @var \App\Models\ProjectInput|null $the_last_input Input which is at the end */
        $the_last_input = $this->inputs()
            ->orderBy(\DB::raw('(position + length)'), 'desc')
            ->first();

        return $the_last_input ? $the_last_input->end_point : 0.0;
    }

    public function getLayersAttribute()
    {
        if (empty($this->getOriginal('layers')) || $this->getOriginal('layers') == 'null') {
            return [
                [
                    'id' => 0,
                    'volume' => .8,
                    'height' => 1,
                ], [
                    'id' => 1,
                    'volume' => .8,
                    'height' => 1,
                ], [
                    'id' => 2,
                    'volume' => .8,
                    'height' => 1,
                ],
            ];
        }
        return json_decode($this->getOriginal('layers'), true);
    }

    public function getIsDraftAttribute()
    {
        return $this->status == self::STATUS_DRAFT;
    }

    public function getIsProcessingAttribute()
    {
        return $this->status == self::STATUS_PROCESSING;
    }

    public function getIsPublishedAttribute()
    {
        return $this->status == self::STATUS_PUBLISHED;
    }

    public function getIsRenderedAttribute()
    {
        return $this->status == self::STATUS_RENDERED;
    }

    public function getIsFailedAttribute()
    {
        return $this->status == self::STATUS_FAILED;
    }

    public function getClipsCountAttribute()
    {
        return $this->assets()
            ->where('version', $this->version)
            ->get()
            ->sum(function ($asset) {
                return $asset->clips->count();
            });
    }

    public function getForeignCreditsAttribute()
    {
        $json_string = "%\"project\": {\"id\": {$this->id}%";
        $credits = ProjectCredit::where('details', 'like', $json_string)
                ->with('project')
                ->get();

        return $credits;
    }

    public function getViewsCountAttribute()
    {
        return $this->views()->sum('views_count');
    }

    // Helpers

    /**
     *  Check if project has image inputs
     * @return bool
     */
    public function hasImages()
    {
        return !! $this->inputs()
            ->where('type', Image::MORPH_TYPE)
            ->count();
    }

    /**
     *  Check if project has audio inputs
     * @return bool
     */
    public function hasAudios()
    {
        return !! $this->inputs()
            ->where('type', Audio::MORPH_TYPE)
            ->count();
    }

    /**
     *  Get layers IDs which have no sound
     * @return array Layers IDs
     */
    public function getSilentLayers()
    {
        return collect($this->layers)
            ->filter(function ($layer) {
                return $layer['volume'] <= 0;
            })
            ->pluck('id');
    }

    public function draw()
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput;

        $this->inputs()
            ->orderBy('layer_id', 'desc')
            ->orderBy('position')
            ->get()
            ->groupBy('layer_id')
            ->each(function ($layer) use ($output) {
                /** @var \Illuminate\Support\Collection $layer */
                $output->write(PHP_EOL);
                $layer->reduce(function ($position, ProjectInput $input) use ($output) {

                    if (!$position) {
                        $output->write(str_repeat('-', $input->position));
                        $position = $input->position;
                    } else {
                        $output->write(str_repeat('-', ($input->position - $position)));
                        $position += $input->position - $position;
                    }
                    $output->write(str_repeat('=', $input->length));

                    return (float)$position + $input->length ;
                });
            });

        $output->writeln(PHP_EOL);

        return $this->title;
    }

    public function stars()
    {
        return $this->morphToMany(User::class, 'starable', 'stars');
    }

    public function likes()
    {
        return $this->morphToMany(User::class, 'likeable', 'likes');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getUserReactionAttribute()
    {
        if ($this->isPerformance) {
            return $this->isStarredByUser();
        } else {
            return $this->isLikedByUser();
        }
    }

    /**
     * Return whether post is liked by a user
     *
     * @return bool
     */
    public function isLikedByUser()
    {
        return Auth::user() ?  (bool) $this->likes->contains(Auth::user()->id) : false;
    }

    /**
     * Return whether post is starred by a user
     *
     * @return bool
     */
    public function isStarredByUser()
    {
        return Auth::user() ?  (bool) $this->stars->contains(Auth::user()->id) : false;
    }

    public function getIsPerformanceAttribute()
    {
        return true;
    }

    public function usedAssetTypesCount($type)
    {
        return $this->usedAssets()
            ->where('type', $type)
            ->get()
            ->count();
    }

    public function getDurationAttribute()
    {
        if (!count($this->inputs)) {
            return 0;
        }

        $last_input = $this->inputs->sortByDesc(function ($input) {
            return $input->position * 1000 + $input->length;
        })
            ->first();
        return $last_input->position * 1000 + $last_input->length * 1000;
    }
}
