<?php

namespace App\Helpers;

use Exception;

final class URLHelper
{
    public static function getDocumentTitle($url)
    {
        try {
            $str = file_get_contents($url);

            if (strlen($str) > 0) {
                $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
                preg_match("/\<title\>(.*)\<\/title\>/i", $str, $title); // ignore case

                return $title[1];
            }
        } catch (Exception $e) {
        }

        return '';
    }
}
