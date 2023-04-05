<?php

namespace App\Http\Requests\setting;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'red_week_is_odd' => 'nullable|boolean',
            'full_time_week_days_limit' => 'required|integer',
            'distance_week_days_limit' => 'required|integer',
            'full_time_class_periods_limit' => 'required|integer',
            'distance_class_periods_limit' => 'required|integer',
            'default_rows_per_page' => 'required|integer',
            'min_replacement_period' => 'required|integer',
        ];
    }

    public function attributes()
    {
        return [
            'red_week_is_odd' => __('attribute_names.red_week_is_odd'),
            'full_time_week_days_limit' => __('attribute_names.full_time_week_days_limit'),
            'distance_week_days_limit' => __('attribute_names.distance_week_days_limit'),
            'full_time_class_periods_limit' => __('attribute_names.full_time_class_periods_limit'),
            'distance_class_periods_limit' => __('attribute_names.distance_class_periods_limit'),
            'default_rows_per_page' => __('attribute_names.default_rows_per_page'),
            'min_replacement_period' => __('attribute_names.min_replacement_period'),
        ];
    }
}
