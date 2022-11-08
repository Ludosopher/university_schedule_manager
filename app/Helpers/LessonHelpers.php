<?php

namespace App\Helpers;

use App\ClassPeriod;
use App\Group;
use App\Lesson;
use App\Teacher;
use App\WeekDay;
use App\WeeklyPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class LessonHelpers
{
    public static function getLessonsForReplacement($data)
    {
        $weekly_period_ids = config('enum.weekly_period_ids');
        $replacement_lessons = [];
        $groups_lessons = [];

        $replacing_lesson = Lesson::where('id', $data['lesson_id'])->first();
        $seeking_teacher = Teacher::where('id', $data['teacher_id'])->with(['lessons'])->first();
        $groups_ids = array_column($replacing_lesson->groups->toArray(), 'id');
        sort($groups_ids);

        $preliminary_lessons = Lesson::with(['groups', 'teacher.lessons'])->whereHas('groups', function (Builder $query) use ($groups_ids) {
            $query->whereIn('id', $groups_ids);
        })->get();
        foreach ($preliminary_lessons as $lesson) {
            $current_groups_ids = array_column($lesson->groups->toArray(), 'id');
            sort($current_groups_ids);
            if (count($current_groups_ids) == count($groups_ids)
                && $current_groups_ids === $groups_ids
                && $lesson->id != $data['lesson_id']) 
            {
                $groups_lessons[] = $lesson;
            }    
        }
        
        $is_suitable_teacher = true;
        $is_suitable_lesson = true;
        $looked_teachers = [$seeking_teacher->id];
        foreach ($groups_lessons as $g_lesson) {
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
                        $dt_lesson_groups_ids = array_column($dt_lesson->groups->toArray(), 'id');
                        sort($dt_lesson_groups_ids);
                        if (count($dt_lesson_groups_ids) == count($groups_ids) && $dt_lesson_groups_ids === $groups_ids) {
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
        
        $replacemented_lesson = Lesson::with(['week_day', 'weekly_period', 'class_period', 'teacher', 'groups'])->where('id', $prev_replace_rules['lesson_id'])->first();
        if ($replacemented_lesson) {
            $header_data = [
                'week_day' => mb_strtolower($replacemented_lesson->week_day->name),
                'weekly_period' => mb_strtolower($replacemented_lesson->weekly_period->name),
                'class_period' => mb_strtolower($replacemented_lesson->class_period->name),
                'teacher' => $replacemented_lesson->teacher->profession_level_name,
                'group' => $replacemented_lesson->groups_name,
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
        $lesson = Lesson::with(['groups.lessons'])->where('id', $data['lesson_id'])->first();
        $weekly_period_ids = config('enum.weekly_period_ids');
        $rescheduling_week_days_limit = config('site.rescheduling_week_days_limit');
        $rescheduling_class_periods_limit = config('site.rescheduling_class_periods_limit');
        $week_days = WeekDay::select('id', 'name')->get();
        $class_periods = ClassPeriod::get();

        $schedule_subjects[] = $teacher->lessons;
        foreach ($lesson->groups as $lesson_group) {
            $schedule_subjects[] = $lesson_group->lessons;
        }
        
        $free_periods = [];
        $is_free = $weekly_period_ids['every_week'];
        $result = $weekly_period_ids['every_week'];
        foreach ($week_days as $week_day) {
            if ($week_day->id <= $rescheduling_week_days_limit) {
                // echo '<pre>';
                // echo "***************************************************";
                // print_r(['week_day' => $week_day->name]);
                // echo "****************************************************";
                // echo '</pre>';
                foreach ($class_periods as $class_period) {
                    if ($class_period->id <= $rescheduling_class_periods_limit) {
                        // echo '<pre>';
                        // echo '////////////////////////////////////////////////';
                        // print_r(['class_period' => $class_period->name]);
                        // echo '////////////////////////////////////////////////';
                        // echo '</pre>';
                        foreach ($schedule_subjects as $key => $subject_lessons) {
                            // echo '<pre>';
                            // echo '|||||||||||||||||||||||||||||||||||||||||||||||||';
                            // print_r(['schedule_subject' => $key]);
                            // echo '|||||||||||||||||||||||||||||||||||||||||||||||||';
                            // echo '</pre>';
                            foreach ($subject_lessons as $lesson) {
                                // echo '<pre>';
                                // echo '--------------------------------------------------';
                                // print_r(['lesson' => $lesson->id, 'class_period' => $lesson->class_period->name, 'week_day' => $lesson->week_day->name, 'subject' => $lesson->name]);
                                // echo '---------------------------------------------------';
                                // echo '</pre>';
                                if ($lesson->week_day_id == $week_day->id
                                    && $lesson->class_period_id == $class_period->id)
                                {
                                    // echo '<pre>';
                                    // echo '====================================================';
                                    // print_r(['busy_lesson' => $lesson->id]);
                                    // echo '====================================================';
                                    // echo '</pre>';
                                    if ($lesson->weekly_period_id == $weekly_period_ids['every_week']) {
                                        $is_free = 'no';
                                        break;
                                    } elseif ($lesson->weekly_period_id == $weekly_period_ids['red_week']) {
                                        $is_free = $weekly_period_ids['blue_week']; 
                                    } else {
                                        $is_free = $weekly_period_ids['red_week'];
                                    }
                                }    
                            }
                            // echo '<pre>';
                            // print_r(['is_free' => $is_free]);
                            // echo '</pre>';
                            if ($is_free == 'no'
                                || $is_free == $weekly_period_ids['blue_week'] && $result == $weekly_period_ids['red_week']
                                || $is_free == $weekly_period_ids['red_week'] && $result == $weekly_period_ids['blue_week']) 
                            {
                                $result = false;
                                $is_free = $weekly_period_ids['every_week'];
                                break;
                            } else {
                                if (!$result || $result == $weekly_period_ids['every_week']) {
                                    $result = $is_free;
                                }
                                $is_free = $weekly_period_ids['every_week'];
                                // echo '<pre>';
                                // print_r(['new_is_free' => $is_free, 'result' => $result]);
                                // echo '</pre>';
                            }
                        }
                        // echo '<pre>';
                        // echo '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!';
                        // print_r(['class_period' => $class_period->id, 'week_day' => $week_day->id, 'result' => $result]);
                        // echo '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!';
                        // echo '</pre>';
                        if ($result) {
                            $free_periods[$class_period->id][$week_day->id][$result] = true;
                            // echo '<pre>';
                            // print_r(['free_periods' => $free_periods]);
                            // echo '</pre>'; 
                        }   
                        $is_free = $weekly_period_ids['every_week'];
                        $result = $weekly_period_ids['every_week'];
                    }
                }
                
            }
        }
// dd($free_periods);
        $data = [
            'free_periods' => $free_periods,
            'teacher_name' => $teacher->profession_level_name,
            'groups_name' => $lesson->groups_name,
            'lesson_name' => mb_strtolower($lesson->name),
            'lesson_week_day' => mb_strtolower($lesson->week_day->name),
            'lesson_weekly_period' => mb_strtolower($lesson->weekly_period->name),
            'lesson_class_period' => mb_strtolower($lesson->class_period->name),
            'class_periods' => array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()))
        ];

        return $data;
    }

    public static function addOrUpdateLessonGroups($group_ids, $id) {
        
        $lesson = Lesson::find($id);
        $lesson->groups()->sync($group_ids);
        
        return true;
    }

    public static function getGroupsData($data) {
        
        $updating_lesson = $data['updating_instance'];
        foreach ($updating_lesson->groups as $group) {
            $group_ids[] = $group->id;
        }
        $data['updating_instance']->group_id = $group_ids;

        return $data;
    }

    public static function deleteLessonGroupsRelation($id) {
        $lesson = Lesson::with(['groups'])->find($id);
        if ($lesson) {
            $lesson->groups()->detach();
            return true; 
        }

        return false;
    }
   
}