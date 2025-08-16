<?php

namespace App\Http\Requests\lesson;

use App\Helpers\DateHelpers;
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
            'rescheduling_lesson_date' => 'nullable|date',
            'week_data' => 'nullable|string',
            'week_dates' => 'nullable|string',
            'is_red_week' => 'nullable|boolean',
            'week_number' => 'nullable|string',
            'week_number' => function ($attribute, $value, $fail) {
                $study_seasons = config('enum.study_seasons');
                $study_periods_data = DateHelpers::getStudyPeriodsData();
                $required_study_period = DateHelpers::getRequiredStudyPeriod($study_periods_data['all_periods'], $study_periods_data['current_period_id']);
                if (isset($value) && DateHelpers::checkWeekToStudyPeriodSeason($required_study_period, $value) !== $study_seasons['studies']) $fail(__('user_validation.failed_week_number'));
            },
        ];
    }
}
