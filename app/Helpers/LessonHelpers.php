<?php

namespace App\Helpers;

use App\ClassPeriod;
use App\Group;
use App\Lesson;
use App\Teacher;
use App\WeekDay;
use App\WeeklyPeriod;
use Illuminate\Support\Facades\Schema;

class LessonHelpers
{
    public static function getLessonsForReplacement($data)
    {
        $weekly_period_ids = config('enum.weekly_period_ids');
        $replacement_lessons = [];

        $replacing_lesson = Lesson::where('id', $data['lesson_id'])->first();
        $seeking_teacher = Teacher::where('id', $data['teacher_id'])->with(['lessons'])->first();
        $group = Group::where('id', $data['group_id'])->with(['lessons.teacher.lessons'])->first();
        
        $is_suitable_teacher = true;
        $is_suitable_lesson = true;
        $looked_teachers = [$seeking_teacher->id];

        foreach ($group->lessons as $g_lesson) {
            if (!in_array($g_lesson->teacher->id, $looked_teachers)) {
                foreach ($g_lesson->teacher->lessons as $dt_lesson) {
                    if ($dt_lesson->week_day_id == $data['week_day_id']
                        && ($dt_lesson->weekly_period_id == $data['weekly_period_id'] || $dt_lesson->weekly_period_id == $weekly_period_ids['every_week'])
                        && $dt_lesson->class_period_id == $data['class_period_id'])
                    {
                        $is_suitable_teacher = false;
                        break;
                    }
                }
                $looked_teachers[] = $g_lesson->teacher->id;
                if ($is_suitable_teacher) {
                    foreach ($g_lesson->teacher->lessons as $dt_lesson) {
                        if ($dt_lesson->group_id == $data['group_id']) {
                            foreach ($seeking_teacher->lessons as $st_lesson) {
                                if ($st_lesson->week_day_id == $dt_lesson->week_day_id
                                    && ($st_lesson->weekly_period_id == $dt_lesson->weekly_period_id || $st_lesson->weekly_period_id == $weekly_period_ids['every_week'])
                                    && $st_lesson->class_period_id == $dt_lesson->class_period_id)
                                {
                                    $is_suitable_lesson = false;
                                    break;
                                }
                            }
                            if ($is_suitable_lesson) {
                                $replacement_lessons[] = [
                                    'subject' => $dt_lesson->name,
                                    'week_day_id' => ['id' => $dt_lesson->week_day->id, 'name' => $dt_lesson->week_day->name],
                                    'weekly_period_id' => ['id' => $dt_lesson->weekly_period->id, 'name' => $dt_lesson->weekly_period->name],
                                    'class_period_id' => ['id' => $dt_lesson->class_period->id, 'name' => $dt_lesson->class_period->name],
                                    'teacher_id' => $g_lesson->teacher->id,
                                    'profession_level_name' => $g_lesson->teacher->profession_level_name,
                                    'phone' => $g_lesson->teacher->phone,
                                    'age' => $g_lesson->teacher->age,
                                    'department_id' => ['id' => $g_lesson->teacher->department->id, 'name' => $g_lesson->teacher->department->name],
                                    'position_id' => ['id' => $g_lesson->teacher->position->id, 'name' => $g_lesson->teacher->position->name],
                                    'schedule_position_id' => self::getLessonSchedulePosition($replacing_lesson, $g_lesson->teacher)
                                ];
                            }
                            $is_suitable_lesson = true;
                        }
                    }
                }
                $is_suitable_teacher = true;
            }
        }

        return $replacement_lessons;
    }

    public static function getLessonSchedulePosition ($lesson, $teacher) {
              
        $is_prev_lesson = false;
        $is_next_lesson = false;

        foreach ($teacher->lessons as $teacher_lesson) {
            if ($teacher_lesson->week_day_id == $lesson->week_day_id) {
                if ($teacher_lesson->class_period_id == $lesson->class_period_id + 1) {
                    $is_prev_lesson = true;
                } elseif ($teacher_lesson->class_period_id == $lesson->class_period_id - 1) {
                    $is_next_lesson = true;
                }
            }
        }

        if ($is_prev_lesson && $is_next_lesson) {
            $result = ['id' => 1, 'name' => 'Между двумя имеющимися парами'];
        } elseif ($is_prev_lesson || $is_next_lesson) {
            $result = ['id' => 2, 'name' => 'Рядом с одной из имеющихся пар'];
        } else {
            $result = ['id' => 3, 'name' => 'Нет рядом имеющихся пар']; 
        }
       
        return $result;
    }

    public static function getReplacementData($data)
    {
        $replacement_lessons = [];
        if (!isset($data['replace_rules'])) {
            if (isset($data['prev_replace_rules'])) {
                $prev_replace_rules = json_decode($data['prev_replace_rules'], true);
                $replacement_lessons = LessonHelpers::getLessonsForReplacement($prev_replace_rules);
            }
        } else {
            $replacement_lessons = LessonHelpers::getLessonsForReplacement($data['replace_rules']);
            $prev_replace_rules = $data['replace_rules'];
        }
         
        $filtered_replacement_lessons = FilterHelpers::getFilteredArrayOfArrays($replacement_lessons, $data);
        
        $replacemented_lesson = Lesson::with(['week_day', 'weekly_period', 'class_period', 'teacher', 'group'])->where('id', $prev_replace_rules['lesson_id'])->first();
        if ($replacemented_lesson) {
            $header_data = [
                'week_day' => mb_strtolower($replacemented_lesson->week_day->name),
                'weekly_period' => mb_strtolower($replacemented_lesson->weekly_period->name),
                'class_period' => mb_strtolower($replacemented_lesson->class_period->name),
                'teacher' => $replacemented_lesson->teacher->profession_level_name,
                'group' => $replacemented_lesson->group->name,
            ];
        }

        $data = [
            'replacement_lessons' => $filtered_replacement_lessons,
            'table_properties' => config("tables.replacement_variants"),
            'filter_form_fields' => Lesson::getReplacementFilterFormFields(),
            'prev_replace_rules' => $prev_replace_rules,
            'header_data' => $header_data
        ];

        return array_merge($data, Lesson::getReplacementProperties());
    }

    public static function getReschedulingData($data) {
        
        $teacher = Teacher::with(['lessons'])->where('id', $data['teacher_id'])->first();
        $group = Group::with(['lessons'])->where('id', $data['group_id'])->first();
        $lesson = Lesson::with(['week_day', 'weekly_period', 'class_period'])->where('id', $data['lesson_id'])->first();
        $weekly_period_ids = config('enum.weekly_period_ids');
        $rescheduling_week_days_limit = config('site.rescheduling_week_days_limit');
        $rescheduling_class_periods_limit = config('site.rescheduling_class_periods_limit');
        $week_days = WeekDay::select('id', 'name')->get();
        $class_periods = ClassPeriod::get();
        
        $is_teacher_busy = ['result' => false, 'weekly_period' => null];
        $is_group_busy = ['result' => false, 'weekly_period' => null];
        $free_periods = [];
        foreach ($week_days as $week_day) {
            if ($week_day->id <= $rescheduling_week_days_limit) {
                foreach ($class_periods as $class_period) {
                    if ($class_period->id <= $rescheduling_class_periods_limit) {
                        foreach ($teacher->lessons as $teacher_lesson) {
                            if ($teacher_lesson->week_day_id == $week_day->id && $teacher_lesson->class_period_id == $class_period->id) {
                                $is_teacher_busy['result'] = true;
                                $is_teacher_busy['weekly_period'] = $teacher_lesson->weekly_period->id;
                                break;
                            }
                        }
                        foreach ($group->lessons as $group_lesson) {
                            if ($group_lesson->week_day_id == $week_day->id && $group_lesson->class_period_id == $class_period->id) {
                                $is_group_busy['result'] = true;
                                $is_group_busy['weekly_period'] = $group_lesson->weekly_period->id;
                                break;
                            }
                        }
                        if ((!$is_teacher_busy['result'] && !$is_group_busy['result'])
                            || ($is_teacher_busy['result'] 
                                && $is_group_busy['result'] 
                                && $is_teacher_busy['weekly_period'] != $weekly_period_ids['every_week'] 
                                && $is_teacher_busy['weekly_period'] != $is_group_busy['weekly_period'])
                            || (!$is_teacher_busy['result'] && $is_group_busy['weekly_period'] != $weekly_period_ids['every_week'])
                            || (!$is_group_busy['result'] && $is_teacher_busy['weekly_period'] != $weekly_period_ids['every_week']))
                        {
                            
                            if ($is_teacher_busy['weekly_period'] == $weekly_period_ids['red_week']) {
                                $teacher_weekly_period = $weekly_period_ids['blue_week']; 
                            } elseif ($is_teacher_busy['weekly_period'] == $weekly_period_ids['blue_week']) {
                                $teacher_weekly_period = $weekly_period_ids['red_week']; 
                            } else {
                                $teacher_weekly_period = $weekly_period_ids['every_week'];
                            }

                            if ($is_group_busy['weekly_period'] == $weekly_period_ids['red_week']) {
                                $group_weekly_period = $weekly_period_ids['blue_week'];
                            } elseif ($is_group_busy['weekly_period'] == $weekly_period_ids['blue_week']) {
                                $group_weekly_period = $weekly_period_ids['red_week']; 
                            } else {
                                $group_weekly_period = $weekly_period_ids['every_week'];
                            }

                            if ($teacher_weekly_period == $group_weekly_period
                                || $teacher_weekly_period != $weekly_period_ids['every_week']) 
                            {
                                $free_weekly_period = $teacher_weekly_period;
                            } else {
                                $free_weekly_period = $group_weekly_period;
                            }
                                
                            $free_periods[$class_period->id][$week_day->id][$free_weekly_period] = true;
                        }
                        $is_teacher_busy = ['result' => false, 'weekly_period' => null];
                        $is_group_busy = ['result' => false, 'weekly_period' => null];
                    }
                }
            }
        }

        $data = [
            'free_periods' => $free_periods,
            'teacher_name' => $teacher->profession_level_name,
            'group_name' => $group->name,
            'lesson_name' => mb_strtolower($lesson->name),
            'lesson_week_day' => mb_strtolower($lesson->week_day->name),
            'lesson_weekly_period' => mb_strtolower($lesson->weekly_period->name),
            'lesson_class_period' => mb_strtolower($lesson->class_period->name),
            'class_periods' => array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()))
        ];

        return $data;
    }
}