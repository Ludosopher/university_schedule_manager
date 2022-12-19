<?php

namespace App\Http\Requests\group;

use Illuminate\Foundation\Http\FormRequest;

class ExportScheduleToDocGroupRequest extends FormRequest
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
            'lessons' => 'required|string',
            'group_name' => 'required|string',
            'week_data' => 'nullable|string',
            'week_dates' => 'nullable|string',
            'is_red_week' => 'nullable|boolean',
        ];
    }
}
