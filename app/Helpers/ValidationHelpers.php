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
    public static function getReplacementValidationParametrs() {
        return [
            'rules' => [
               'week_day_id' => 'nullable|array',
               'weekly_period_id' => 'nullable|array',
               'class_period_id' => 'nullable|array',
               'faculty_id' => 'nullable|array',
               'department_id' => 'nullable|array',
               'professional_level_id' => 'nullable|array',
               'position_id' => 'nullable|array',
               'lesson_room_id' => 'nullable|array',
               'schedule_position' => 'nullable|array',
               'week_data' => 'nullable|string',
      
               'replace_rules.*.week_day_id' => 'nullable|integer|exists:App\WeekDay,id',
               'replace_rules.*.weekly_period_id' => 'nullable|integer|exists:App\WeeklyPeriod,id',
               'replace_rules.*.class_period_id' => 'nullable|integer|exists:App\ClassPeriod,id',
               'replace_rules.*.teacher_id' => 'nullable|integer|exists:App\Teacher,id',
            ],
            'messages' => [
               //
            ],
            'attributes' => [
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
            ]
        ];
    }

}
