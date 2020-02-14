<?php

return [
    'default_disk' => 'local',

    'ffmpeg.binaries' => env('FFMPEG'),
    'ffmpeg.threads'  => 12,

    'ffprobe.binaries' => env('FFPROBE'),

    'timeout' => 3600,
];
