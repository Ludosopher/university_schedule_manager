<?php

namespace App\Http\Controllers;

use App\Helpers\SettingHelpers;
use App\Http\Requests\setting\StoreSettingsRequest;
use App\Setting;
use Illuminate\Http\Request;


class SettingController extends Controller
{
    public $config = [
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

    public function getSettings (Request $request)
    {
        $data = [
            'forms' => config('forms.settings'),
            'settings' => Setting::all(),
        ];
        return view("settings")->with('data', $data);
    }

    public function updateSettings (StoreSettingsRequest $request)
    {
        SettingHelpers::updateSettings($request->validated(), $this->config['boolean_attributes']);

        return redirect()->back()->with('response', [
            'success' => true,
            'message' => __('setting.settings_update'),
        ]);
    }

}
