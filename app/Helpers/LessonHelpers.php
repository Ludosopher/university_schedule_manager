<?php

namespace App\Helpers;

use App\ClassPeriod;
use App\Group;
use App\Http\Controllers\TeacherController;
use App\Lesson;
use App\Setting;
use App\Teacher;
use App\WeekDay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class LessonHelpers
{
    public static function getLessonsForReplacement($data, $week_number, $week_dates)
    {
        $weekly_period_ids = config('enum.weekly_period_ids');
        $replacement_lessons = [];
        $groups_lessons = [];
        $class_periods = ClassPeriod::get();
        $normalaze_class_periods = array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()));

        $replacing_lesson = Lesson::where('id', $data['lesson_id'])->first();
        $is_periodic_replacing_lesson = $replacing_lesson->weekly_period_id != $weekly_period_ids['every_week'];
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
                foreach ($g_lesson->teacher->lessons as $key => $dt_lesson) {
                    if ($dt_lesson->week_day_id == $data['week_day_id']
                        && ($dt_lesson->weekly_period_id == $data['weekly_period_id'] || $dt_lesson->weekly_period_id == $weekly_period_ids['every_week'] || $data['weekly_period_id'] == $weekly_period_ids['every_week'])
                        && $dt_lesson->class_period_id == $data['class_period_id'])
                    {
                        $is_suitable_teacher = false;
                        break;
                    }
                }
                $looked_teachers[] = $g_lesson->teacher->id;
                if ($is_suitable_teacher) {
                    foreach ($g_lesson->teacher->lessons as $dt_lesson) {
                        if (! DateHelpers::testLessonDate($week_number, $dt_lesson)) {
                            continue;
                        };
                        $week_schedule_lesson = DateHelpers::getWeeklyScheduleLesson($week_number, $dt_lesson);
                        $w_p_field = 'weekly_period_id';
                        if (isset($week_schedule_lesson)) {
                            if ($week_schedule_lesson) {
                                $dt_lesson = $week_schedule_lesson;
                                $w_p_field = 'real_weekly_period_id';
                            } else {
                                continue;
                            }
                        }
                        if ($dt_lesson->$w_p_field == $weekly_period_ids['every_week'] && $is_periodic_replacing_lesson
                            || $dt_lesson->$w_p_field != $weekly_period_ids['every_week'] && ! $is_periodic_replacing_lesson)
                        {
                            continue;
                        }
                        if (isset($week_dates) && is_array($week_dates[$dt_lesson->weekly_period_id]) && isset($week_dates[$dt_lesson->weekly_period_id]['is_holiday'])) {
                            continue;
                        }
                        $dt_lesson_groups_ids = array_column($dt_lesson->groups->toArray(), 'id');
                        sort($dt_lesson_groups_ids);
                        if (count($dt_lesson_groups_ids) == count($groups_ids) && $dt_lesson_groups_ids === $groups_ids) {
                            foreach ($seeking_teacher->lessons as $st_lesson) {
                                if (! DateHelpers::testLessonDate($week_number, $st_lesson)) {
                                    continue;
                                };
                                $week_schedule_lesson = DateHelpers::getWeeklyScheduleLesson($week_number, $st_lesson);
                                if (isset($week_schedule_lesson)) {
                                    if ($week_schedule_lesson) {
                                        $st_lesson = $week_schedule_lesson;
                                    } else {
                                        continue;
                                    }
                                }
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
                                
                                $replacing_date_time = null;
                                $replacing_hours_diff = null;
                                if (isset($week_dates) && ! is_array($week_dates[$dt_lesson->week_day->id])) {
                                    $lesson_date = $week_dates[$dt_lesson->week_day->id];
                                    $replacing_date = date('Y-m-d', strtotime(str_replace('"', '', $lesson_date)));
                                    $class_period_start_time = date('H:i', strtotime($normalaze_class_periods[$dt_lesson->class_period->id]['start']));
                                    $replacing_date_time = date('Y-m-d '.$class_period_start_time, strtotime(str_replace('"', '', $lesson_date)));
                                    $replacing_hours_diff = round((strtotime($replacing_date_time) - strtotime(now()))/3600);
                                }
                                                                
                                $replacement_lessons[] = [
                                    'lesson_id' => $dt_lesson->id,
                                    'subject' => $dt_lesson->name,
                                    'week_day_id' => ['id' => $dt_lesson->week_day->id, 'name' => $dt_lesson->week_day->name],
                                    'date' => $g_lesson->date ?? null,
                                    'weekly_period_id' => ['id' => $dt_lesson->weekly_period->id, 'name' => $dt_lesson->weekly_period->name],
                                    'class_period_id' => ['id' => $dt_lesson->class_period->id, 'name' => $dt_lesson->class_period->name],
                                    'lesson_room_id' => ['id' => $dt_lesson->lesson_room->id, 'name' => $dt_lesson->lesson_room->number],
                                    'department_id' => ['id' => $g_lesson->teacher->department->id, 'name' => $g_lesson->teacher->department->name],
                                    'position_id' => ['id' => $g_lesson->teacher->position->id, 'name' => $g_lesson->teacher->position->name],
                                    'lesson_type' => $dt_lesson->lesson_type->short_notation,
                                    'lesson_room' => $dt_lesson->lesson_room->number,
                                    'groups_name' => $dt_lesson->groups_name,
                                    'teacher_id' => $g_lesson->teacher->id,
                                    'profession_level_name' => $g_lesson->teacher->profession_level_name,
                                    'phone' => $g_lesson->teacher->phone,
                                    'age' => $g_lesson->teacher->age,
                                    'schedule_position_id' => self::getLessonSchedulePosition($replacing_lesson, $g_lesson->teacher),
                                    'replacing_date_time' => $replacing_date_time, 
                                    'replacing_hours_diff' => $replacing_hours_diff,
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
            $result = ['id' => 1, 'name' => 'between_two_available_pairs'];
        } elseif ($is_prev_lesson || $is_next_lesson) {
            $result = ['id' => 2, 'name' => 'next_to_one_of_available_pairs'];
        } else {
            $result = ['id' => 3, 'name' => 'there_are_no_pairs_available_nearby'];
        }

        return $result;
    }

    public static function getReplacementData($incoming_data)
    {
        $settings = Setting::pluck('value', 'name');
        $class_periods_limit = $settings['full_time_class_periods_limit'] ?? config('site.class_periods_limits')['full_time'];
        $week_days_limit = $settings['full_time_week_days_limit'] ?? config('site.week_days_limits')['full_time'];
        $replacement_lessons = [];
        $header_data = [];
        $date_or_weekly_period = '';

        if (isset($incoming_data['week_number'])) {
            $week_number = $incoming_data['week_number'];
            $week_dates = DateHelpers::weekDates($week_number);
            $week_border_dates = DateHelpers::weekStartEndDates($week_number);
            $is_red_week = DateHelpers::weekColorIsRed($week_number);
            $week_data = [
                'week_number' => $week_number,
                'start_date' => $week_border_dates['start_date'],
                'end_date' => $week_border_dates['end_date'],
            ];
            $class_periods_limit = $settings['distance_class_periods_limit'] ?? config('site.class_periods_limits')['distance'];
            $week_days_limit = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
        } elseif (isset($incoming_data['week_data'])) {
            $week_data = json_decode($incoming_data['week_data'], true);
            $week_number = $week_data['week_number'];
            $week_dates = json_decode($incoming_data['week_dates'], true);
            $is_red_week = $incoming_data['is_red_week'];
        } else {
            $week_data = null;
            $week_number = null;
            $week_dates = null;
            $is_red_week = null;
        }

        if (!isset($incoming_data['replace_rules'])) {
            if (isset($incoming_data['prev_replace_rules'])) {
                $prev_replace_rules = json_decode($incoming_data['prev_replace_rules'], true);
                $replacement_lessons = LessonHelpers::getLessonsForReplacement($prev_replace_rules, $week_number, $week_dates);
            }
        } else {
            $replacement_lessons = LessonHelpers::getLessonsForReplacement($incoming_data['replace_rules'], $week_number, $week_dates);
            $prev_replace_rules = $incoming_data['replace_rules'];
        }

        $filtered_replacement_lessons = FilterHelpers::getFilteredArrayOfArrays($replacement_lessons, $incoming_data);

        $replacemented_lesson = Lesson::with(['week_day', 'weekly_period', 'class_period', 'teacher', 'groups'])->where('id', $prev_replace_rules['lesson_id'])->first();
        if ($replacemented_lesson) {
            $header_data = [
                'week_day' => mb_strtolower($replacemented_lesson->week_day->name),
                'weekly_period' => mb_strtolower($replacemented_lesson->weekly_period->name),
                'class_period' => mb_strtolower($replacemented_lesson->class_period->name),
                'teacher' => $replacemented_lesson->teacher->profession_level_name,
                'group' => $replacemented_lesson->groups_name,
            ];
            $date_or_weekly_period = __('dictionary.'.$replacemented_lesson->weekly_period->name);
        }

        $replaceable_date_time = null;
        $replaceable_hours_diff = null;
        if (isset($prev_replace_rules['date'])) {
            $date_or_weekly_period = date('d.m.y', strtotime(str_replace('"', '', $prev_replace_rules['date'])));
            $replaceable_date_time = date('Y-m-d H:i', strtotime(str_replace('"', '', $prev_replace_rules['date'])));
            $replaceable_hours_diff = round((strtotime($replaceable_date_time) - strtotime(now()))/3600);
        }

        $data = [
            'replacement_lessons' => $filtered_replacement_lessons,
            'table_properties' => config("tables.replacement_variants"),
            'filter_form_fields' => config("forms.lesson_replacement_filter"),
            'prev_replace_rules' => $prev_replace_rules,
            'header_data' => $header_data,
            'week_data' => $week_data,
            'week_dates' => $week_dates,
            'is_red_week' => $is_red_week,
            'initiator_id' => Auth::user()->id,
            'user_teachers_ids' => Auth::user()->teachers->pluck('id')->toArray(),
            'date_or_weekly_period' => $date_or_weekly_period,
            'replaceable_date_time' => $replaceable_date_time,
            'replaceable_hours_diff' => $replaceable_hours_diff,
            'week_day_ids' => config('enum.week_day_ids'),
            'weekly_periods' => config('enum.weekly_periods'),
            'weekly_period_ids' => config('enum.weekly_period_ids'),
            'weekly_period_colors' => config('enum.weekly_period_colors'),
            'class_period_ids' => config('enum.class_period_ids'),
            'week_days_limit' => $week_days_limit,
            'class_periods_limit' => $class_periods_limit,
        ];

        return array_merge($data, DictionaryHelpers::getReplacementProperties());
    }

    public static function getReschedulingData($incoming_data) 
    {
        $settings = Setting::pluck('value', 'name');
        $class_periods_limit = $settings['full_time_class_periods_limit'] ?? config('site.class_periods_limits')['full_time'];
        $week_days_limit = $settings['full_time_week_days_limit'] ?? config('site.week_days_limits')['full_time'];
        $teacher = Teacher::with(['lessons'])->where('id', $incoming_data['teacher_id'])->first();
        $lesson = Lesson::with(['groups.lessons'])->where('id', $incoming_data['lesson_id'])->first();
        $weekly_period_ids = config('enum.weekly_period_ids');
        $week_days = WeekDay::select('id', 'name')->get();
        $class_periods = ClassPeriod::get();
        
        if (isset($incoming_data['week_data']) && isset($incoming_data['week_data']['week_number'])) {
            $week_data = json_decode($incoming_data['week_data'], true);
            $week_number = $week_data['week_number'];
            $week_dates = json_decode($incoming_data['week_dates'], true);
            $is_red_week = $incoming_data['is_red_week'];
        } elseif (isset($incoming_data['week_number'])) {
            $week_number = $incoming_data['week_number'];
            $week_dates = DateHelpers::weekDates($week_number);
            $week_border_dates = DateHelpers::weekStartEndDates($week_number);
            $is_red_week = DateHelpers::weekColorIsRed($week_number);
            $week_data = [
                'week_number' => $week_number,
                'start_date' => $week_border_dates['start_date'],
                'end_date' => $week_border_dates['end_date'],
            ];
            $class_periods_limit = $settings['distance_class_periods_limit'] ?? config('site.class_periods_limits')['distance'];
            $week_days_limit = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
        } else {
            $week_data = null;
            $week_number = null;
            $week_dates = null;
            $is_red_week = null;
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
                foreach ($class_periods as $class_period) {
                    if ($class_period->id <= $class_periods_limit) {
                        foreach ($schedule_subjects as $key => $subject_lessons) {
                            foreach ($subject_lessons as $sub_lesson) {
                                if (! DateHelpers::testLessonDate($week_number, $sub_lesson)) {
                                    continue;
                                };
                                $week_schedule_lesson = DateHelpers::getWeeklyScheduleLesson($week_number, $sub_lesson);
                                if (isset($week_schedule_lesson)) {
                                    if ($week_schedule_lesson) {
                                        $sub_lesson = $week_schedule_lesson;
                                    } else {
                                        continue;
                                    }
                                }
                                if ($sub_lesson->week_day_id == $week_day->id
                                    && $sub_lesson->class_period_id == $class_period->id)
                                {
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
                            }
                        }
                        if ($result) {
                            $free_periods[$class_period->id][$week_day->id][$result] = true;
                        }
                        $is_free = $weekly_period_ids['every_week'];
                        $result = $weekly_period_ids['every_week'];
                    }
                }

            }
        }

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
            'week_data' => $week_data,
            'week_dates' => $week_dates,
            'is_red_week' => $is_red_week,
            'week_day_ids' => config('enum.week_day_ids'),
            'weekly_period' => config('enum.weekly_periods'),
            'weekly_period_id' => config('enum.weekly_period_ids'),
            'weekly_period_color' => config('enum.weekly_period_colors'),
            'class_period_ids' => config('enum.class_period_ids'),
            'week_days_limit' => $week_days_limit,
            'class_periods_limit' => $class_periods_limit,
            'free_weekly_period_colors' => config('enum.free_weekly_period_colors'),
        ];

        return $data;
    }

    public static function getReplacementSchedule($teacher_id, $incom_replacement_data, $incom_data) {

        $class_periods = ClassPeriod::get();
        $week_days = WeekDay::get();

        $data_for_schedule["schedule_teacher_id"] = $teacher_id;
        $data_for_schedule["week_number"] = isset($incom_data['week_data']) ? json_decode($incom_data['week_data'], true)['week_number'] 
                                                                            : (isset($incom_data['week_number']) ? $incom_data['week_number']
                                                                                                                 : null);
        $teacher_controller = new TeacherController();
        $schedule_data = ModelHelpers::getSchedule($data_for_schedule, $teacher_controller->config);

        foreach ($incom_replacement_data['replacement_lessons'] as $lesson) {
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
                'replacing_date_time' => $lesson['replacing_date_time'],
                'replacing_hours_diff' => $lesson['replacing_hours_diff'],
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

        $data['week_dates'] = (array)$incom_replacement_data['week_dates'];
        $data['is_red_week'] = $incom_replacement_data['is_red_week'];

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
