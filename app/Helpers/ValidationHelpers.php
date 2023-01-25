<?php

namespace App\Helpers;

use App\ClassPeriod;
use App\Lesson;
use App\Teacher;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ValidationHelpers
{
    public static function validation($data, $rules, $messages = [], $attributes = []) {
        
        $validator = Validator::make($data, $rules, $messages, $attributes);
        if ($validator->fails()) {
            return [
                'success' => false,
                'validator' => $validator 
            ]; 
        }
        return [
            'success' => true,
            'validated' => $validator->validated()
        ];
    }

    public static function getReplacementVariantsValidation($data) {
        
        $rules = [
            'week_day_id' => 'nullable|array',
            'weekly_period_id' => 'nullable|array',
            'class_period_id' => 'nullable|array',
            'faculty_id' => 'nullable|array',
            'department_id' => 'nullable|array',
            'professional_level_id' => 'nullable|array',
            'position_id' => 'nullable|array',
            'lesson_room_id' => 'nullable|array',
            'schedule_position' => 'nullable|array',
            'week_number' => 'nullable|string',
            'week_data' => 'nullable|string',
            'week_dates' => 'nullable|string',
            'is_red_week' => 'nullable|boolean',
      
            'replace_rules.*.week_day_id' => 'nullable|integer|exists:App\WeekDay,id',
            'replace_rules.*.weekly_period_id' => 'nullable|integer|exists:App\WeeklyPeriod,id',
            'replace_rules.*.class_period_id' => 'nullable|integer|exists:App\ClassPeriod,id',
            'replace_rules.*.teacher_id' => 'nullable|integer|exists:App\Teacher,id',
            'replace_rules.*.date' => 'nullable|date',
        ];
        
        $messages = [
               //
        ];
        
        $attributes = [
            'week_day_id' => __('attribute_names.week_day_id'),
            'weekly_period_id' => __('attribute_names.weekly_period_id'),
            'class_period_id' => __('attribute_names.class_period_id'),
            'faculty_id' => __('attribute_names.faculty_id'),
            'department_id' => __('attribute_names.department_id'),
            'professional_level_id' => __('attribute_names.professional_level_id'),
            'position_id' =>  __('attribute_names.position_id'),
            'lesson_room_id' => __('attribute_names.lesson_room_id'),
            'schedule_position' => __('attribute_names.schedule_position'),
            'week_data' => __('attribute_names.week_data'),
      
            'replace_rules.*.week_day_id' => __('attribute_names.replace_rules_week_day_id'),
            'replace_rules.*.weekly_period_id' => __('attribute_names.replace_rules_weekly_period_id'),
            'replace_rules.*.class_period_id' => __('attribute_names.replace_rules_class_period_id'),
            'replace_rules.*.teacher_id' => __('attribute_names.replace_rules_teacher_id'),
        ];

        return self::validation($data, $rules, $messages, $attributes);
    }

    public static function getTeacherRescheduleValidation($data) {
        
        $rules = [
            'teacher_id' => 'required|integer|exists:App\Teacher,id',
            'lesson_id' => 'required|integer|exists:App\Lesson,id',
            'week_number' => 'nullable|string',
            'week_dates' => 'nullable|string',
            'is_red_week' => 'nullable|boolean',
        ];
        
        return self::validation($data, $rules);
    }

    public static function getGroupRescheduleValidation($data) {
        
        $rules = [
            'group_id' => 'required|integer|exists:App\Group,id',
            'teacher_id' => 'required|integer|exists:App\Teacher,id',
            'lesson_id' => 'required|integer|exists:App\Lesson,id',
            'week_number' => 'nullable|string',
            'week_dates' => 'nullable|string',
            'is_red_week' => 'nullable|boolean',
        ];
        
        return self::validation($data, $rules);
    }

    public static function exportTeacherRescheduleToDocValidation($data) {
        
        $rules = [
            'lessons' => 'required|string',
            'teacher_name' => 'required|string',
            'rescheduling_lesson_id' => 'required|integer|exists:App\Lesson,id',
            'week_data' => 'nullable|string',
            'week_dates' => 'nullable|string',
            'is_red_week' => 'nullable|boolean',
        ];
        
        return self::validation($data, $rules);
    }

    public static function exportGroupRescheduleToDocValidation($data) {
        
        $rules = [
            'lessons' => 'required|string',
            'group_name' => 'required|string',
            'rescheduling_lesson_id' => 'required|integer|exists:App\Lesson,id',
            'week_data' => 'nullable|string',
            'week_dates' => 'nullable|string',
            'is_red_week' => 'nullable|boolean',
        ];
        
        return self::validation($data, $rules);
    }

    public static function exportReplacementToDocValidation($data) {
        
        $rules = [
            'replacement_lessons' => 'required|string',
            'header_data' => 'required|string',
            'week_data' => 'nullable|string',
            'week_dates' => 'nullable|string',
            'is_red_week' => 'nullable|boolean',
        ];
        
        return self::validation($data, $rules);
    }

    public static function exportReplacementScheduleToDocValidation($data) {
        
        $rules = [
            'lessons' => 'required|string',
            'header_data' => 'required|string',
            'week_data' => 'nullable|string',
            'replaceable_lesson_id' => 'required|integer|exists:App\Lesson,id',
            'week_dates' => 'nullable|string',
            'is_red_week' => 'nullable|boolean',
            'date_or_weekly_period' => 'nullable|string', 
        ];
        
        return self::validation($data, $rules);
    }

    public static function exportMonthTeacherScheduleToDocValidation($data) {
        
        $rules = [
            "month_name" => "required|string",
            "teacher_name" => "required|string",
            "weeks" => "required|string", 
        ];
        
        return self::validation($data, $rules);
    }

    public static function exportMonthGroupScheduleToDocValidation($data) {
        
        $rules = [
            "month_name" => "required|string",
            "group_name" => "required|string",
            "weeks" => "required|string", 
        ];
        
        return self::validation($data, $rules);
    }

    public static function addReplacementRequestValidation($data) {
        
        $min_replacement_period = config('site.min_replacement_period');
                
        $rules = [
            'replaceable_lesson_id' => 'required|integer|exists:App\Lesson,id',
            'replacing_lesson_id' => 'required|integer|exists:App\Lesson,id',
            'replaceable_date' => 'nullable|string',
            'replaceable_date' => function ($attribute, $value, $fail) use ($min_replacement_period) {
                if (isset($value)) {
                    $replaceable_hours_diff = round((strtotime($value) - strtotime(now()))/3600);
                    if ($replaceable_hours_diff < $min_replacement_period) $fail(__('user_validation.not_time_for_replacement_request_process'));
                }
            },
            'replacing_date' => 'nullable|string',
            'replacing_date' => function ($attribute, $value, $fail) use ($min_replacement_period) {
                if (isset($value)) {
                    $replacing_hours_diff = round((strtotime($value) - strtotime(now()))/3600);
                    if ($replacing_hours_diff < $min_replacement_period) $fail(__('user_validation.not_time_for_replacement_request_process'));
                }
            },
            'is_regular' => 'required|boolean',
            'initiator_id' => 'required|integer|exists:App\User,id', 
        ];

        return self::validation($data, $rules);
    }

}
