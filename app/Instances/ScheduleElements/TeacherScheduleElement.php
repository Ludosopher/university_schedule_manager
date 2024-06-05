<?php

namespace App\Instances\ScheduleElements;

use App\ClassPeriod;
use App\Helpers\DateHelpers;
use App\Helpers\DictionaryHelpers;
use App\Instances\ScheduleElements\ScheduleElement;
use App\Lesson;
use App\Setting;
use App\Teacher;
use App\WeekDay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TeacherScheduleElement extends ScheduleElement
{
    protected $config = [
        'model_name' => 'App\Teacher',
        'instance_name' => 'teacher',
        'instance_plural_name' => 'teachers',
        'instance_name_field' => 'full_name',
        'profession_level_name_field' => 'profession_level_name',
        'eager_loading_fields' => ['faculty', 'department', 'professional_level', 'position'],
        'other_lesson_participant' => 'group',
        'other_lesson_participant_name' => 'groups_name',
        'boolean_attributes' => [],
        'many_to_many_attributes' => [],
    ];

    protected function getLessonsForReplacement($data, $week_number, $week_dates)
    {
        $weekly_period_ids = config('enum.weekly_period_ids');
        $study_seasons = config('enum.study_seasons');
        $study_periods_data = DateHelpers::getStudyPeriodsData();
        $required_study_period_id = (int)($data['study_period_id'] ?? $study_periods_data['current_period_id']);
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
                foreach ($g_lesson->teacher->lessons as $dt_lesson) {
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
                        if (! isset($week_number) && $dt_lesson->study_period_id !== $required_study_period_id) {
                            continue;
                        }
                        if (isset($week_number) && isset($dt_lesson->study_period_id) && ! DateHelpers::checkRegularLessonToWeek($dt_lesson, $week_number)) {
                            continue;
                        }
                        if (! DateHelpers::checkOneTimeLessonToWeek($week_number, $dt_lesson)) {
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
                                if (! isset($week_number) && $st_lesson->study_period_id !== $required_study_period_id) {
                                    continue;
                                }
                                if (isset($week_number) && isset($st_lesson->study_period_id) && ! DateHelpers::checkRegularLessonToWeek($st_lesson, $week_number)) {
                                    continue;
                                }
                                if (! DateHelpers::checkOneTimeLessonToWeek($week_number, $st_lesson)) {
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
                                    'schedule_position_id' => $this->getLessonSchedulePosition($replacing_lesson, $g_lesson->teacher),
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

    protected function getLessonSchedulePosition ($lesson, $teacher) {

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

    public function getReplacementData($incoming_data)
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
                //$replacement_lessons = $this->getLessonsForReplacement($prev_replace_rules, $week_number, $week_dates);
                $replacement_lessons = $this->getReplacementLessons($prev_replace_rules, $week_number, $week_dates);
            }
        } else {
            //$replacement_lessons = $this->getLessonsForReplacement($incoming_data['replace_rules'], $week_number, $week_dates);
            $replacement_lessons = $this->getReplacementLessons($incoming_data['replace_rules'], $week_number, $week_dates);
            $prev_replace_rules = $incoming_data['replace_rules'];
        }

        $filtered_replacement_lessons = $this->getFilteredArrayOfArrays($replacement_lessons, $incoming_data);

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

    public function getReplacementSchedule($teacher_id, $incom_replacement_data, $incom_data) {

        $class_periods = ClassPeriod::get();
        $week_days = WeekDay::get();
        $data_for_schedule["schedule_teacher_id"] = $teacher_id;
        $data_for_schedule["week_number"] = isset($incom_data['week_number']) ? $incom_data['week_number'] 
                                                                              : (isset(json_decode($incom_data['week_data'], true)['week_number']) ? json_decode($incom_data['week_data'], true)['week_number']
                                                                                                                                                   : null);
        $schedule_data = $this->getSchedule($data_for_schedule);
        
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
        $data['current_study_period_border_weeks'] = DateHelpers::getCurrentStudyPeriodBorderWeeks();

        return $data;
    }

    public function getReplacingTeacherSchedule($data) {
        
        $weekly_period_ids = config('enum.weekly_period_ids');
        $incom_replacing_data = false;

        $incom_replaceable_data = [
            'schedule_teacher_id' => $data['replacing_teacher_id'], 
            'week_number' => null,
        ];
        
        if (! $data['is_regular'] && isset($data['replaceable_date']) && isset($data['replacing_date'])) {
            $incom_replaceable_data = [
                'schedule_teacher_id' => $data['replacing_teacher_id'], 
                'week_number' => DateHelpers::getWeekNumberFromDate($data['replaceable_date']),
            ];
            if (date('W', strtotime($data['replaceable_date'])) !== date('W', strtotime($data['replacing_date']))) {
                $incom_replacing_data = [
                    'schedule_teacher_id' => $data['replacing_teacher_id'], 
                    'week_number' => DateHelpers::getWeekNumberFromDate($data['replacing_date']),
                ];
                $replacing_schedule_data = $this->getSchedule($incom_replacing_data);
            }
        }
                
        $replaceable_schedule_data = $this->getSchedule($incom_replaceable_data);
        
        $replaceable_lesson = Lesson::with(['lesson_type', 'lesson_room', 'teacher', 'class_period', 'week_day', 'weekly_period'])
                                    ->where('id', $data['replaceable_lesson_id'])
                                    ->first();
        $replacing_lesson = Lesson::with(['lesson_type', 'lesson_room', 'teacher', 'teacher.users', 'class_period', 'week_day', 'weekly_period'])
                                   ->where('id', $data['replacing_lesson_id'])
                                   ->first();

        if ($data['is_regular']) {
            $replaceable_weekly_period_id = $replaceable_lesson->weekly_period_id;
            $replacing_weekly_period_id = $replacing_lesson->weekly_period_id;
            $replaceable_lesson_description = __('mail.regular_replaceable_lesson_description').$replacing_lesson->name.'", '.__('dictionary.'.$replaceable_lesson->weekly_period->name).', '.__('dictionary.'.$replaceable_lesson->week_day->name).', '.__('dictionary.'.$replaceable_lesson->class_period->name).' '.__('header.class_period');
            $replacing_lesson_description = __('mail.regular_replacing_lesson_descript').$replaceable_lesson->name.'", '.__('dictionary.'.$replaceable_lesson->weekly_period->name).', '.__('dictionary.'.$replaceable_lesson->week_day->name).', '.__('dictionary.'.$replaceable_lesson->class_period->name).' '.__('header.class_period');
        } else {
            $replaceable_date = '';
            if (isset($replaceable_schedule_data['week_dates'])) {
                $replaceable_date = date('d.m.Y', strtotime($replaceable_schedule_data['week_dates'][$replaceable_lesson->week_day_id]));
            }
            if ($incom_replacing_data) {
                $replacing_date = '';
                if (isset($replacing_schedule_data['week_dates'])) {
                    $replacing_date = date('d.m.Y', strtotime($replacing_schedule_data['week_dates'][$replacing_lesson->week_day_id]));
                }
            } else {
                $replacing_date = date('d.m.Y', strtotime($replaceable_schedule_data['week_dates'][$replacing_lesson->week_day_id]));
            }
            $replaceable_weekly_period_id = $weekly_period_ids['every_week'];
            $replacing_weekly_period_id = $weekly_period_ids['every_week'];
            $replaceable_lesson_description = __('mail.dated_replaceable_lesson_description').$replaceable_date.', "'.$replacing_lesson->name.'", '.__('dictionary.'.$replaceable_lesson->week_day->name).', '.__('dictionary.'.$replaceable_lesson->class_period->name).' '.__('header.class_period');
            $replacing_lesson_description = __('mail.dated_replacing_lesson_descript').$replacing_date.', "'.$replaceable_lesson->name.'", '.__('dictionary.'.$replaceable_lesson->week_day->name).', '.__('dictionary.'.$replaceable_lesson->class_period->name).' '.__('header.class_period');
        }

        $mails_to = [];
        if (env('is_testing') === true) {
            $mails_to[] = env('testing_email');
        } else {
            foreach ($replacing_lesson->teacher->users as $user) {
                $mails_to[] = $user->email;
            }
        }
        
        $result = [
            'mails_to' => $mails_to,
            'addressee_name' => $replacing_lesson->teacher->first_name_patronymic,
            'requester_name' => $replaceable_lesson->teacher->profession_level_name,
            'replaceable_lesson_description' => $replaceable_lesson_description,
            'replacing_lesson_description' => $replacing_lesson_description,
            'group' => $replaceable_lesson->groups_name,
        ];

        if (! isset($replaceable_schedule_data['lessons'][$replaceable_lesson->class_period_id][$replaceable_lesson->week_day_id][$replaceable_lesson->weekly_period_id])
            && ! isset($replaceable_schedule_data['lessons'][$replaceable_lesson->class_period_id][$replaceable_lesson->week_day_id][$weekly_period_ids['every_week']]))
        {
            $replaceable_schedule_data['lessons'][$replaceable_lesson->class_period_id][$replaceable_lesson->week_day_id][$replaceable_weekly_period_id] = [
                'id' => $replaceable_lesson->id,
                'week_day_id' => $replaceable_lesson->week_day_id,
                'weekly_period_id' => $replaceable_weekly_period_id,
                'class_period_id' => $replaceable_lesson->class_period_id,
                'teacher_id' => $replaceable_lesson->teacher_id,
                'teacher_name' => $replaceable_lesson->teacher->profession_level_name,
                'type' => $replaceable_lesson->lesson_type->short_notation,
                'name' => $replaceable_lesson->name,
                'room' => $replaceable_lesson->lesson_room->number,
                'date' => $replaceable_lesson->date,
                'is_replaceable' => true,
            ];

            if (! $incom_replacing_data) {
                $replaceable_schedule_data['lessons'][$replacing_lesson->class_period_id][$replacing_lesson->week_day_id][$replacing_weekly_period_id]['is_replacing'] = true;
                $result['schedule_data'] = [
                    'replaceable' => $replaceable_schedule_data,
                ];
            } else {
                $replacing_schedule_data['lessons'][$replacing_lesson->class_period_id][$replacing_lesson->week_day_id][$replacing_weekly_period_id]['is_replacing'] = true;
                $result['schedule_data'] = [
                    'replaceable' => $replaceable_schedule_data,
                    'replacing' => $replacing_schedule_data,
                ];
            }

            return $result;
        }

        return false;
    }

    protected function getGroupsLessons($groups_ids, $replacing_lesson, $preliminary_lessons)
    {
        $groups_lessons = [];    
        foreach ($preliminary_lessons as $lesson) {
            $current_groups_ids = array_column($lesson->groups->toArray(), 'id');
            sort($current_groups_ids);
            if (count($current_groups_ids) == count($groups_ids)
                && $current_groups_ids === $groups_ids
                && $lesson->id != $replacing_lesson->id)
            {
                $groups_lessons[] = $lesson;
            }
        }
        
        return $groups_lessons;
    }

    protected function getReplacementLessons($data, $week_number, $week_dates)
    {
        $replacement_lessons = [];
        $weekly_period_ids = config('enum.weekly_period_ids');
        $class_periods = ClassPeriod::get();
        $normalaze_class_periods = array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()));
        $replaceble_lesson = Lesson::find($data['lesson_id']);
        $replacement_requester = Teacher::with(['lessons'])->find($data['teacher_id']);
        $groups_ids = $replaceble_lesson->groups->pluck('id')->toArray();
        sort($groups_ids);
        $study_periods_data = DateHelpers::getStudyPeriodsData();
                
        $unsuitable_teachers = array_unique(Lesson::where('week_day_id', $replaceble_lesson->week_day_id)
                                                    ->where('class_period_id', $replaceble_lesson->class_period_id)
                                                    ->pluck('teacher_id')
                                                    ->toArray());
        
        $preliminary_lessons = Lesson::with(['groups'])
                                        ->whereNotIn('teacher_id', $unsuitable_teachers)
                                        ->where('teacher_id', '!=', $replacement_requester->id)
                                        ->where('study_period_id', $study_periods_data['current_period_id'])
                                        ->whereHas('groups', function (Builder $query) use ($groups_ids) {
                                            $query->whereIn('id', $groups_ids);
                                        })->get();
        
        $replaceble_groups_lessons = $this->getGroupsLessons($groups_ids, $replaceble_lesson, $preliminary_lessons);
        $is_suitable_less = true;
        foreach($replaceble_groups_lessons as $verified_lesson) {
            $check_lesson = $this->checkLesson($verified_lesson, $week_number, 'additionally', $replaceble_lesson, $week_dates);
            if (is_object($check_lesson)) {
                $verified_lesson = $check_lesson;
            }
            if ($check_lesson) {
                foreach($replacement_requester->lessons as $verification_lesson) {
                    $check_lesson = $this->checkLesson($verification_lesson, $week_number);
                    if (is_object($check_lesson)) {
                        $verification_lesson = $check_lesson;
                    }
                    if ($check_lesson) {
                        if ($verification_lesson->week_day_id == $verified_lesson->week_day_id
                            && ($verification_lesson->weekly_period_id == $verified_lesson->weekly_period_id
                                || $verified_lesson->weekly_period_id == $weekly_period_ids['every_week']
                                || $verification_lesson->weekly_period_id == $weekly_period_ids['every_week'])
                            && $verification_lesson->class_period_id == $verified_lesson->class_period_id) 
                        {
                            $is_suitable_less = false;
                            break;
                        }
                    }
                }
                if ($is_suitable_less) {
                    $replacing_date_time = null;
                    $replacing_hours_diff = null;
                    if (isset($week_dates) && ! is_array($week_dates[$verification_lesson->week_day->id])) {
                        $lesson_date = $week_dates[$verification_lesson->week_day->id];
                        $replacing_date = date('Y-m-d', strtotime(str_replace('"', '', $lesson_date)));
                        $class_period_start_time = date('H:i', strtotime($normalaze_class_periods[$verification_lesson->class_period->id]['start']));
                        $replacing_date_time = date('Y-m-d '.$class_period_start_time, strtotime(str_replace('"', '', $lesson_date)));
                        $replacing_hours_diff = round((strtotime($replacing_date_time) - strtotime(now()))/3600);
                    }

                    $replacement_lessons[] = [
                        'lesson_id' => $verified_lesson->id,
                        'subject' => $verified_lesson->name,
                        'week_day_id' => ['id' => $verified_lesson->week_day->id, 'name' => $verified_lesson->week_day->name],
                        'date' => $verified_lesson->date ?? null,
                        'weekly_period_id' => ['id' => $verified_lesson->weekly_period->id, 'name' => $verified_lesson->weekly_period->name],
                        'class_period_id' => ['id' => $verified_lesson->class_period->id, 'name' => $verified_lesson->class_period->name],
                        'lesson_room_id' => ['id' => $verified_lesson->lesson_room->id, 'name' => $verified_lesson->lesson_room->number],
                        'department_id' => ['id' => $verified_lesson->teacher->department->id, 'name' => $verified_lesson->teacher->department->name],
                        'position_id' => ['id' => $verified_lesson->teacher->position->id, 'name' => $verified_lesson->teacher->position->name],
                        'lesson_type' => $verified_lesson->lesson_type->short_notation,
                        'lesson_room' => $verified_lesson->lesson_room->number,
                        'groups_name' => $verified_lesson->groups_name,
                        'teacher_id' => $verified_lesson->teacher->id,
                        'profession_level_name' => $verified_lesson->teacher->profession_level_name,
                        'phone' => $verified_lesson->teacher->phone,
                        'age' => $verified_lesson->teacher->age,
                        'schedule_position_id' => $this->getLessonSchedulePosition($replaceble_lesson, $verified_lesson->teacher),
                        'replacing_date_time' => $replacing_date_time, 
                        'replacing_hours_diff' => $replacing_hours_diff,
                    ];
                }
                $is_suitable_less = true;
            }
        }
       
        return $replacement_lessons;
    }
}