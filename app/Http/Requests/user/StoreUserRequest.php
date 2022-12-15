<?php

namespace App\Http\Requests\user;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'is_moderator' => 'nullable|boolean',
            'is_admin' => 'nullable|boolean',
            'teacher_id' => 'nullable|array',
            'group_id' => 'nullable|array',
            'updating_id' => 'required|integer|exists:App\User,id',
        ];
    }

    public function attributes()
    {
        return [
            'is_moderator' => __('attribute_names.is_moderator'),
            'is_admin' => __('attribute_names.is_admin'),
            'teacher_id' => __('attribute_names.teacher_id'),
            'group_id' => __('attribute_names.group_id'),
        ];
    }
    
}
