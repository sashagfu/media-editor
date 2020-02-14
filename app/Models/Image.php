<?php

namespace App\Models;

use App\Helpers\DBHelper;
use App\Models\Helpers\S3File;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\AuthHelper;
use Illuminate\Support\Facades\Auth;
use Img;
use File;
use Storage;

/**
 * App\Models\Image
 *
 * @property int $id
 * @property string $file_name
 * @property int $width
 * @property int $height
 * @property int $file_size
 * @property int $album_id
 * @property int $imageable_id
 * @property string $imageable_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Image whereAlbumId($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Image whereCreatedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Image whereFileName($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Image whereFileSize($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Image whereHeight($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Image whereId($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Image whereImageableId($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Image whereImageableType($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Image whereUpdatedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\App\Models\Image whereWidth($value)
 * @mixin    \Eloquent
 * @property string|null $file_path
 * @property string|null $thumb_path
 * @method   static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereFilePath($value)
 * @method   static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereThumbPath($value)
 */
class Image extends BaseModel
{

    // Few helpers for s3
    use S3File;

    const MORPH_TYPE = 'image';

    protected $appends = [
        'mediaType'
    ];

    const AVATARS = 1;
    const COVERS = 2;
    const POST_ATTACHMENTS = 3;
    const MEDIA_EDITOR_IMAGES = 4;
    const PROJECT_THUMB = 5;
    const AVATAR_WIDTH = 300;
    const AVATAR_HEIGHT = 300;
    const COVER_WIDTH = 1000;
    const COVER_HEIGHT = 350;
    const THUMB_SIZE = 100;
    const THUMB_NAME = '_thumb';
    const AVATAR_EXT = 'jpg';

    public function getMediaTypeAttribute()
    {
        return 'image';
    }

    public static function newUserAvatarFromForm($avatar)
    {

        $user_id = AuthHelper::myId();
        $type = DBHelper::getMapByModel(User::class);
        $file_name = md5(time()) . '.' . self::AVATAR_EXT;
        $path = storage_path() . '/uploads/users/' . $user_id . '/avatars/';
        $user_dir = 'users/' . $user_id;
        $avatars_dir = 'users/' . $user_id . '/avatars/';
        Storage::disk('uploads')->makeDirectory($avatars_dir);
        $file = Img::make($avatar)->resize(self::AVATAR_WIDTH, self::AVATAR_HEIGHT)->save($path . $file_name);
        Storage::disk('s3')->put($avatars_dir . $file_name, file_get_contents($path . $file_name));
        Storage::disk('s3')->setVisibility($avatars_dir . $file_name, 'public');
        Storage::disk('uploads')->deleteDirectory($user_dir);

        $image = new static;
        $image->file_name = $file_name;
        $image->width = $file->width();
        $image->height = $file->height();
        $image->file_size = $file->filesize();
        $image->album_id = self::AVATARS;
        $image->imageable_id = $user_id;
        $image->imageable_type = $type;

        $image->save();

        return $image;
    }

    public static function newCircleCover($cover, $circle_id)
    {
        $type = DBHelper::getMapByModel(Circle::class);
        $file_name = md5(time()) . '.' . $cover->getClientOriginalExtension();
        $path = storage_path() . '/uploads/circles/' . $circle_id . '/covers/';
        File::makeDirectory($path, '0755', true, true);
        $file = Img::make($cover)->resize(self::COVER_WIDTH, self::COVER_HEIGHT)->save($path . $file_name);

        $image = new static;
        $image->file_name = $file_name;
        $image->width = $file->width();
        $image->height = $file->height();
        $image->file_size = $file->filesize();
        $image->album_id = self::COVERS;
        $image->imageable_id = $circle_id;
        $image->imageable_type = $type;

        $image->save();

        return $image;
    }

    public static function newPostImage($image, $post)
    {
        $file = Img::make($image);
        $file_size = $file->filesize();
        $type = DBHelper::getMapByModel(Post::class);
        $file_name = md5(time() . $file_size) . '.' . $image->getClientOriginalExtension();
        $path = storage_path() . '/uploads/posts/' . $post->id . '/attachments/';
        File::makeDirectory($path, '0755', true, true);
        $file->save($path . $file_name);

        $image = new static;
        $image->file_name = $file_name;
        $image->width = $file->width();
        $image->height = $file->height();
        $image->file_size = $file_size;
        $image->album_id = self::POST_ATTACHMENTS;
        $image->imageable_id = $post->id;
        $image->imageable_type = $type;

        $image->save();

        return $image;
    }

    public static function mediaEditorImageUplaod($image_url, $images_path, $ext, $project_id, $storage_base)
    {
        //  Define image instance
        $file = Img::make($image_url);
        $file_size = $file->filesize();
        $base_file_name = md5(time() . $file_size);
        // Define names
        $file_name = $base_file_name . '.' . $ext;
        $thumb_name = $base_file_name . self::THUMB_NAME . '.' . $ext;
        // Define pathes
        $full_path = $images_path . '/' . $file_name;
        $full_thumb_path = $images_path . '/thumbs/' . $thumb_name;

        // Upload image to s3
        Storage::disk('s3')->put(
            $full_path,
            $file->stream()->__toString(),
            'public'
        );
        // Define and upload thumb to s3
        $thumb = $file->resize(self::THUMB_SIZE, self::THUMB_SIZE)->stream()->__toString();
        Storage::disk('s3')->put(
            $full_thumb_path,
            $thumb,
            'public'
        );

        // Create image instance in db
        $image = new static;
        $image->file_name = $file_name;
        $image->width = $file->width();
        $image->height = $file->height();
        $image->file_size = $file_size;
        $image->file_path = $storage_base . $full_path;
        $image->thumb_path = $storage_base . $full_thumb_path;
        $image->album_id = self::MEDIA_EDITOR_IMAGES;
        $image->imageable_id = $project_id;
        $image->imageable_type = DBHelper::getMapByModel(Project::class);
        $image->save();

        return $image;
    }

    public static function uploadProjectThumb($image, $project_id)
    {
        //  Define image instance
        $file = Img::make($image);
        $file_size = $file->filesize();
        $base_file_name = md5(time() . $project_id);
        $base_dir = 'users/' . AuthHelper::myId() . '/projects/' . $project_id;
        $storage_base = 'https://' . env('AWS_ACCESS_MEDIA_BUCKET') . '.s3.amazonaws.com/';
        $thumbs_dir = $base_dir . '/thumbs';
        $ext = 'png';
        // Define names
        $file_name = $base_file_name . '.' . $ext;
        // Define pathes
        $full_path = $thumbs_dir . '/' . $file_name;

        // Upload image to s3
        Storage::disk('s3')->put(
            $full_path,
            $file->stream()->__toString(),
            'public'
        );

        // Create image instance in db
        $image = new static;
        $image->file_name = $file_name;
        $image->width = $file->width();
        $image->height = $file->height();
        $image->file_size = $file_size;
        $image->file_path = $storage_base . $full_path;
        $image->album_id = self::PROJECT_THUMB;
        $image->imageable_id = $project_id;
        $image->imageable_type = DBHelper::getMapByModel(Project::class);
        $image->save();

        return $image;
    }

    public function getNameAttribute()
    {
        return $this->file_name;
    }
}
