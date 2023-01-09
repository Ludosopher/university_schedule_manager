<?php

namespace App\Http\Requests\replacement_request;

use Illuminate\Foundation\Http\FormRequest;

class FilterReplacementReqRequest extends FormRequest
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
        // dd($this->all());
        return [
            'group_id' => 'nullable|array',
            'teacher_id' => 'nullable|array',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'is_regular' => 'nullable|boolean',
            'status_id' => 'nullable|array',
            'user_id' => 'nullable|array',
        ];
    }

    public function attributes()
    {
        return [
            'group_id' => __('attribute_names.full_name'),
            'teacher_id' => __('attribute_names.age_from'),
            'date_from' => __('attribute_names.age_to'),
            'date_to' => __('attribute_names.faculty_id'),
            'is_regular' => __('attribute_names.department_id'),
            'status_id' =>  __('attribute_names.position_id'),
            'user_id' => __('attribute_names.user_id'),
        ];
    }
}
