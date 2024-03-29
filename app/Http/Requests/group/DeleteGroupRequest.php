<?php

namespace App\Http\Requests\group;

use Illuminate\Foundation\Http\FormRequest;

class DeleteGroupRequest extends FormRequest
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
            'deleting_id' => 'required|integer|exists:App\Group,id',
        ];

        
    }

    public function attributes()
    {
        return [
            'deleting_id' => __('attribute_names.deleting_id'),
        ];
    }
}
