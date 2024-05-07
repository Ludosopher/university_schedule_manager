<?php

namespace App\Http\Controllers;

use App\Http\Requests\setting\StoreSettingsRequest;
use App\Instances\SettingInstance;
use App\Setting;
use Illuminate\Http\Request;


class SettingController extends Controller
{
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
        (new SettingInstance())->updateSettings($request->validated());

        return redirect()->back()->with('response', [
            'success' => true,
            'message' => __('setting.settings_update'),
        ]);
    }

}
