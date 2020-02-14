<?php

namespace App\Models;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Helpers\S3File;
use Illuminate\Database\Eloquent\Model;
use Monolog\Handler\CouchDBHandler;
use Symfony\Component\Process\Process;

/**
 * App\Models\Video
 *
 * @property      int $id
 * @property      string $file_path
 * @property      int $author_id
 * @property      bool $is_performance
 * @property      \Carbon\Carbon $created_at
 * @property      \Carbon\Carbon $updated_at
 * @property      string $thumbnail_path
 * @property      string $name
 * @property      string $sprite_path
 * @property      float $time
 * @property      int $frames
 * @property-read \App\Models\User $author
 * @property-read $s3_path
 * @property-read $s3_path_effected
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Video whereAuthorId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Video whereCreatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Video whereFilePath($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Video whereFrames($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Video whereId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Video whereIsPerformance($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Video whereName($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Video whereSpritePath($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Video whereThumbnailPath($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Video whereTime($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Video whereUpdatedAt($value)
 * @mixin         \Eloquent
 * @property      int $height
 * @property      int $width
 * @property      string $waveform
 * @property      int|null $videoable_id
 * @property      string|null $videoable_type
 * @property-read mixed $created_at_diff
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereHeight($value)
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereVideoableId($value)
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereVideoableType($value)
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereWaveform($value)
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereWidth($value)
 */

class Video extends BaseModel
{

    // Few helpers for s3
    use S3File;

    const MORPH_TYPE = 'video';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'author_id',
        'parent_id',
        'file_path',
        'thumbnail_path',
        'is_performance',
        'name',
        'sprite_path',
        'time',
        'frames',
        'videoable_id',
        'videoable_type',
    ];

    protected $appends = ['createdAtDiff', 'mediaType'];

    public $attributes = [
        'is_performance' => true
    ];

    public $casts = [
        'is_performance' => 'boolean'
    ];

    /**
     * Video is owned by an author
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function getCountOfUsers($parentId)
    {
        return $this->where('parent_id', $parentId)->get()->count();
    }

    public function getCountOfSaves($video_id)
    {
        return $this->where('parent_id', $video_id)->count();
    }

    public function getCreatedAtDiffAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getMediaTypeAttribute()
    {
        return 'video';
    }

    public static function uploadVideoToPost($file, $post)
    {
        //  Define image instance
        $storage_base = 'https://' . env('AWS_ACCESS_MEDIA_BUCKET') . '.s3.amazonaws.com/';
        $path = 'uploads/posts/' . $post->id . '/videos';
        $file_size = $file->getClientSize();
        $base_file_name = md5(time() . $file_size);
        $ext = $file->extension();
        // Define names
        $file_name = $base_file_name . '.' . $ext;
        // Define pathes
        $full_path = $path . '/' . $file_name;

        // Upload image to s3
        \Storage::disk('s3')->put(
            $full_path,
            file_get_contents($file),
            'public'
        );

        // Create image instance in db
        $video = new static;
        $video->file_path = $storage_base . $full_path;
        $video->author_id = AuthHelper::myId();
        $video->is_performance = false;
        $video->videoable_id = $post->id;
        $video->videoable_type = DBHelper::getMapByModel(Post::class);
        $video->save();

        return $video;
    }

    public static function muteVideo($video_local_dir, $muted_video_name, $source_path)
    {

        $muted_video_path = storage_path('uploads/') . $video_local_dir . '/' . $muted_video_name;

        $mute_video_cmd = sprintf(
            "%s -f lavfi -i aevalsrc=0 -i \"%s\" -vcodec copy" .
            " -acodec aac -map 0:0 -map 1:0 -shortest -strict experimental -y \"%s\"",
            env('FFMPEG'),
            $source_path,
            $muted_video_path
        );

        $mute_video_process = new Process($mute_video_cmd);
        $mute_video_process->setTimeout(0);
        $mute_video_process->run();

        if ($mute_video_process->isSuccessful()) {
            return $muted_video_path;
        }

        return '';
    }
}
