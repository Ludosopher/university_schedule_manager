<?php

namespace App\Http\Requests\group;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleGroupRequest extends FormRequest
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
            "schedule_group_id" => "required|integer|exists:App\Group,id",
            'week_number' => 'nullable|string'
        ];
    }
}
