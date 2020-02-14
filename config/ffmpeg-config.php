<?php

return array(
    /**
     * Basic configuration for FFMPEG to define a custom command if it needed
     *
     * In ffmpeg.commands can define a custom ffmpeg set of command to implement using saveWitCustomConfiguration method
     */

    'ffprobe' => [
        'binaries'  => env('FFPROBE'),
    ],
    'timeout'           => 3600, // The timeout for the underlying process
    'ffmpeg' => [
        'binaries' => env('FFMPEG'),
        'threads'    => 0,   // The number of threads that FFMpeg should use
        'commands'   => array(
            '-b:v',
            '64k',
            '-refs',
            '6',
            '-coder',
            '1',
            '-sc_threshold',
            '40',
            '-flags',
            '+loop',
            '-me_range',
            '16',
            '-subq',
            '7',
            '-i_qfactor',
            '0.71',
            '-qcomp',
            '0.6',
            '-qdiff',
            '4',
            '-trellis',
            '1'
        ),
    ],
);
