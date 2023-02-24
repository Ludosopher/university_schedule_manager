<?php

namespace App\Http\Requests\replacement_request;

use Illuminate\Foundation\Http\FormRequest;

class SendReplacementReqRequest extends FormRequest
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
            'replaceable_lesson_id' => 'required|integer|exists:App\Lesson,id',
            'replaceable_date' => 'nullable|date',
            'replacing_lesson_id' => 'required|integer|exists:App\Lesson,id',
            'replacing_date' => 'nullable|date',
            'is_regular' => 'nullable|boolean',
            'replacing_teacher_id' => 'required|integer|exists:App\Teacher,id',
        ];
    }
}
