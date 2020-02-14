<?php

namespace App\Helpers\Proxies;

use Log;

class LaravelLoggerProxy
{
    public function log($msg)
    {
        Log::info($msg);
    }
}
