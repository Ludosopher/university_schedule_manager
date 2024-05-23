<?php

namespace App\Http\Requests\teacher;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleTeacherRequest extends FormRequest
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
            "schedule_teacher_id" => "required|integer|exists:App\Teacher,id", 
            "study_period_id" => "nullable|integer|exists:App\StudyPeriod,id",
            "week_number" => "nullable|string",
        ];
    }
    
}
