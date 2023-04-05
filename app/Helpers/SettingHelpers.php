<?php

namespace App\Helpers;

use App\Setting;


class SettingHelpers
{
    public static function updateSettings($data, $boolean_attributes) 
    {
        $settings = Setting::get();
        $data = ModelHelpers::preparingBooleans($data, $boolean_attributes);
        
        foreach ($settings as $setting) {
            if (isset($data[$setting->name])) {
                if ($setting->value != $data[$setting->name]) {
                    $setting->value = (string)$data[$setting->name];
                    $setting->save();
                }
            }
        }
    }


}
