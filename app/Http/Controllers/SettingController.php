<?php

namespace App\Http\Controllers;

use App\Http\Requests\setting\StoreSettingsRequest;
use App\Services\SettingService;
use App\Setting;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class SettingController extends Controller
{
    /** @var SettingService $settingService */
    private $settingService;
    
    public function __construct(
        SettingService $settingService
        )
    {
        $this->settingService = $settingService;
    }    

    /**
     * Display list of schedule manager settings.
     */    
    public function getSettings (Request $request): Renderable
    {
        $data = [
            'forms' => config('forms.settings'),
            'settings' => Setting::all(),
        ];
        return view("settings")->with('data', $data);
    }

    /**
     * Update schedule manager settings.
     */
    public function updateSettings (StoreSettingsRequest $request): RedirectResponse
    {
        $this->settingService->updateSettings($request->validated());

        return redirect()->back()->with('response', [
            'success' => true,
            'message' => __('setting.settings_update'),
        ]);
    }

}
