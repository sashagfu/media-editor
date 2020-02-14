<?php

namespace App\Http\GraphQL\Types;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSetting;

class SettingsType
{
    public function privacy($settings)
    {
        return $settings->where('name', 'privacy')->first()->value ?? null;
    }
}
