<?php

/**
 *  Convert ml. seconds to seconds
 * @param float $ml_sec
 * @return float
 */
function ml_sec_to_sec(float $ml_sec)
{
    if ($ml_sec == 0) {
        return 0.0;
    }

    return $ml_sec / 1000;
}

function hex2rgba($hex)
{
    $hex = str_replace("#", "", $hex);
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
    } elseif (strlen($hex) == 6) {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $a = hexdec(substr($hex, 6, 2));
    }
    return [$r, $g, $b, $a ?? 1 ];
}
