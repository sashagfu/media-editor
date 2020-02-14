<?php

namespace App\Services\Studio\Tools;

use App\Services\Sox\Sox;
use App\Services\Studio\Entities\FontContract;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Intervention\Image\Facades\Image;

class Media
{

    /**
     *  Black screen video
     */
    const BLACK_SCREEN_VIDEO = 'black_screen_video.mp4';
    const BLACK_SCREEN_LENGTH = 1.0;

    /** @var string  */
    protected $source_disk = 'local';

    /** @var string  */
    protected $output_disk = 'local';

    public static function init()
    {
        return new static();
    }

    public static function getTempPath()
    {
        /** @var  $path */
        $path = config('filesystems.disks.uploads.root') . config('filesystems.disks.uploads.tmp_path');
        if (!file_exists($path)) {
            mkdir($path, 755, true);
        }
        return $path;
    }

    /**
     *  Convert image to video
     *
     * @param string $image
     * @param float $duration
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function imageToVideo(
        string $image,
        float $duration,
        $project,
        int $width = 1024,
        int $height = 720
    ) {

        /** @var string $temp_path Path to temp files */
        $temp_path = self::getTempPath() . "project_{$project->id}/";

        $tmp_video = md5(time()).'.mp4';

        // STEP 1. Convert image to video
        $to_video_cmd = sprintf(
            env("FFMPEG") . " -y -i \"%s\" -c:v libx264 -pix_fmt yuv420p -vf scale=%d:%d \"%s\"",
            $image,
            $width,
            $height,
            $temp_path . $tmp_video
        );

        // Run converting
        $to_video = new Process($to_video_cmd);
        $to_video->run();

        if ($to_video->isSuccessful()) {
            $result_file = md5(time()) . '.mp4';

            // STEP 2. Add audio channel to video
            $add_audio_chanel_cmd = sprintf(
                env('FFMPEG') . " -y -f lavfi -i %s -i \"%s\" -to %f -c:v copy -c:a aac -strict experimental \"%s\"",
                'anullsrc=channel_layout=stereo:sample_rate=44100', // Silent audio
                $temp_path . $tmp_video,
                $duration,
                $temp_path . $result_file
            );

            $add_audio_channel = new Process($add_audio_chanel_cmd);
            $add_audio_channel->run();

            if ($add_audio_channel->isSuccessful()) {
                return $temp_path . $result_file;
            } else {
                throw new ProcessFailedException($add_audio_channel);
            }
        } else {
            throw new ProcessFailedException($to_video);
        }
    }

    public static function videoToAudio(
        string $video,
        float $start_time = 0.0,
        float $duration = 0.0,
        string $exp = 'wav',
        $project = null
    ) {
        if (App::runningInConsole()) {
            echo "Converting video to audio\n";
        }

        /** @var string $tmp_file Temporary file name */
        $tmp_file = self::getTempPath() . "project_{$project->id}/" . md5(time()).".$exp";

        /** @var string $convert_cmd Convert command */
        $convert_cmd = sprintf(
            env('FFMPEG')." -y -i \"%s\" -f $exp -vn -ss %f -t %f %s",
            $video,
            $start_time,
            $duration,
            $tmp_file
        );

        /** @var \Symfony\Component\Process\Process $conversation */
        $conversation = new Process($convert_cmd);
        $conversation->setTimeout(0);
        $conversation->run();

        if ($conversation->isSuccessful()) {
            return $tmp_file;
        }

        throw new ProcessFailedException($conversation);
    }

    public static function silence(
        $duration,
        $project,
        $rate = '44100',
        $channels = 2,
        $exp = 'wav'
    ) {
        /** @var string $tmp_file Temporary file name */
        $tmp_file = self::getTempPath() . "project_{$project->id}/" . md5(time()).".$exp";

        /** @var Sox $sox */
        $sox = new Sox;

        /** @var \App\Services\Sox\Classes\InputInterface $sox_input */
        $sox_input = Sox::input('-n')
            ->pipe()
            ->cut(0, $duration)
            ->rate($rate)
            ->channels($channels);

        $sox->addInput($sox_input)
            ->saveAs($tmp_file)
            ->process();

        return $tmp_file;
    }

    public static function createBlackScreenVideo(string $disk, string $filename, float $duration)
    {
        if (!Storage::disk($disk)->exists($filename)) {

            /** @var string $tmp_path Temporary file name */
            $tmp_path = self::getTempPath();

            /** @var string $tmp_file File name of video without audio channel */
            $tmp_file = md5(time()) . ".mp4";

            $create_cmd = sprintf(
                env('FFMPEG') . " -s qcif -f rawvideo  -pix_fmt rgb24 -t %f -r 25 -i /dev/zero \"%s\"",
                $duration,
                $tmp_path.$tmp_file
            );

            // Step 1. Create blank video without audio channel
            $create_video_step_1 = new Process($create_cmd);

            $create_video_step_1->run();
            if ($create_video_step_1->isSuccessful()) {
                $tmp_file_with_audio = md5(time()).'_with_audio.mp4';

                $add_audio_channel_cmd = sprintf(
                    env('FFMPEG')." -y -f lavfi  -i \"%s\" -i \"%s\" -to %f -c:v copy -c:a aac -strict experimental \"%s\"", // @codingStandardsIgnoreEnd
                    'anullsrc=channel_layout=stereo:sample_rate=44100', // Silent audio
                    $tmp_path.$tmp_file,
                    $duration,
                    $tmp_path.$tmp_file_with_audio
                );

                // Step 2. Add silence to the video
                $adding_silence = new Process($add_audio_channel_cmd);
                $adding_silence->run();

                if ($adding_silence->isSuccessful()) {
                    Storage::disk('s3')->put(
                        $filename,
                        file_get_contents($tmp_path.$tmp_file_with_audio)
                    );
                } else {
                    throw new ProcessFailedException($adding_silence);
                }
            } else {
                throw new ProcessFailedException($create_video_step_1);
            }
        }

        return $filename;
    }

    public static function textToImage(
        FontContract $font,
        string $text,
        float $x,
        float $y,
        int $width,
        int $height,
        array $background = []
    ) {
        // Background is transparent
        if (empty($background)) {
            $background = [0,0,0,0];
        }

        /** @var string $file_name Image file name */
        $file_name = md5(time()). rand() .'.png';

        /** @var string $file_path Path to image */
        $file_path = self::getTempPath() . $file_name;

        /** @var callable $decorator Callback for setting text styles */
        $decorator = function ($settings) use ($font) {
            $settings->file($font->getAbsolutePath());
            $settings->size($font->size);
            $settings->color($font->color);
        };

        // Create text image
        Image::canvas($width, $height, $background)
            ->text($text, $x, $y + $font->size, $decorator)
            ->save($file_path);

        return $file_path;
    }

    /**
     *  Get video file duration (in seconds)
     * @param string $path
     * @return float
     */
    public static function getVideoDuration(string $path) : float
    {
        /** @var string $command */
        $command = env('FFPROBE') . " -v quiet -of csv=p=0 -show_entries format=duration $path ";

        /** @var \Symfony\Component\Process\Process $process */
        $process = new Process($command);

        $process->run();

        if ($process->isSuccessful()) {
            return (float)$process->getOutput();
        }

        throw new ProcessFailedException($process);
    }

    public static function renderVolumeLevels(
        $tmp_file,
        $volume_levels = [],
        $project = null,
        $ext = 'wav'
    ) {
        $sections_dir = config('filesystems.disks.uploads.tmp_path')
                        . "project_{$project->id}/" . 'sections/';

        Storage::disk('uploads')->makeDirectory($sections_dir);

        $sections = [];
        foreach ($volume_levels as $key => $volume_level) {
            if ($key == 0) {
                continue;
            }

            $previous_volume = $volume_levels[$key - 1]['level'];
            $current_volume = $volume_level['level'];
            $full_volume = 1;

            $sections_dir = self::getTempPath() . "project_{$project->id}/" . 'sections/';

            $section_name = $sections_dir . md5(time()) . "_section_$key.wav";
            $trim_start = $volume_levels[$key - 1]['length'] / 1000;
            $trim_duration = ($volume_level['length'] - $volume_levels[$key - 1]['length']) / 1000;

            /*
            Add 2 seconds to the last volume control point, (the last point on the frontend
            is less for 2 seconds than real track duration for ability to display volume control
            points)
            */
            if (!isset($volume_levels[$key + 1])) {
                $trim_duration += 2;
            }

            // if section is straight set section volume while trimming
            if ($current_volume == $previous_volume) {
                $volume_options = sprintf(
                    "vol %s",
                    $current_volume
                );
            } else {
                $volume_options = "";
            }

            // First we need to cut section sox section.mp3
            $cuted_section_generate_cmd = sprintf(
                'sox %s %s trim %s %s %s',
                $tmp_file,
                $section_name,
                $trim_start,
                $trim_duration,
                $volume_options
            );

            $cuted_section_generate_process = new Process($cuted_section_generate_cmd);
            $cuted_section_generate_process->setTimeout(0);
            $cuted_section_generate_process->run();

            if ($previous_volume < $current_volume) {
                // Fade in
                // x = a * ((b - a) / c)
                $silence_before = $previous_volume * ($trim_duration / ($current_volume - $previous_volume));
                // y = Q - (x + c) |  Q = k * (x + c) / b
                $full_duration = $full_volume * (($silence_before + $trim_duration) / $current_volume);
                $silence_after =
                    ($full_volume * (($silence_before + $trim_duration) / $current_volume))
                    - ($silence_before + $trim_duration);
            } elseif ($previous_volume > $current_volume) {
                // Fade out
                $silence_after = $current_volume * ($trim_duration / ($previous_volume - $current_volume));
                $full_duration = ($full_volume * ($silence_after + $trim_duration)) / $previous_volume;
                $silence_before = $full_duration - ($silence_after + $trim_duration);
            } else {
                // Volume has been set while was trimming
                $sections[] = $section_name;
                continue;
            }

            $section_with_silence = $sections_dir . md5(time())
                                    . "_section_{$key}_with_silence.wav";
            $section_with_silence_cmd = sprintf(
                "sox %s %s pad %s %s",
                $section_name,
                $section_with_silence,
                $silence_before,
                $silence_after
            );

            $section_with_silence_process = new Process($section_with_silence_cmd);
            $section_with_silence_process->setTimeout(0);
            $section_with_silence_process->run();

            $faded_section = $sections_dir . md5(time())
                             . "_section_{$key}_with_silence_faded.wav";

            $fade_options = sprintf(
                "t %s %s %s",
                ($previous_volume < $current_volume) ? $full_duration : 0,
                $full_duration,
                ($previous_volume > $current_volume) ? $full_duration : 0
            );

            $fade_section_cmd = sprintf(
                "sox %s %s fade %s",
                $section_with_silence,
                $faded_section,
                $fade_options
            );

            $fade_section_process = new Process($fade_section_cmd);
            $fade_section_process->setTimeout(0);
            $fade_section_process->run();

            $final_section = $section_name;
            $trim_start = $silence_before;
            $trim_end = $trim_duration;

            $trim_faded_cmd = sprintf(
                "sox %s %s trim %s %s",
                $faded_section,
                $final_section,
                $trim_start,
                $trim_end
            );
            $trim_faded_process = new Process($trim_faded_cmd);
            $trim_faded_process->setTimeout(0);
            $trim_faded_process->run();

            $sections[$key] = $final_section;

            $delete_tmp_files_cmd = sprintf(
                "rm %s %s",
                $section_with_silence,
                $faded_section
            );
            $delete_tmp_files_process = new Process($delete_tmp_files_cmd);
            $delete_tmp_files_process->setTimeout(0);
            $delete_tmp_files_process->run();
        }

        $sox = new Sox();
        $sox->concat();

        foreach ($sections as $section_name) {
            $sox->addInput($sox->input($section_name));
        }

        $final_track = self::getTempPath() . "project_{$project->id}/" . md5(time()). "_final_track" . ".$ext";

        $sox->saveAs($final_track)
            ->process();

        return $final_track;
    }

    public static function addEffects(
        $input
    ) {
        try {
            echo "Adding effects to the input\r\n";

            $file_path = $input->object->file_path;

            $project_dir = config('filesystems.disks.uploads.tmp_path')
                . "project_{$input->project_id}/";

            $tmp_path = config('filesystems.disks.uploads.root')
                . $project_dir;

            $tmp_effected = "{$tmp_path}effected_{$input->object->id}.mp4";

            // Generate command for add effect
            // i.e. ffmpeg -i input.mp4 -vf "fade=type=in:duration=1,fade=type=out:duration=1:start_time=9"
            //-c:a copy output.mp4
            // https://askubuntu.com/questions/1128754/how-do-i-add-a-1-second-fade-out-effect-to-the-end-of-a-video-with-ffmpeg

            $effect_options = sprintf(
                "\"fade=type=in:duration=%s:start_time=%s,fade=type=out:duration=%s:start_time=%s\"",
                $input->effects['fadeIn']['duration'],
                $input->start_from,
                $input->effects['fadeOut']['duration'],
                $input->length + $input->start_from - $input->effects['fadeOut']['duration']
            );

            $effect_video_cmd = sprintf(
                "%s -y -i %s -vf %s -c:a copy %s",
                env('FFMPEG'),
                $file_path,
                $effect_options,
                $tmp_effected
            );

            $effect_video_process = new Process($effect_video_cmd);
            $effect_video_process->setTimeout(0);
            $effect_video_process->run();

            $video_info = pathinfo($input->object->s3_path);

            $effected_final = "{$video_info['dirname']}/{$video_info['filename']}_effected.{$video_info['extension']}";

            if (Storage::disk('s3')->exists($effected_final)) {
                echo "Deleting old effected video...\r\n";
                Storage::disk('s3')->delete($effected_final);
            }

            Storage::disk('s3')->put($effected_final, file_get_contents($tmp_effected));
            Storage::disk('s3')->setVisibility($effected_final, 'public');

            $storage_base = config('filesystems.disks.s3.storage_base');
            $input->object->file_path_effected = $storage_base . $effected_final;
            $input->object->save();

            echo "Video effected successfully!\r\n";
        } catch (\Exception $e) {
            logger($e->getMessage());
        }
    }
}
