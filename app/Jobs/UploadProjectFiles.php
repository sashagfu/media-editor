<?php

namespace App\Jobs;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Audio;
use App\Models\Image;
use App\Models\Project;
use App\Models\Video;
use App\Notifications\FilesUploadedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Nuwave\Lighthouse\Execution\Utils\Subscription;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use File;
use Symfony\Component\Process\Process;

class UploadProjectFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Declaring MIMEs types array
    const MIME_TYPES = [
            "323"     => "text/h323",
            "aac"     => "audio/aac",
            "acx"     => "application/internet-property-stream",
            "ai"      => "application/postscript",
            "aif"     => "audio/x-aiff",
            "aifc"    => "audio/x-aiff",
            "aiff"    => "audio/x-aiff",
            "asf"     => "video/x-ms-asf",
            "asr"     => "video/x-ms-asf",
            "asx"     => "video/x-ms-asf",
            "au"      => "audio/basic",
            "avi"     => "video/x-msvideo",
            "axs"     => "application/olescript",
            "bas"     => "text/plain",
            "bcpio"   => "application/x-bcpio",
            "bin"     => "application/octet-stream",
            "bmp"     => "image/bmp",
            "c"       => "text/plain",
            "cat"     => "application/vnd.ms-pkiseccat",
            "cdf"     => "application/x-cdf",
            "cer"     => "application/x-x509-ca-cert",
            "class"   => "application/octet-stream",
            "clp"     => "application/x-msclip",
            "cmx"     => "image/x-cmx",
            "cod"     => "image/cis-cod",
            "cpio"    => "application/x-cpio",
            "crd"     => "application/x-mscardfile",
            "crl"     => "application/pkix-crl",
            "crt"     => "application/x-x509-ca-cert",
            "csh"     => "application/x-csh",
            "css"     => "text/css",
            "dcr"     => "application/x-director",
            "der"     => "application/x-x509-ca-cert",
            "dir"     => "application/x-director",
            "dll"     => "application/x-msdownload",
            "dms"     => "application/octet-stream",
            "doc"     => "application/msword",
            "dot"     => "application/msword",
            "dvi"     => "application/x-dvi",
            "dxr"     => "application/x-director",
            "eps"     => "application/postscript",
            "etx"     => "text/x-setext",
            "evy"     => "application/envoy",
            "exe"     => "application/octet-stream",
            "fif"     => "application/fractals",
            "flr"     => "x-world/x-vrml",
            "gif"     => "image/gif",
            "gtar"    => "application/x-gtar",
            "gz"      => "application/x-gzip",
            "h"       => "text/plain",
            "hdf"     => "application/x-hdf",
            "hlp"     => "application/winhlp",
            "hqx"     => "application/mac-binhex40",
            "hta"     => "application/hta",
            "htc"     => "text/x-component",
            "htm"     => "text/html",
            "html"    => "text/html",
            "htt"     => "text/webviewhtml",
            "ico"     => "image/x-icon",
            "ief"     => "image/ief",
            "iii"     => "application/x-iphone",
            "ins"     => "application/x-internet-signup",
            "isp"     => "application/x-internet-signup",
            "jfif"    => "image/pipeg",
            "jpe"     => "image/jpeg",
            "jpeg"    => "image/jpeg",
            "jpg"     => "image/jpeg",
            "js"      => "application/x-javascript",
            "latex"   => "application/x-latex",
            "lha"     => "application/octet-stream",
            "lsf"     => "video/x-la-asf",
            "lsx"     => "video/x-la-asf",
            "lzh"     => "application/octet-stream",
            "m13"     => "application/x-msmediaview",
            "m14"     => "application/x-msmediaview",
            "m3u"     => "audio/x-mpegurl",
            "m4v"     => "video/x-m4v",
            "man"     => "application/x-troff-man",
            "mdb"     => "application/x-msaccess",
            "me"      => "application/x-troff-me",
            "mht"     => "message/rfc822",
            "mhtml"   => "message/rfc822",
            "mid"     => "audio/mid",
            "mny"     => "application/x-msmoney",
            "mov"     => "video/quicktime",
            "movie"   => "video/x-sgi-movie",
            "mp2"     => "video/mpeg",
            "mp3"     => "audio/mpeg",
            "mp4"     => "video/mpeg",
            "mpa"     => "video/mpeg",
            "mpe"     => "video/mpeg",
            "mpeg"    => "video/mpeg",
            "mpg"     => "video/mpeg",
            "mpp"     => "application/vnd.ms-project",
            "mpv2"    => "video/mpeg",
            "ms"      => "application/x-troff-ms",
            "mvb"     => "application/x-msmediaview",
            "nws"     => "message/rfc822",
            "oda"     => "application/oda",
            "p10"     => "application/pkcs10",
            "p12"     => "application/x-pkcs12",
            "p7b"     => "application/x-pkcs7-certificates",
            "p7c"     => "application/x-pkcs7-mime",
            "p7m"     => "application/x-pkcs7-mime",
            "p7r"     => "application/x-pkcs7-certreqresp",
            "p7s"     => "application/x-pkcs7-signature",
            "pbm"     => "image/x-portable-bitmap",
            "pdf"     => "application/pdf",
            "pfx"     => "application/x-pkcs12",
            "pgm"     => "image/x-portable-graymap",
            "pko"     => "application/ynd.ms-pkipko",
            "pma"     => "application/x-perfmon",
            "pmc"     => "application/x-perfmon",
            "pml"     => "application/x-perfmon",
            "pmr"     => "application/x-perfmon",
            "pmw"     => "application/x-perfmon",
            "pnm"     => "image/x-portable-anymap",
            "pot"     => "application/vnd.ms-powerpoint",
            "ppm"     => "image/x-portable-pixmap",
            "pps"     => "application/vnd.ms-powerpoint",
            "ppt"     => "application/vnd.ms-powerpoint",
            "prf"     => "application/pics-rules",
            "ps"      => "application/postscript",
            "pub"     => "application/x-mspublisher",
            "qt"      => "video/quicktime",
            "ra"      => "audio/x-pn-realaudio",
            "ram"     => "audio/x-pn-realaudio",
            "ras"     => "image/x-cmu-raster",
            "rgb"     => "image/x-rgb",
            "rmi"     => "audio/mid",
            "roff"    => "application/x-troff",
            "rtf"     => "application/rtf",
            "rtx"     => "text/richtext",
            "scd"     => "application/x-msschedule",
            "sct"     => "text/scriptlet",
            "setpay"  => "application/set-payment-initiation",
            "setreg"  => "application/set-registration-initiation",
            "sh"      => "application/x-sh",
            "shar"    => "application/x-shar",
            "sit"     => "application/x-stuffit",
            "snd"     => "audio/basic",
            "spc"     => "application/x-pkcs7-certificates",
            "spl"     => "application/futuresplash",
            "src"     => "application/x-wais-source",
            "sst"     => "application/vnd.ms-pkicertstore",
            "stl"     => "application/vnd.ms-pkistl",
            "stm"     => "text/html",
            "svg"     => "image/svg+xml",
            "sv4cpio" => "application/x-sv4cpio",
            "sv4crc"  => "application/x-sv4crc",
            "t"       => "application/x-troff",
            "tar"     => "application/x-tar",
            "tcl"     => "application/x-tcl",
            "tex"     => "application/x-tex",
            "texi"    => "application/x-texinfo",
            "texinfo" => "application/x-texinfo",
            "tgz"     => "application/x-compressed",
            "tif"     => "image/tiff",
            "tiff"    => "image/tiff",
            "tr"      => "application/x-troff",
            "trm"     => "application/x-msterminal",
            "tsv"     => "text/tab-separated-values",
            "txt"     => "text/plain",
            "uls"     => "text/iuls",
            "ustar"   => "application/x-ustar",
            "vcf"     => "text/x-vcard",
            "vrml"    => "x-world/x-vrml",
            "wav"     => "audio/x-wav",
            "wcm"     => "application/vnd.ms-works",
            "wdb"     => "application/vnd.ms-works",
            "wks"     => "application/vnd.ms-works",
            "wmf"     => "application/x-msmetafile",
            "wps"     => "application/vnd.ms-works",
            "wri"     => "application/x-mswrite",
            "wrl"     => "x-world/x-vrml",
            "wrz"     => "x-world/x-vrml",
            "xaf"     => "x-world/x-vrml",
            "xbm"     => "image/x-xbitmap",
            "xla"     => "application/vnd.ms-excel",
            "xlc"     => "application/vnd.ms-excel",
            "xlm"     => "application/vnd.ms-excel",
            "xls"     => "application/vnd.ms-excel",
            "xlsx"    => "vnd.ms-excel",
            "xlt"     => "application/vnd.ms-excel",
            "xlw"     => "application/vnd.ms-excel",
            "xof"     => "x-world/x-vrml",
            "xpm"     => "image/x-xpixmap",
            "xwd"     => "image/x-xwindowdump",
            "z"       => "application/x-compress",
            "zip"     => "application/zip",
        ];
    const FPS                    = 1;
    const FRAME_WIDTH            = 480;
    const FRAME_HEIGHT           = 270;
    const THUMBS_PREFIX          = 'video';
    const THUMBS_EXTENSION       = 'jpg';
    const THUMB_NAME             = 'thumb.jpg';
    const SPRITE_NAME            = 'sprite.png';
    const SPRITE_CONVERT_WRAPPER = 'bash -c ';

    /**
     * How many seconds allowed for thumbnail generator process
     */
    const THUMB_GENERATE_TIMEOUT = 3600;

    protected $project;

    protected $fileUrls;

    protected $isReady;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Project $project, $fileUrls, $isReady = false)
    {
        $this->project  = $project;
        $this->fileUrls = $fileUrls;
        $this->isReady  = $isReady;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $uploaded_files = collect();

            foreach ($this->fileUrls as $file) {
                $mime_type = $this->getMimeType($file)['type'];
                $ext       = $this->getMimeType($file)['ext'];

                $filename = md5(time() . $this->getMimeType($file)['filename']);

                // Create Project Project Path
                $storage_base = 'https://' . env('AWS_ACCESS_MEDIA_BUCKET') . '.s3.amazonaws.com/';

                $base_dir = 'users/' . $this->project->author->id . '/projects/' . $this->project->id . '/uploads';

                Storage::disk('s3')->makeDirectory($base_dir);

                // Get URL of a stored file, i. e. "https://bucket.s3.amazonaws.com/user%20video.mp4"
                $file_url = $file;

                // Get file name only, i. e. "user video.mp4"
                $file_name_orig = urldecode(str_replace($storage_base, '', $file_url));

                $image = strpos($mime_type, 'image') !== false;
                $audio = strpos($mime_type, 'audio') !== false;
                $video = strpos($mime_type, 'video') !== false;

                if ($image) {
                    // Create images path
                    $images_dir = $base_dir . '/images';
                    $thumbs_dir = $images_dir . '/thumbs';

                    Storage::disk('s3')->makeDirectory($images_dir);
                    Storage::disk('s3')->makeDirectory($thumbs_dir);

                    $image = Image::mediaEditorImageUplaod(
                        $file,
                        $images_dir,
                        $ext,
                        $this->project->id,
                        $storage_base
                    );

                    $uploaded_files->push($image);
                }

                if ($audio) {
                    // Create audio folder in project
                    $audio_dir       = $base_dir . '/audios';
                    $audio_local_dir = $audio_dir;
                    Storage::disk('s3')->makeDirectory($audio_dir);
                    Storage::disk('uploads')->makeDirectory($audio_local_dir);

                    // Generate new file name, i.e. "source-real_file-name.mp3"
                    $source_name = 'source-' . $filename . '.' . $ext;

                    // Full file path, i. e. "users/1/projects/1/source-real_file-name.mp3"
                    $full_path = $audio_local_dir . '/' . $source_name;

                    // Set also local path
                    $local_path = storage_path('uploads/') . $full_path;

                    // Connect then to s3 service
                    /**
                     * @var \League\Flysystem\AwsS3v3\AwsS3Adapter $s3s
                     */
                    $s3s = Storage::disk('s3')->getDriver()->getAdapter();
                    $s3s->getClient()->getObject(
                        [
                            'Bucket' => env('AWS_ACCESS_MEDIA_BUCKET'),
                            'Key'    => $file_name_orig,
                            'SaveAs' => $local_path,
                        ]
                    );

                    // Move file
                    if (!Storage::disk('s3')->exists($full_path)) {
                        Storage::disk('s3')->move($file_name_orig, $local_path);
                    }

                    $audio = Audio::mediaEditorAudioUpload(
                        $file_name_orig,
                        $filename,
                        $full_path,
                        $ext,
                        $audio_local_dir,
                        $this->project->id,
                        $storage_base
                    );

                    if ($this->isReady) {
                        $this->addProjectInput($audio);
                    }

                    $uploaded_files->push($audio);
                }

                if ($video) {
                    // Create video path
                    $video_dir       = $base_dir . '/videos/' . $filename;
                    $video_local_dir = $video_dir;

                    // Make dir on the s3
                    Storage::disk('s3')->makeDirectory($video_dir);

                    // Make dir on the local
                    Storage::disk('uploads')->makeDirectory($video_local_dir);

                    // Create thumbs path
                    $thumbs_dir       = $video_dir;
                    $thumbs_local_dir = $thumbs_dir;
                    Storage::disk('s3')->makeDirectory($thumbs_dir);
                    Storage::disk('uploads')->makeDirectory($thumbs_local_dir);

                    // Generate new file name, i.e. "source-real_file-name.mp3"
                    $source_name = 'source-' . $filename . '.' . $ext;

                    // Full file path, i. e. "users/1/projects/1/source-real_file-name.avi"
                    $full_path = $video_local_dir . '/' . $source_name;

                    // Set also local path
                    $local_path = storage_path('uploads/') . $full_path;

                    // Connect then to s3 service
                    /**
                     * @var \League\Flysystem\AwsS3v3\AwsS3Adapter $s3s
                     */
                    $s3s = Storage::disk('s3')->getDriver()->getAdapter();
                    $s3s->getClient()->getObject(
                        [
                            'Bucket' => env('AWS_ACCESS_MEDIA_BUCKET'),
                            'Key'    => $file_name_orig,
                            'SaveAs' => $local_path,
                        ]
                    );

                    // Convert file to mp4
                    if ($ext !== 'mp4') {
                        $new_name = $filename . '.mp4';
                        FFMpeg::fromDisk('uploads')
                              ->open($full_path)
                              ->export()
                              ->toDisk('uploads')
                              ->inFormat(new \FFMpeg\Format\Video\X264('aac'))
                              ->save($video_local_dir . '/' . $new_name);

                        // Redifine values
                        File::delete($local_path);
                        $source_name = $new_name;
                        $full_path   = $video_local_dir . '/' . $source_name;
                        $local_path  = storage_path('uploads/') . $full_path;
                    }

                    // Prepare Video wave form
                    // First convert video to wav file
                    $video_wav_name = 'video-wav.wav';
                    $media          = FFMpeg::fromDisk('uploads')
                                            ->open($full_path);

                    $streams = $media
                        ->getFFProbe()
                        ->streams($local_path);

                    $wav_json_file = null;

                    if (0 < count($streams->audios())) {
                        $media->export()
                              ->toDisk('uploads')
                              ->inFormat(new \FFMpeg\Format\Audio\Wav())
                              ->save($video_local_dir . '/' . $video_wav_name);

                        $wav_video_local_file = storage_path('uploads/')
                                                . $video_local_dir . '/' . $video_wav_name;
                        $wav_json_name = Audio::generateJsonWav(
                            $wav_video_local_file,
                            $video_local_dir,
                            $filename
                        );

                        // Set separated audio name
                        $audio_name = 'source-' . $filename . '.mp3';

                        // Upload the separated mp3 file to S3
                        Storage::disk('s3')->putFileAs(
                            $video_dir,
                            new \Illuminate\Http\File($wav_video_local_file),
                            $audio_name,
                            'public'
                        );

                        // Set muted video name
                        $muted_video_name = 'source-' . $filename . '_muted.mp4';

                        // Render muted video
                        $muted_video_file = Video::muteVideo(
                            $video_local_dir,
                            $muted_video_name,
                            $local_path
                        );

                        // Upload the muted video to S3
                        Storage::disk('s3')->putFileAs(
                            $video_dir,
                            new \Illuminate\Http\File($muted_video_file),
                            $muted_video_name,
                            'public'
                        );

                        // Define pathes
                        $wav_json_file = $video_local_dir . '/' . $wav_json_name;
                        $wav_json_local_file = storage_path('uploads/')
                                               . $video_local_dir . '/' . $wav_json_name;

                        // Upload the wavJson to S3
                        Storage::disk('s3')->putFileAs(
                            $video_dir,
                            new \Illuminate\Http\File($wav_json_local_file),
                            $wav_json_name,
                            'public'
                        );
                    }


                    // Move file
                    if (!Storage::disk('s3')->exists($full_path)) {
                        Storage::disk('s3')->move($file_name_orig, $full_path);
                    }

                    $video = $this->processVideo(
                        $local_path,
                        $thumbs_dir,
                        $thumbs_local_dir,
                        $storage_base,
                        $full_path,
                        $file_name_orig,
                        $wav_json_file,
                        $video_local_dir,
                        $this->project->id
                    );

                    // If uploaded ready project
                    if ($this->isReady) {
                        $this->addProjectInput($video);
                    }

                    $uploaded_files->push($video);
                }
            }

            Subscription::broadcast('projectFilesUploaded', $uploaded_files);

            $this->project->author->notify(new FilesUploadedNotification($this->project));
        } catch (\Exception $e) {
            logger($e->getMessage());
        }
    }

    private function processVideo(
        $local_path,
        $thumbs_dir,
        $thumbs_local_dir,
        $storage_base,
        $full_path,
        $file_name_orig,
        $wav_json_file,
        $video_local_dir,
        $project_id
    ) {
        // Set time limit to infinite
        set_time_limit(0);

        // Configure FFMpeg params, i. e. "-vf fps=1,scale=160x90"
        $generate_thumbs_opts = sprintf(
            ' -vf fps=%d,scale=%dx%d',
            self::FPS,
            self::FRAME_WIDTH,
            self::FRAME_HEIGHT
        );

        // Define generate thumbs command, i. e.
        // ffmpeg -i "https://bucket.s3.amazonaws.com/users/1/videos/20161116065847/source.mp4" -vf fps=1,scale=160x90
        //      "storage/uploads/users/1/videos/20161116065847/thumbs/video%d.jpg"

        $cmd_generate_thumbs = sprintf(
            '%s -i "%s" %s %s/%s%%d.%s',
            env('FFMPEG', '/bin/false'),
            $local_path,
            $generate_thumbs_opts,
            storage_path('uploads/' . $thumbs_local_dir),
            self::THUMBS_PREFIX,
            self::THUMBS_EXTENSION
        );

        // Execute ffmpeg command
        $process = new Process($cmd_generate_thumbs);
        $process->setTimeout(self::THUMB_GENERATE_TIMEOUT);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Gather all thumbs
        $thumbs = File::allFiles(storage_path('uploads/' . $thumbs_local_dir));

        /**
         * @var \Symfony\Component\Finder\SplFileInfo $thumb
         */
        $key = (int)round(count($thumbs) / 2);
        $thumb = $thumbs["$key"];

        // Upload the thumbs to S3
        Storage::disk('s3')->putFileAs(
            $thumbs_dir,
            new \Illuminate\Http\File($thumb),
            self::THUMB_NAME,
            'public'
        );

        // Define make sprite command, i. e,
        // gm convert "storage/uploads/users/1/videos/20161116065847/thumbs/video{1..123}.jpg" --append
        //      "storage/uploads/users/1/videos/20161116065847/thumbs/sprite.jpg"
        $cmd_make_sprite = sprintf(
            '%s "%s convert %s/%s{1..%d}.%s -append %s"',
            self::SPRITE_CONVERT_WRAPPER,
            env('GRAPHICKSMAGIC', '/bin/false'),
            storage_path('uploads/' . $thumbs_local_dir),
            self::THUMBS_PREFIX,
            count($thumbs),
            self::THUMBS_EXTENSION,
            storage_path('uploads/' . $thumbs_local_dir . '/' . self::SPRITE_NAME)
        );

        // Execute graphicsmagic command
        $process = new Process($cmd_make_sprite);
        $process->run();

        // Upload the sprite to S3
        $uploaded_sprite = Storage::disk('s3')->putFileAs(
            $thumbs_dir,
            new \Illuminate\Http\File(
                storage_path('uploads/' . $thumbs_local_dir) . '/' . self::SPRITE_NAME
            ),
            self::SPRITE_NAME,
            'public'
        );

        // Get video information
        $media_info = FFMpeg::fromDisk('uploads')
                            ->open($full_path)
                            ->getStreams()
                            ->videos()
                            ->first();
        $width = $media_info->get('width');
        $height = $media_info->get('height');
        $time = $media_info->get('duration') * 1000;

        // Create a new object in DB
        $video                 = new Video();
        $video->file_path      = $storage_base . $full_path;
        $video->thumbnail_path = $storage_base . $thumbs_dir . '/' . self::THUMB_NAME;
        $video->sprite_path    = $storage_base . $uploaded_sprite;
        $video->name           = $file_name_orig;
        $video->time           = $time;
        $video->height         = $height;
        $video->width          = $width;
        $video->frames         = count($thumbs);
        $video->waveform       = $storage_base . $wav_json_file;
        $video->author()->associate($this->project->author);
        $video->videoable_id   = $project_id;
        $video->videoable_type = DBHelper::getMapByModel(Project::class);
        $video->save();

        Storage::disk('uploads')->deleteDirectory($video_local_dir);

        return $video;
    }

    private function addProjectInput($file)
    {
        $this->project->inputs()->create(
            [
                'object_id'     => $file->id,
                'type'          => $file::MORPH_TYPE,
                'length'        => $file->time * .001,
                'layer_id'      => 1,
                'position'      => 0,
                'start_from'    => 0,
                'transform'     => [
                    'scale'    => 1,
                    'position' => ['x' => 0, 'y' => 0],
                    'size'     => ['width' => 1, 'height' => 1],
                ],
                'volume_levels' => [],
            ]
        );
    }

    private function getMimeType($file)
    {
        // Return the array value
        $info     = pathinfo($file);
        $ext      = strtolower($info['extension']);
        $filename = $info['filename'];

        return [
            'type'     => self::MIME_TYPES[$ext],
            'ext'      => $ext,
            'filename' => $filename,
        ];
    }
}
