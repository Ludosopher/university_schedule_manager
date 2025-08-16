<?php

namespace App\Http\Requests\lesson;

use App\Instances\LessonInstance;
use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
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
            'name' => 'required|string',
            'study_period_id' => 'required|integer|exists:App\StudyPeriod,id',
            'lesson_type_id' => 'required|integer|exists:App\LessonType,id',
            'week_day_id' => 'required|integer|exists:App\WeekDay,id',
            'weekly_period_id' => 'required|integer|exists:App\WeeklyPeriod,id',
            'class_period_id' => 'required|integer|exists:App\ClassPeriod,id',
            'group_id' => function ($attribute, $value, $fail) {
                if ((new LessonInstance())->searchSameLesson(request()->all(), $attribute)) $fail(__('user_validation.group_is_occupied'));
            },
            'group_id' => 'required|array',
            'teacher_id' => function ($attribute, $value, $fail) {
                if ((new LessonInstance())->searchSameLesson(request()->all(), $attribute)) $fail(__('user_validation.teacher_is_occupied'));
            },
            'teacher_id' => 'required|integer|exists:App\Teacher,id',
            'lesson_room_id' => function ($attribute, $value, $fail) {
                if ((new LessonInstance())->searchSameLesson(request()->all(), $attribute)) $fail(__('user_validation.room_is_occupied'));
            },
            'lesson_room_id' => 'required|integer|exists:App\LessonRoom,id',
            'updating_id' => 'nullable|integer|exists:App\Lesson,id',
            'date' => 'nullable|date',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('attribute_names.name'),
            'lesson_type_id' => __('attribute_names.lesson_type_id'),
            'week_day_id' => __('attribute_names.week_day_id'),
            'weekly_period_id' => __('attribute_names.weekly_period_id'),
            'class_period_id' => __('attribute_names.class_period_id'),
            'group_id' => __('attribute_names.group_id'),
            'teacher_id' => __('attribute_names.teacher_id'),
            'lesson_room_id' => __('attribute_names.lesson_room_id'),
            'updating_id' => __('attribute_names.updating_id'),
            'date' => __('attribute_names.date'),
        ];
    }
}
