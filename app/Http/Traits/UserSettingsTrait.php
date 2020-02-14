<?php namespace App\Http\Traits;

use App\Models\UserSetting;

trait
UserSettingsTrait
{
    // Get setting value
    public function getSetting($name)
    {
        $setting = UserSetting::where(['user_id' => $this->id, 'name' => $name])->get();
        return $setting->isNotEmpty() ? $setting->first()->value : '{}';
    }

    // Create-update setting
    public function setSetting($name, $value)
    {
        $setting = $this->settings()->updateOrCreate(['user_id' => $this->id, 'name' => $name], ['value' => $value]);
        return $setting;
    }

    // Delete setting
    public function deleteSetting($name)
    {
        $setting = UserSetting::where(['user_id' => $this->id, 'name' => $name])->first();
        return $setting ? $setting->delete() : false;
    }
}
