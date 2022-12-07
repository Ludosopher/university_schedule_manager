<?php

namespace App\Http\Requests\group;

use Illuminate\Foundation\Http\FormRequest;

class FilterGroupRequest extends FormRequest
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
                'group_id' => 'nullable|array',
                'faculty_id' => 'nullable|array',
                'study_program_id' => 'nullable|array',
                'study_orientation_id' => 'nullable|array',
                'study_degree_id' => 'nullable|array',
                'study_form_id' => 'nullable|array',
                'course_id' => 'nullable|array',
                'size' => 'nullable|integer',
            ];
        } else {
            return [];
        }
    }

    public function attributes()
    {
        if ($this->method() == 'POST') {
            return [
                'group_id' => __('attribute_names.group_id'),
                'faculty_id' => __('attribute_names.faculty_id'),
                'study_program_id' => __('attribute_names.study_program_id'),
                'study_orientation_id' => __('attribute_names.study_orientation_id'),
                'study_degree_id' => __('attribute_names.study_degree_id'),
                'study_form_id' => __('attribute_names.study_form_id'),
                'course_id' => __('attribute_names.course_id'),
                'size' => __('attribute_names.size'),
            ];
        } else {
            return [];
        }
    }
}
