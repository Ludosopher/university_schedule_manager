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
    public static function getLessonsForReplacement($data, $week_number)
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
            if (!UniversalHelpers::testDateLesson($week_number, $g_lesson)) {
                continue;
            };
            if (!in_array($g_lesson->teacher->id, $looked_teachers)) {
                foreach ($g_lesson->teacher->lessons as $dt_lesson) {
                    if (!UniversalHelpers::testDateLesson($week_number, $dt_lesson)) {
                        continue;
                    };
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
                        if (!UniversalHelpers::testDateLesson($week_number, $dt_lesson)) {
                            continue;
                        };
                        $dt_lesson_groups_ids = array_column($dt_lesson->groups->toArray(), 'id');
                        sort($dt_lesson_groups_ids);
                        if (count($dt_lesson_groups_ids) == count($groups_ids) && $dt_lesson_groups_ids === $groups_ids) {
                            foreach ($seeking_teacher->lessons as $st_lesson) {
                                if (!UniversalHelpers::testDateLesson($week_number, $st_lesson)) {
                                    continue;
                                };
                                if ($st_lesson->week_day_id == $dt_lesson->week_day_id
                                    && ($st_lesson->weekly_period_id == $dt_lesson->weekly_period_id
                                        || $st_lesson->weekly_period_id == $weekly_period_ids['every_week']
                                        || $dt_lesson->weekly_period_id == $weekly_period_ids['every_week'])
                                    && $st_lesson->class_period_id == $dt_lesson->class_period_id)
                                {
                                    $is_suitable_lesson = false;
                                    break;
                                }
                            }
                            if ($is_suitable_lesson) {
                                $replacement_lessons[] = [
                                    'lesson_id' => $g_lesson->id,
                                    'subject' => $dt_lesson->name,
                                    'week_day_id' => ['id' => $dt_lesson->week_day->id, 'name' => $dt_lesson->week_day->name],
                                    'date' => $g_lesson->date ?? null,
                                    'weekly_period_id' => ['id' => $dt_lesson->weekly_period->id, 'name' => $dt_lesson->weekly_period->name],
                                    'class_period_id' => ['id' => $dt_lesson->class_period->id, 'name' => $dt_lesson->class_period->name],
                                    'lesson_room_id' => ['id' => $dt_lesson->lesson_room->id, 'name' => $dt_lesson->lesson_room->number],
                                    'department_id' => ['id' => $g_lesson->teacher->department->id, 'name' => $g_lesson->teacher->department->name],
                                    'position_id' => ['id' => $g_lesson->teacher->position->id, 'name' => $g_lesson->teacher->position->name],
                                    'lesson_type' => $g_lesson->lesson_type->short_notation,
                                    'lesson_room' => $g_lesson->lesson_room->number,
                                    'groups_name' => $g_lesson->groups_name,
                                    'teacher_id' => $g_lesson->teacher->id,
                                    'profession_level_name' => $g_lesson->teacher->profession_level_name,
                                    'phone' => $g_lesson->teacher->phone,
                                    'age' => $g_lesson->teacher->age,
                                    'schedule_position_id' => self::getLessonSchedulePosition($replacing_lesson, $g_lesson->teacher),
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

        $week_data = null;
        $week_number = null;
        if (isset($data['week_data'])) {
            $week_data = json_decode($data['week_data'], true);
            $week_number = $week_data['week_number'];
        }

        if (!isset($data['replace_rules'])) {
            if (isset($data['prev_replace_rules'])) {
                $prev_replace_rules = json_decode($data['prev_replace_rules'], true);
                $replacement_lessons = LessonHelpers::getLessonsForReplacement($prev_replace_rules, $week_number);
            }
        } else {
            $replacement_lessons = LessonHelpers::getLessonsForReplacement($data['replace_rules'], $week_number);
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
            'filter_form_fields' => config("forms.lesson_replacement_filter"),
            'prev_replace_rules' => $prev_replace_rules,
            'header_data' => $header_data,
            'week_data' => $week_data
        ];

        return array_merge($data, Lesson::getReplacementProperties());
    }

    public static function getReschedulingData($data) {

        $teacher = Teacher::with(['lessons'])->where('id', $data['teacher_id'])->first();
        $lesson = Lesson::with(['groups.lessons'])->where('id', $data['lesson_id'])->first();
        $weekly_period_ids = config('enum.weekly_period_ids');
        $week_days_limits = config('site.week_days_limits');
        $class_periods_limits = config('site.class_periods_limits');
        $week_days = WeekDay::select('id', 'name')->get();
        $class_periods = ClassPeriod::get();
        
        if (isset($data['week_data'])) {
            $week_data = json_decode($data['week_data'], true);
            $week_number = $week_data['week_number'];
            $class_periods_limit = $class_periods_limits['distance'];
            $week_days_limit = $week_days_limits['distance'];
        } else {
            $week_data = null;
            $week_number = null;
            $class_periods_limit = $class_periods_limits['full_time'];
            $week_days_limit = $week_days_limits['full_time'];
        }
        

        $schedule_subjects[] = $teacher->lessons;
        foreach ($lesson->groups as $lesson_group) {
            $schedule_subjects[] = $lesson_group->lessons;
            $groups_ids_names[] = [
                'id' => $lesson_group->id,
                'name' => $lesson_group->name
            ];
        }

        $free_periods = [];
        $is_free = $weekly_period_ids['every_week'];
        $result = $weekly_period_ids['every_week'];
        foreach ($week_days as $week_day) {
            if ($week_day->id <= $week_days_limit) {
                // echo '<pre>';
                // echo "***************************************************";
                // print_r(['week_day' => $week_day->name]);
                // echo "****************************************************";
                // echo '</pre>';
                foreach ($class_periods as $class_period) {
                    if ($class_period->id <= $class_periods_limit) {
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
                            foreach ($subject_lessons as $sub_lesson) {
                                if (!UniversalHelpers::testDateLesson($week_number, $sub_lesson)) {
                                    continue;
                                };
                                // echo '<pre>';
                                // echo '--------------------------------------------------';
                                // print_r(['lesson' => $sub_lesson->id, 'class_period' => $sub_lesson->class_period->name, 'week_day' => $sub_lesson->week_day->name, 'subject' => $sub_lesson->name]);
                                // echo '---------------------------------------------------';
                                // echo '</pre>';
                                if ($sub_lesson->week_day_id == $week_day->id
                                    && $sub_lesson->class_period_id == $class_period->id)
                                {
                                    // echo '<pre>';
                                    // echo '====================================================';
                                    // print_r(['busy_lesson' => $sub_lesson->id]);
                                    // echo '====================================================';
                                    // echo '</pre>';
                                    if ($sub_lesson->weekly_period_id == $weekly_period_ids['every_week']) {
                                        $is_free = 'no';
                                        break;
                                    } elseif ($sub_lesson->weekly_period_id == $weekly_period_ids['red_week']) {
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
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->profession_level_name,
            'groups_ids_names' => $groups_ids_names,
            'groups_name' => $lesson->groups_name,
            'lesson_id' => $lesson->id,
            'lesson_name' => mb_strtolower($lesson->name),
            'lesson_week_day' => mb_strtolower($lesson->week_day->name),
            'lesson_weekly_period' => mb_strtolower($lesson->weekly_period->name),
            'lesson_class_period' => mb_strtolower($lesson->class_period->name),
            'class_periods' => array_combine(range(1, count($class_periods)), array_values($class_periods->toArray())),
            'week_data' => $week_data
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

    public static function getReplacementSchedule($teacher_id, $incom_replacement_lessons, $week_data) {

        $class_periods = ClassPeriod::get();
        $week_days = WeekDay::get();

        $incom_data = [
            'model_name' => 'App\Teacher',
            'instance_name' => 'teacher',
            'schedule_instance_id' => $teacher_id,
            'instance_name_field' => 'full_name',
            'profession_level_name_field' => 'profession_level_name',
            'other_lesson_participant' => 'group',
            'other_lesson_participant_name' => 'groups_name',
            'week_number' => isset($week_data) ? json_decode($week_data, true)['week_number'] : null
        ];

        $schedule_data = ModelHelpers::getSchedule($incom_data);

        foreach ($incom_replacement_lessons as $lesson) {
            $replacement_lessons[$lesson['class_period_id']['id']][$lesson['week_day_id']['id']][$lesson['weekly_period_id']['id']] = [
                'id' => $lesson['lesson_id'],
                'week_day_id' => $lesson['week_day_id']['id'],
                'weekly_period_id' => $lesson['weekly_period_id']['id'],
                'class_period_id' => $lesson['class_period_id']['id'],
                'teacher_id' => $lesson['teacher_id'],
                'type' => $lesson['lesson_type'],
                'room' => $lesson['lesson_room'],
                'name' => $lesson['subject'],
                'group' => $lesson['groups_name'],
                'teacher' => $lesson['profession_level_name'],
                'for_replacement' => true
            ];
        }

        $schedule_lessons = $schedule_data['lessons'];

        foreach ($class_periods as $class_period) {
            foreach ($week_days as $week_day) {
                if (isset($schedule_lessons[$class_period->id][$week_day->id])) {
                    $this_lessons = $schedule_lessons[$class_period->id][$week_day->id];
                    foreach ($this_lessons as $weekly_period_id => $this_lesson) {
                        $data[$class_period->id][$week_day->id][$weekly_period_id] = $this_lesson;
                    }
                }
                if (isset($replacement_lessons[$class_period->id][$week_day->id])) {
                    $this_lessons = $replacement_lessons[$class_period->id][$week_day->id];
                    foreach ($this_lessons as $weekly_period_id => $this_lesson) {
                        $data[$class_period->id][$week_day->id][$weekly_period_id] = $this_lesson;
                    }
                }
            }
        }

        return $data;
    }

    public static function searchSameLesson($data, $field) {
        
        $weekly_period_ids = config('enum.weekly_period_ids');
        
        $like_lessons_query = Lesson::where([
                                 ['week_day_id', $data['week_day_id']],
                                 ['class_period_id', $data['class_period_id']]
                              ])->where(function ($query) use($data, $weekly_period_ids) {
                                    $query->orWhere('weekly_period_id', $data['weekly_period_id'])
                                          ->orWhere('weekly_period_id', $weekly_period_ids['every_week']);
                                 });

        $is_dublicate = false;
        switch ($field) {
            case 'group_id':
                $like_group_lessons = $like_lessons_query->get();
                $groups_ids = $data['group_id'];
                sort($groups_ids);
                $is_dublicate = false;
                foreach ($like_group_lessons as $lesson) {
                    $current_groups_ids = array_column($lesson->groups->toArray(), 'id');
                    sort($current_groups_ids);
                    if (count($current_groups_ids) == count($groups_ids)
                        && $current_groups_ids == $groups_ids)
                    {
                        $is_dublicate = true;
                        break;
                    }
                }
                break;
            case 'teacher_id':
                $is_dublicate = $like_lessons_query->where('teacher_id', $data['teacher_id'])->first();
                break;
            case 'lesson_room_id':
                $is_dublicate = $like_lessons_query->where('lesson_room_id', $data['lesson_room_id'])->first();
                break; 
        }
        
        return $is_dublicate;
    }

}
