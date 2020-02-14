<?php

namespace App\Models;

use App\Helpers\DBHelper;
use App\Models\Helpers\S3File;
use Illuminate\Database\Eloquent\Model;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use File;
use Storage;

/**
 * App\Models\Audio
 *
 * @property int $id
 * @property string $name
 * @property string|null $sprite
 * @property int $time
 * @property string $file_path
 * @property int|null $audioable_id
 * @property string|null $audioable_type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method   static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereAudioableId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereAudioableType($value)
 * @method   static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereCreatedAt($value)
 * @method   static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereFilePath($value)
 * @method   static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereName($value)
 * @method   static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereSprite($value)
 * @method   static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereTime($value)
 * @method   static \Illuminate\Database\Eloquent\Builder|\App\Models\Audio whereUpdatedAt($value)
 * @mixin    \Eloquent
 */
class Audio extends BaseModel
{
    // Few helpers for s3
    use S3File;

    const MORPH_TYPE = 'audio';

    const CHANNELS_OPTION = '--channels left right';
    const SAMPLES_OPTION = '-s 20000';
    const JSON_WAVE_TIMEOUT = 180;

    protected $fillable = ['name', 'sprite', 'time', 'file_path'];

    public static function mediaEditorAudioUpload(
        $file_name_orig,
        $filename,
        $full_path,
        $ext,
        $audio_local_dir,
        $project_id,
        $storage_base
    ) {
        // First lets us convert file to WAV to generate json wave
        if ($ext !== 'wav') {
            $new_file_name = 'converted-wav.wav';

            FFMpeg::fromDisk('uploads')
                ->open($full_path)
                ->export()
                ->toDisk('uploads')
                ->inFormat(new \FFMpeg\Format\Audio\Wav)
                ->save($audio_local_dir . '/' . $new_file_name);

            $converted_file_path = $audio_local_dir . '/' . $new_file_name;
        }

        $local_path = storage_path('uploads/') . $full_path;

        // Run generating json wave
        $file_path = isset($converted_file_path) ? storage_path('uploads/') . $converted_file_path : $local_path;

        // Define generate json wave command, i. e.
        //  wav2json tmp.wav --channels left right -s 20000 -o source.json
        $json_file = storage_path('uploads/' . $audio_local_dir . '/' . 'sprite-' . $filename . '.json');
        $json_wave_generate = sprintf(
            '%s %s %s %s -o %s',
            env('WAV2JSON', '/bin/false'),
            $file_path,
            self::CHANNELS_OPTION,
            self::SAMPLES_OPTION,
            $json_file
        );
        $process = new Process($json_wave_generate);
        $process->setTimeout(self::JSON_WAVE_TIMEOUT);
        $process->run();

        if (!$process->isSuccessful()
            || !is_readable($json_file)
        ) {
            throw new ProcessFailedException($process);
        }

        // Upload Json file to s3 immediately
        Storage::disk('s3')->putFileAs(
            $audio_local_dir,
            new \Illuminate\Http\File($json_file),
            'sprite-' . $filename . '.json',
            'public'
        );

        // Redifine audio file
        $file = 'source-' . $filename . '.' . $ext;

        // If file is not in MP3, convert it to MP3
        if ($ext !== 'mp3') {
            $new_file_name = $filename . '.mp3';

            FFMpeg::fromDisk('uploads')
                ->open($full_path)
                ->export()
                ->toDisk('uploads')
                ->inFormat(new \FFMpeg\Format\Audio\Mp3)
                ->save($audio_local_dir . '/' . $new_file_name);

            // declare new file name
            $full_path = $audio_local_dir . '/' . $new_file_name;
            $file = $new_file_name;
        }

        // Upload mp3 to s3 immediately
        Storage::disk('s3')->putFileAs(
            $audio_local_dir,
            new \Illuminate\Http\File(storage_path('uploads/' . $full_path)),
            $file,
            'public'
        );

        // Generate sprite link
        $sprite_link = $storage_base . $audio_local_dir . '/' . 'sprite-' . $filename . '.json';

        // Generate file path links
        $file_link = $storage_base . $full_path;

        // Get time for file
        $time = FFMpeg::fromDisk('uploads')->open($full_path)->getDurationInSeconds() * 1000;

        // Delete all audio files
        Storage::disk('uploads')->deleteDirectory($audio_local_dir);

        // Create audio object
        $audio = new static;
        $audio->name = $file_name_orig;
        $audio->sprite = $sprite_link;
        $audio->time = $time;
        $audio->file_path = $file_link;
        $audio->audioable_id = $project_id;
        $audio->audioable_type = DBHelper::getMapByModel(Project::class);
        $audio->save();

        return $audio;
    }

    public static function generateJsonWav($file_path, $videos_dir, $filename)
    {
        // Define generate json wave command, i. e.
        //  wav2json tmp.wav --channels left right -s 20000 -o source.json
        $json_wave_generate = sprintf(
            '%s %s %s %s -o %s',
            env('WAV2JSON', '/bin/false'),
            $file_path,
            self::CHANNELS_OPTION,
            self::SAMPLES_OPTION,
            storage_path('uploads/' . $videos_dir . '/' . 'sprite-wav-' . $filename . '.json')
        );

        $process = new Process($json_wave_generate);
        $process->setTimeout(self::JSON_WAVE_TIMEOUT);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return 'sprite-wav-' . $filename . '.json';
    }
}
