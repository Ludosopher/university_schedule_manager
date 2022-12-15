<?php

namespace App\Http\Requests\user;

use Illuminate\Foundation\Http\FormRequest;

class FilterUserRequest extends FormRequest
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
                'name' => 'nullable|string',
                'teacher_id' => 'nullable|array',
                'group_id' => 'nullable|array',
                'is_moderator' => 'nullable|boolean',
                'is_admin' => 'nullable|boolean',
            ];
        } else {
            return [];
        }
    }

    public function attributes()
    {
        if ($this->method() == 'POST') {
            return [
                'name' => __('attribute_names.full_name'),
                'teacher_id' => __('attribute_names.age_from'),
                'group_id' => __('attribute_names.age_to'),
                'is_moderator' => __('attribute_names.is_moderator'),
                'is_admin' => __('attribute_names.is_admin'),
            ];
        } else {
            return [];
        }
    }
}
