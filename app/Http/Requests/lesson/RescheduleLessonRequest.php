<?php

namespace App\Http\Requests\lesson;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleLessonRequest extends FormRequest
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
//dd($this->all());
        return [
            'teacher_id' => 'required|integer|exists:App\Teacher,id',
            'lesson_id' => 'required|integer|exists:App\Lesson,id',
            'week_data' => 'nullable|string',
            'week_number' => 'nullable|string',
            'week_dates' => 'nullable|string',
            'is_red_week' => 'nullable|boolean',
        ];
    }
}
