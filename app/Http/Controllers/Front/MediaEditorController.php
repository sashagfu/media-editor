<?php

namespace App\Http\Controllers\Front;

use App\Jobs\FireProjectExport;
use App\Models\Audio;
use App\Helpers\AuthHelper;
use App\Models\Image;
use App\Models\ProjectProcess;
use App\Models\Text;
use App\Models\Video;
use App\Notifications\ProjectHasBeenAddedToQueue;
use FFMpeg\FFProbe;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use App\Helpers\DBHelper;
use App\Models\Project;
use App\Http\Requests\CreateTextInputRequest;

/**
 * Class MediaEditorController
 *
 * @package App\Http\Controllers\Api
 */
class MediaEditorController extends Controller
{
    const FPS = 1;
    const THUMB_WIDTH = 160;
    const THUMB_HEIGHT = 90;
    const FRAME_WIDTH = 480;
    const FRAME_HEIGHT = 270;
    const THUMBS_PREFIX = 'video';
    const THUMBS_EXTENSION = 'jpg';
    const THUMB_NAME = 'thumb.jpg';
    const SPRITE_NAME = 'sprite.jpg';
    const SPRITE_CONVERT_WRAPPER = 'bash -c ';

    /**
     * How many seconds allowed for thumbnail generator process
    */
    const THUMB_GENERATE_TIMEOUT = 3600;

    /**
     * Returns HTTP Authorization Header string
     *
     * @url http://docs.aws.amazon.com/AmazonS3/latest/API/sigv4-auth-using-authorization-header.html
     *
     * @param                                  Request $request
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function getAWSSignKey(Request $request)
    {
        $key = base64_encode(
            hash_hmac(
                'sha1',
                $request->to_sign,
                env('AWS_SECRET_ACCESS_KEY', ''),
                true
            )
        );

        $origins = implode(', ', config('cors.allowedOrigins'));
        $methods = implode(', ', config('cors.allowedMethods'));
        $headers = implode(', ', config('cors.allowedHeaders'));

        header('Access-Control-Allow-Origin: ' . $origins);
        header('Access-Control-Allow-Methods: ' . $methods);
        header('Access-Control-Allow-Headers: ' . $headers);

        echo $key;
        exit();
    }
}
