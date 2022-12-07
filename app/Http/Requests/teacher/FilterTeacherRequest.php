<?php

namespace App\Http\Requests\teacher;

use Illuminate\Foundation\Http\FormRequest;

class FilterTeacherRequest extends FormRequest
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
        if ($this->method() == 'POST') {
            return [
                'full_name' => 'nullable|string',
                'age_from' => 'nullable|integer',
                'age_to' => 'nullable|integer',
                'faculty_id' => 'nullable|array',
                'department_id' => 'nullable|array',
                'professional_level_id' => 'nullable|array',
                'position_id' => 'nullable|array',
                'academic_degree_id' => 'nullable|array',
            ];
        } else {
            return [];
        }
    }

    public function attributes()
    {
        if ($this->method() == 'POST') {
            return [
                'full_name' => __('attribute_names.full_name'),
                'age_from' => __('attribute_names.age_from'),
                'age_to' => __('attribute_names.age_to'),
                'faculty_id' => __('attribute_names.faculty_id'),
                'department_id' => __('attribute_names.department_id'),
                'professional_level_id' => __('attribute_names.professional_level_id'),
                'position_id' =>  __('attribute_names.position_id'),
                'academic_degree_id' => __('attribute_names.academic_degree_id'),
            ];
        } else {
            return [];
        }
    }
}
