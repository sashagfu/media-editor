<?php

use Aws\Laravel\AwsServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | AWS SDK Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration options set in this file will be passed directly to the
    | `Aws\Sdk` object, from which all client objects are created. The minimum
    | required options are declared here, but the full set of possible options
    | are documented at:
    | http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html
    |
    */

    'region' => env('AWS_REGION', 'us-east-1'),
    'version' => 'latest',
    'ua_append' => [
        'L5MOD/' . AwsServiceProvider::VERSION,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pipelines
    |--------------------------------------------------------------------------
    |
    | More: https://docs.aws.amazon.com/elastictranscoder/latest/developerguide/working-with-pipelines.html?shortFooter=true
    |
    */
    'pipelines' => [
        'common' => env('AWS_PIPELINE_COMMON'),
        'high-priority' => ''
    ],

    /*
    |--------------------------------------------------------------------------
    | Default AWS presets
    |--------------------------------------------------------------------------
    |
    | http://<region>.console.aws.amazon.com/elastictranscoder/home?region=<region>#presets:
    |
    */
    'presets' => [
        [
            'key' => '1080p',
            'name' => 'Custom preset: Generic 1080p (padded)',
            'parent' => 'System preset: Generic 1080p',
        ], [
            'key' => '720p',
            'name' => 'Custom preset: Generic 720p (padded)',
            'parent' => 'System preset: Generic 720p',
        ],
        [
            'key' => '480p_4x3',
            'name' => 'Custom preset: Generic 480p 4:3 (padded)',
            'parent' => 'System preset: Generic 480p 4:3',
        ],[
            'key' => '480p_4x3',
            'name' => 'Custom preset: Generic 480p 4:3 (padded)',
            'parent' => 'System preset: Generic 480p 4:3'
        ],
    ],

    /*
     *  Simple notification service configs
     */
    'sns' => [
        'topics' => [
            'completed' => 'arn:aws:sns:eu-west-1:520999585012:video_is_completed',
            'processing'=> 'arn:aws:sns:eu-west-1:520999585012:video_is_in_progress',
            'warning'   => 'arn:aws:sns:eu-west-1:520999585012:video_has_warning',
            'error'     => 'arn:aws:sns:eu-west-1:520999585012:video_has_error',
        ]
    ]
];
