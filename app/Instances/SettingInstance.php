<?php

namespace App\Instances;

use App\Instances\Instance;
use App\Setting;

class SettingInstance extends Instance
{
    protected $config = [
        'model_name' => 'App\Setting',
        'instance_name' => 'setting',
        'instance_plural_name' => 'setting',
        'instance_name_field' => 'name',
        'profession_level_name_field' => null,
        'eager_loading_fields' => [],
        'other_lesson_participant' => null,
        'other_lesson_participant_name' => null,
        'boolean_attributes' => ['red_week_is_odd'],
        'many_to_many_attributes' => [],
    ];

    public function updateSettings($data) 
    {
        $settings = Setting::get();
        $data = $this->preparingBooleans($data);
        
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