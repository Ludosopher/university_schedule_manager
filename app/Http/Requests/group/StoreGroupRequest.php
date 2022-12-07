<?php

namespace App\Http\Requests\group;

use App\StudyOrientation;
use App\StudyProgram;
use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
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
            'faculty_id' => 'required|integer|exists:App\Faculty,id',
            'study_program_id' => function ($attribute, $value, $fail) {
                if (!in_array($value, StudyProgram::where('faculty_id', request()->input('faculty_id'))->pluck('id')->toArray())) $fail(__('user_validation. '));
            },
            'study_program_id' => function ($attribute, $value, $fail) {
                if (!in_array($value, StudyProgram::where('study_degree_id', request()->input('study_degree_id'))->pluck('id')->toArray())) $fail(__('user_validation.study_degree_study_program_discrepancy'));
            },
            'study_program_id' => 'required|integer|exists:App\StudyProgram,id',
            'study_orientation_id' => function ($attribute, $value, $fail) {
                if (!in_array($value, StudyOrientation::where('study_program_id', request()->input('study_program_id'))->pluck('id')->toArray())) $fail(__('user_validation.study_program_study_orientation_discrepancy'));
            },
            'study_orientation_id' => 'required|integer|exists:App\StudyOrientation,id',
            'study_degree_id' => 'required|integer|exists:App\StudyDegree,id',
            'study_form_id' => 'required|integer|exists:App\StudyForm,id',
            'course_id' => 'required|integer|exists:App\Course,id',
            'size' => 'required|integer', 
            'updating_id' => 'nullable|integer|exists:App\Group,id',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('attribute_names.name'),
            'faculty_id' => __('attribute_names.faculty_id'),
            'study_program_id' => __('attribute_names.study_program_id'),
            'study_orientation_id' => __('attribute_names.study_orientation_id'),
            'study_degree_id' => __('attribute_names.study_degree_id'),
            'study_form_id' => __('attribute_names.study_form_id'),
            'course_id' => __('attribute_names.course_id'),
            'size' => __('attribute_names.size'),
        ];
    }
}
