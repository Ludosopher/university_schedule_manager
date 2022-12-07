<?php

namespace App\Http\Requests\teacher;

use App\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;

class StoreTeacherRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'patronymic' => 'nullable|string',
            'gender' => 'required|in:мужчина,женщина,не указано',
            'birth_year' => 'required|integer|min:1900|max:2099',
            'phone' => 'required|string',
            'email' => 'required|email',
            'faculty_id' => 'required|integer|exists:App\Faculty,id',
            'department_id' => function ($attribute, $value, $fail) {
                if (!in_array($value, Department::where('faculty_id', request()->input('faculty_id'))->pluck('id')->toArray())) $fail(__('user_validation.faculty_department_discrepancy'));
            },
            'department_id' => 'required|integer|exists:App\Department,id',
            'professional_level_id' => 'required|integer|exists:App\ProfessionalLevel,id',
            'position_id' => 'required|integer|exists:App\Position,id',
            'academic_degree_id' => 'nullable|integer|exists:App\AcademicDegree,id',
            'updating_id' => 'nullable|integer|exists:App\Teacher,id',
        ];
    }

    public function attributes()
    {
        return [
            'first_name' => __('attribute_names.first_name'),
            'last_name' => __('attribute_names.last_name'),
            'patronymic' => __('attribute_names.patronymic'),
            'gender' => __('attribute_names.gender'),
            'birth_year' => __('attribute_names.birth_year'),
            'phone' => __('attribute_names.phone'),
            'email' => __('attribute_names.email'),
            'faculty_id' => __('attribute_names.faculty_id'),
            'department_id' => __('attribute_names.department_id'),
            'professional_level_id' => __('attribute_names.professional_level_id'),
            'position_id' =>  __('attribute_names.position_id'),
            'academic_degree_id' => __('attribute_names.academic_degree_id'),
            'updating_id' => __('attribute_names.updating_id')
        ];
    }
}
