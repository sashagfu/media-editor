<?php

namespace App\Models;

use App\Helpers\FileHelper;
use App\Models\Helpers\S3File;
use FFMpeg\Format\Video\X264;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Psy\Exception\ErrorException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;

/**
 * Class ProjectInput
 *
 * @property int $id
 * @property int $project_id
 * @property int $object_id
 * @property int $file_id
 * @property int $layer_id
 * @property float $position
 * @property float $end_point
 * @property float $start_from
 * @property float $length
 * @property array $transform
 * @property string $type
 * @property string $content
 * @property string|null $converted_file
 * @property-read string $name Input file name without
 * @property-read \App\Models\Project $project
 *
 * @property-read S3File $object
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio visible()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio withSound()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio typeAudio()
 */

class ProjectInput extends BaseModel
{

    /**
     * @var string Table name
     */
    protected $table = 'project_inputs';

    /**
     * @var bool Timestamps enable/disable trigger
     */
    public $timestamps = false;

    const DEFAULT_EFFECTS = [
        'fadeIn' => [
            'active' => false,
            'duration' => 0,
        ],
        'fadeOut' => [
            'active' => false,
            'duration' => 0,
        ],
    ];

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'uuid',
        'project_id',
        'object_id',
        'type',
        'unlinked',
        'converted_file',
        'layer_id',
        'position',
        'start_from',
        'length',
        'transform',
        'volume_levels',
        'effects',
    ];

    /**
     * @var array Convert variables into format
     */
    protected $casts = [
        'transform' => 'json',
        'volume_levels' => 'array',
        'effects' => 'json',
    ];

    // RELATIONS

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function file()
    {
        return $this->object()
            ->whereIn('type', [
                Audio::MORPH_TYPE,
                Image::MORPH_TYPE,
                Video::MORPH_TYPE,
                Asset::MORPH_TYPE,
            ]);
    }

    public function object()
    {
        return $this->morphTo('object', 'type', 'object_id');
    }

    // SCOPES

    /**
     *  Get all projects inputs which are not under the other
     *
     *  Example: There are two inputs
     *  _______________________________________________________________
     *  |                                                              |
     *  | L0:INPUT_0 - - - || position:3 = = = = = = length:6 || - - - |
     *  |                                                              |
     *  | L0:INPUT_1 - - - - || position:4 = = length:2 || - - - - - - |
     *  |______________________________________________________________|
     *
     *  !! The 'INPUT_1' is under 'INPUT_0', 'INPUT_0' covers 'INPUT_1' completely,
     *  !! therefore 'INPUT_1' should by filtered
     *
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder|\App\Models\Audio
     */
    public function scopeVisible($query)
    {
        return $query
            ->where('type', '!=', Audio::MORPH_TYPE)
            ->whereNotExists(function ($sql) {
                $sql->select(\DB::raw(1))
                    ->from($this->table . ' as pi')
                    ->whereRaw("pi.id != $this->table.id")
                    ->whereRaw("pi.layer_id >= $this->table.layer_id")
                    ->whereRaw("pi.project_id = $this->table.project_id")
                    ->whereRaw("type != '" . Audio::MORPH_TYPE . "'")
                    ->where(function ($sql) {
                        $sql->whereRaw("pi.position <= $this->table.position")
                            ->whereRaw("(pi.position + pi.length) >= ($this->table.position +$this->table.length)");
                    })
                    ->limit(1);
            });
    }

    /**
     *  Filter inputs which play sound in project
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeWithSound($query)
    {
        return $query->whereIn('type', [Video::MORPH_TYPE, Audio::MORPH_TYPE]);
    }

    /**
     *  Select audio inputs
     *
     * @param $query
     * @return mixed
     */
    public function scopeTypeAudio($query)
    {
        return $query->where('type', Audio::MORPH_TYPE);
    }

    // ACCESSORS

    public function setTransformAttribute($transform)
    {
        $this->attributes['transform'] = json_encode($transform);
    }

    /**
     * @return mixed|Model
     */
    public function getFileAttribute()
    {
        return $this->object()->first();
    }

    /**
     *  Point on the project time line in that input ends
     *
     * @return float
     */
    public function getEndPointAttribute()
    {
        return $this->position + $this->length;
    }

    /**
     *  Get file name without ext
     *
     * @return string
     */
    public function getNameAttribute()
    {
        /** @var string $file_name File name with ext */
        $file_name = $this->object->name;

        if (strpos($file_name, '.')) {

            /** @var array $arr */
            $arr = explode('.', $file_name);

            return implode('.', array_slice($arr, 0, count($arr)-1));
        } else {
            return $file_name;
        }
    }

    // HELPERS

    public function getObjectData() : array
    {
        if ($this->object instanceof Text) {
            return $this->object->makeHidden(['project_id'])->toArray();
        }
        return [
            'id' => $this->object_id,
            'type' => $this->type,
            'name' => $this->object->name,
            'time' => $this->object->time,
            'thumb' => $this->object->thumbnail_path,
            'width' => $this->object->width,
            'frames' => $this->object->frames,
            'height' => $this->object->height,
            'sprite' => $this->object->sprite_path,
            'filePath' => $this->object->file_path,
            'fileType' => $this->type,
            'waveformData' => $this->object->waveform,
        ];
    }

    /**
     *  Determine whether input is split/crossed by others inputs
     *
     *  Example:
     *  _______________________________________________________________
     *  |                                                              |
     *  | L2:INPUT_0 - - - - - - - - - - - - - - - || p.:5 = l.:1 || - |
     *  |                                                              |
     *  | L1:INPUT_1 - - - - - || position:5 = = length:2 || - - - - - |
     *  |                                                              |
     *  | L0:INPUT_2 - - - || position:3 = = = = = = length:6 || - - - |
     *  |______________________________________________________________|
     *
     *  !! Overlaps called inputs which are above and has duration in common with current
     *  !! Overlaps cut/split inputs which are under them
     *  !! Input can be overlap for other inputs on the same layer if it's endpoint is grater than start of other
     *
     * @return bool
     * @deprecated
     */
    public function hasOverlaps()
    {
        return !! self::query()
            ->select(\DB::raw(1))
            ->where("project_id", $this->project_id)
            ->where("id", "!=", $this->id)
            ->where('type', '!=', Audio::MORPH_TYPE)
            ->where(function ($sql) {
                $sql->where(function ($sql) {
                        $sql->where("layer_id", ">=", $this->layer_id)
                            ->whereRaw("position >= $this->position")                               // Inside or has
                            ->whereRaw("(position + length) <= ($this->position + $this->length)"); // the same points
                })
                    ->orWhere(function ($sql) {
                        $sql->where("layer_id", ">", $this->layer_id)
                            ->whereRaw("position <= $this->position")                               // at the beginning
                            ->whereRaw("(position + length) > $this->position")
                            ->whereRaw("(position + length) < ($this->position + $this->length)");
                    })
                    ->orWhere(function ($sql) {
                        $sql->where("layer_id", $this->layer_id)
                            ->where('position', '<', $this->position)
                            ->whereRaw("(position + length) > $this->position")
                            ->whereRaw("(position + length) < ($this->position + $this->length)");
                    })
                    ->orWhere(function ($sql) {
                        $sql->where("layer_id", ">", $this->layer_id)
                            ->whereRaw("position < ($this->position + $this->length)")              // at the end
                            ->whereRaw("(position + length) >= ($this->position + $this->length)");
                    });
            })
            ->count();
    }

    /**
     *  Determine whether input is split by others inputs
     *
     *  Example:
     *  _______________________________________________________________
     *  |                                                              |
     *  | L1:INPUT_0 - - - - - || position:5 = = length:2 || - - - - - |
     *  |                                                              |
     *  | L0:INPUT_1 - - - || position:3 = = = = = = length:6 || - - - |
     *  |______________________________________________________________|
     *
     *  !! INPUT_1 is under INPUT_0 and has starts earlier and finishes later
     *  !! But INPUT_0 is on layer 1 weather INPUT_1 is on layer 0,
     *  !! INPUT_0 is more important therefore INPUT_1 should be split
     *
     * @return bool
     * @deprecated
     */
    public function isSplit()
    {
        return !! self::query()
            ->select(\DB::raw(1))
            ->whereRaw("project_id = $this->project_id")
            ->whereRaw("layer_id >$this->layer_id")
            ->where(function ($sql) {
                $sql->whereRaw("position > $this->position")
                    ->whereRaw("(position + length) < ($this->position + $this->length)");
            })
            ->limit(1)
            ->count();
    }

    /**
     *  Get splitters (the biggest)
     *
     *  !! Splitter is project input that is above and inside current project input
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     * @deprecated
     */
    public function getSplitters()
    {
        return self::whereRaw("project_id = $this->project_id")
            ->whereRaw("layer_id > $this->layer_id")
            ->where(function ($sql) {
                $sql->whereRaw("position > $this->position")
                    ->whereRaw("(position + length) < ($this->position + $this->length)");
            })
            ->whereNotExists(function ($sql) {
                $sql->select(\DB::raw(1))
                    ->from($this->table . ' as pi')
                    ->whereRaw("pi.id != $this->table.id")
                    ->whereRaw("pi.project_id = $this->table.project_id")
                    ->whereRaw("pi.layer_id  > $this->layer_id")
                    ->whereRaw("pi.layer_id  > $this->layer_id")->where(function ($sql) {
                        $sql->whereRaw("pi.position > $this->position")
                            ->whereRaw("(pi.position + pi.length) < ($this->position + $this->length)");
                    })
                    ->where(function ($sql) {
                        $sql->where(function ($sql) {
                            $sql->whereRaw("pi.position < $this->table.position")
                                ->whereRaw("(pi.position + pi.length) > ($this->table.position + $this->table.length)");
                        })
                        ->orWhere(function ($sql) {
                            $sql->whereRaw("pi.position < $this->table.position")
                                ->whereRaw("(pi.position + pi.length) >= ($this->table.position +$this->table.length)");
                        })
                        ->orWhere(function ($sql) {
                            $sql->whereRaw("pi.position <= $this->table.position")
                                ->whereRaw("(pi.position + pi.length) > ($this->table.position + $this->table.length)");
                        });
                    });
            })
            ->orderBy('position')
            ->get();
    }

    /**
     *  Get the biggest overlaps (which cuts grater length from input)
     *
     *  ______________________________________________________________
     *  |                                                             |
     *  | L2: - - - - - - || = Inp_4 = || - - - - - - - - - - - - - - |
     *  |                                                             |
     *  | L1: - - - - || = = Inp_2 = = || - - || = Inp_3 = || - - - - |
     *  |                                                             |
     *  | L0: - - - || = = = = = = = = Inp_1 = = = = = = = = || - - - |
     *  |_____________________________________________________________|
     *
     *  !! Overlaps called inputs which are above and has duration in common with current
     *  !! Overlaps cut/split inputs which are under them
     *  !! Input can be overlap for other inputs on the same layer if it's endpoint is grater than start of other
     *
     *  !! Inp_2 and Inp_3 are the biggest overlaps in example above
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @deprecated
     */
    public function getOverlaps()
    {
        return self::visible()
            ->whereRaw("project_id = $this->project_id")
            ->where('id', '!=', $this->id)
            ->where(function ($sql) {
                $sql->where(function ($sql) {
                    // at the beginning
                    $sql->where('layer_id', '>=', $this->layer_id)
                        ->where('position', '<', $this->position)
                        ->where(function ($sql) {
                            $sql->whereRaw("(position + length) > $this->position")
                                ->whereRaw("(position + length) <= ($this->position + $this->length)");
                        })
                        ->whereNotExists(function ($sql) {
                            $sql->select(\DB::raw(1))
                                ->from("$this->table as pi")
                                ->where('pi.project_id', $this->project_id)
                                ->whereRaw("pi.type !='" . Audio::MORPH_TYPE . "'")
                                ->where('pi.layer_id', '>=', $this->layer_id)
                                ->where('pi.id', "!=", $this->id)
                                ->whereRaw("pi.id != $this->table.id")
                                ->where('position', '<', $this->position)
                                ->whereRaw("(pi.position + pi.length) > $this->position")
                                ->whereRaw("(pi.position + pi.length) <= ($this->position + $this->length)")
                                ->whereRaw("(pi.position + pi.length) > ($this->table.position + $this->table.length)");
                        });
                })
                ->orWhere(function ($sql) {
                     // Inside the input
                    $sql->whereRaw("layer_id >= $this->layer_id")
                        ->where(function ($sql) {
                            $sql->whereRaw("position > $this->position")
                                ->whereRaw("(position + length) < ($this->position + $this->length)");
                        })
                        ->whereNotExists(function ($sql) {
                            $sql->select(\DB::raw(1))
                                ->from($this->table . ' as pi')
                                ->whereRaw("pi.id != $this->id")
                                ->whereRaw("pi.id != $this->table.id")
                                ->whereRaw("pi.project_id = $this->table.project_id")
                                ->whereRaw("pi.type !='" . Audio::MORPH_TYPE . "'")
                                ->whereRaw("pi.layer_id  > $this->layer_id")->where(function ($sql) {
                                    $sql->whereRaw("pi.position > $this->position")
                                        ->whereRaw("(pi.position + pi.length) < ($this->position + $this->length)");
                                })
                                ->where(function ($sql) {
                                    $sql->where(function ($sql) {
                                            $sql->whereRaw("pi.position < $this->table.position")
                                                ->whereRaw("(pi.position + pi.length) > ($this->table.position + $this->table.length)"); // @codingStandardsIgnoreEnd
                                    })
                                        ->orWhere(function ($sql) {
                                            $sql->whereRaw("pi.position < $this->table.position")
                                                ->whereRaw("(pi.position + pi.length) >= ($this->table.position + $this->table.length)"); // @codingStandardsIgnoreEnd
                                        })
                                        ->orWhere(function ($sql) {
                                            $sql->whereRaw("pi.position <= $this->table.position")
                                                ->whereRaw("(pi.position + pi.length) > ($this->table.position + $this->table.length)"); // @codingStandardsIgnoreEnd
                                        });
                                });
                        });
                })
                ->orWhere(function ($sql) {
                    $sql->where('layer_id', '>', $this->layer_id)
                        ->whereRaw("position > $this->position")
                        ->whereRaw("position < ($this->position + $this->length)")
                        ->whereRaw("(position + length) >= ($this->position + $this->length)")
                        ->whereNotExists(function ($sql) {
                            $sql->select(\DB::raw(1))
                                ->from("$this->table as pi")
                                ->where('pi.id', "!=", $this->id)
                                ->whereRaw("pi.id != $this->table.id")
                                ->where('pi.project_id', $this->project_id)
                                ->whereRaw("pi.type !='" . Audio::MORPH_TYPE . "'")
                                ->where('pi.layer_id', '>=', $this->layer_id)
                                ->whereRaw("pi.position > $this->position")
                                ->whereRaw("pi.position < ($this->position + $this->length)")
                                ->whereRaw("(pi.position + pi.length) >= ($this->position + $this->length)")
                                ->whereRaw("pi.position < $this->table.position");
                        });
                });
            })
            ->orderBy('position')
            ->get();
    }

    /**
     *  Check if input is image
     *
     * @return bool
     */
    public function isImage()
    {
        return $this->type == Image::MORPH_TYPE;
    }

    /**
     *  Check if input is video
     *
     * @return bool
     */
    public function isVideo()
    {
        if ($this->type == Asset::MORPH_TYPE) {
            return $this->object->type == Asset::FULL_TYPE || $this->object->type == Asset::VIDEO_TYPE;
        }
        return $this->type == Video::MORPH_TYPE;
    }

    /**
     *  Check if input is audio
     *
     * @return bool
     */
    public function isAudio()
    {
        if ($this->type == Asset::MORPH_TYPE) {
            return $this->object->type == Asset::AUDIO_TYPE;
        }
        return $this->type == Audio::MORPH_TYPE;
    }

    /**
     *  Check if input is text
     *
     * @return bool
     */
    public function isText()
    {
        return $this->type == 'text';
    }

    /**
     *  Check if input is slide
     *
     * @return bool
     */
    public function isSlide()
    {
        return $this->type == 'slide';
    }


    /**
     *  Inputs in Elastic Transcoder format (with position for sorting)
     *
     *  !! If input is split/cut by others it will be taken into result
     *  !! Result is always collection that contain one input or more if input is split
     *
     * @return \Illuminate\Support\Collection
     * @deprecated
     */
    public function getInputEntries()
    {
        /** @var \Illuminate\Support\Collection $inputs */
        $inputs = collect();

        if ($this->hasOverlaps()) {

            /** @var \Illuminate\Database\Eloquent\Collection $overlaps */
            $overlaps = $this->getOverlaps();

            /**
             * @var int $index
             * @var \App\Models\ProjectInput $overlap
             */
            foreach ($overlaps as $index => $overlap) {
                // Start
                if ($overlap->position <= $this->position && $this->position < $overlap->end_point) {
                    $this->length = $this->length - ($overlap->end_point - $this->position);
                    $this->start_from = $this->start_from + ($overlap->end_point - $this->position);
                    $this->position = $overlap->end_point;

                    // End
                } elseif ($overlap->position < $this->end_point && $this->end_point < $overlap->end_point) {
                    $this->length = $this->length - ($this->end_point - $overlap->position);

                    // Middle
                } elseif ($overlap->position > $this->position && $overlap->end_point < $this->end_point) {
                    $duration = $overlap->position - $this->position;

                    if ($this->isImage()) {
                        /** @var \Illuminate\Support\Collection $image_inputs */
                        $image_inputs = $this->loop($this->position, $duration, 0.0, self::IMAGE_VIDEO_LENGTH);
                        $inputs = $inputs->merge($image_inputs);
                    } else {
                        $inputs->push([
                            'Key' => $this->object->s3_path,
                            'position' => $this->position,
                            'TimeSpan' => [
                                'StartTime' => $this->start_from,
                                'Duration' => $duration
                            ]
                        ]);
                    }

                    $this->position = $overlap->end_point;
                    $this->start_from = $this->start_from + $duration + $overlap->length;
                    $this->length = $this->length - ($duration + $overlap->length);
                }

                if (!isset($overlaps[$index + 1])) {
                    if ($this->isImage()) {
                        /** @var \Illuminate\Support\Collection $image_inputs */
                        $image_inputs = $this->loop($this->position, $this->length, 0.0, self::IMAGE_VIDEO_LENGTH);
                        $inputs = $inputs->merge($image_inputs);
                    } else {
                        $inputs->push([
                            'Key' => $this->object->s3_path,
                            'position' => $this->position,
                            'TimeSpan' => [
                                'StartTime' => $this->start_from,
                                'Duration' => $this->length
                            ]
                        ]);
                    }
                }
            }
        } else {
            if ($this->isImage()) {
                /** @var \Illuminate\Support\Collection $image_inputs */
                $image_inputs = $this->loop($this->position, $this->length, 0.0, self::IMAGE_VIDEO_LENGTH);
                $inputs = $inputs->merge($image_inputs);
            } else {
                $inputs->push([
                    'Key' => $this->object->s3_path,
                    'position' => $this->position,
                    'TimeSpan' => [
                        'StartTime' => $this->start_from,
                        'Duration' => $this->length
                    ]
                ]);
            }
        }

        return $inputs;
    }
}
