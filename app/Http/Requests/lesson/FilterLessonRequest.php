<?php

namespace App\Http\Requests\lesson;

use Illuminate\Foundation\Http\FormRequest;

class FilterLessonRequest extends FormRequest
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
                'lesson_type_id' => 'nullable|array',
                'week_day_id' => 'nullable|array',
                'weekly_period_id' => 'nullable|array',
                'class_period_id' => 'nullable|array',
                'group_id' => 'nullable|array',
                'teacher_id' => 'nullable|array',
                'lesson_room_id' => 'nullable|array',
                'week_number' => 'nullable|string',
            ];
        } else {
            return [];
        }
    }

    public function attributes()
    {
        if ($this->method() == 'POST') {
            return [
                'name' => __('attribute_names.name'),
                'lesson_type_id' => __('attribute_names.lesson_type_id'),
                'week_day_id' => __('attribute_names.week_day_id'),
                'weekly_period_id' => __('attribute_names.weekly_period_id'),
                'class_period_id' => __('attribute_names.class_period_id'),
                'group_id' => __('attribute_names.group_id'),
                'teacher_id' => __('attribute_names.teacher_id'),
                'lesson_room_id' => __('attribute_names.lesson_room_id'),
                'week_number' => __('attribute_names.week_number'),
            ];
        } else {
            return [];
        }
    }
}
