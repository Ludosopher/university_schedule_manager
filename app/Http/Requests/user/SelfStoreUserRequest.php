<?php

namespace App\Http\Requests\user;

use Illuminate\Foundation\Http\FormRequest;

class SelfStoreUserRequest extends FormRequest
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
            'phone' => 'nullable|string',
            'email' => 'nullable|string',
            'updating_id' => 'required|integer|exists:App\User,id',
        ];
    }

    public function attributes()
    {
        return [
            'phone' => __('attribute_names.phone'),
            'email' => __('attribute_names.email'),
        ];
    }
    
}
