<?php

namespace App\Http\Requests\replacement_request;

use Illuminate\Foundation\Http\FormRequest;

class StoreReplacementReqRequest extends FormRequest
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
            'updating_id' => 'required|integer|exists:App\ReplacementRequest,id',
            'is_sent' => 'nullable|boolean',
            'is_agreed' => 'nullable|boolean',
            'is_permitted' => 'nullable|boolean',
            'is_not_permitted' => 'nullable|boolean',
            'is_cancelled' => 'nullable|boolean',
            'is_declined' => 'nullable|boolean',
        ];
    }
}
