<?php

namespace App\Instances;

use App\Instances\Instance;
use App\Lesson;
use App\ClassPeriod;
use App\Group;
use App\Helpers\DateHelpers;
use App\Http\Controllers\TeacherController;
use App\Setting;
use App\Teacher;
use App\WeekDay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LessonInstance extends Instance
{
    protected $config = [
        'model_name' => 'App\Lesson',
        'instance_name' => 'lesson',
        'instance_plural_name' => 'lessons',
        'instance_name_field' => 'name',
        'profession_level_name_field' => null,
        'eager_loading_fields' => ['lesson_type', 'week_day', 'weekly_period', 'class_period', 'teacher', 'groups'],
        'other_lesson_participant' => null,
        'other_lesson_participant_name' => null,
        'boolean_attributes' => [],
        'many_to_many_attributes' => ['group_id' => 'groups'],
    ];

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

    public function getReschedulingData($incoming_data)
    {
        $settings = Setting::pluck('value', 'name');
        $study_periods_data = DateHelpers::getStudyPeriodsData();
        $required_study_period_id = (int)($incoming_data['study_period_id'] ?? $study_periods_data['current_period_id']);
        $data['required_study_period'] = DateHelpers::getRequiredStudyPeriod($study_periods_data['all_periods'], $required_study_period_id);
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
                'current_study_season' => DateHelpers::checkWeekToStudyPeriodSeason($data['required_study_period'], $week_number),
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

        $date_or_weekly_period = __('dictionary.'.$lesson->weekly_period->name);
        $reschedule_hours_diff = null;
        if (isset($incoming_data['rescheduling_lesson_date'])) {
            $date_or_weekly_period = date('d.m.y', strtotime(str_replace('"', '', $incoming_data['rescheduling_lesson_date'])));
            $reschedule_date_time = date('Y-m-d H:i', strtotime(str_replace('"', '', $incoming_data['rescheduling_lesson_date'])));
            $reschedule_hours_diff = round((strtotime($reschedule_date_time) - strtotime(now()))/3600);
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
                                if (! isset($week_number) && $sub_lesson->study_period_id !== $required_study_period_id) {
                                    continue;
                                }
                                if (isset($week_number) && isset($sub_lesson->study_period_id) && ! DateHelpers::checkRegularLessonToWeek($sub_lesson->study_period_id, $week_number)) {
                                    continue;
                                }
                                if (! DateHelpers::checkOneTimeLessonToWeek($week_number, $sub_lesson->date)) {
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

                                $check_lesson = $this->checkLesson($sub_lesson, $week_number);
                                if (is_object($check_lesson)) {
                                    $subject_lessons = $check_lesson;
                                }
                                if ($check_lesson) {
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
            'current_study_period_border_weeks' => DateHelpers::getCurrentStudyPeriodBorderWeeks(),
            'rescheduling_lesson_date' => $incoming_data['rescheduling_lesson_date'] ?? null,
            'date_or_weekly_period' => $date_or_weekly_period,
            'reschedule_hours_diff' => $reschedule_hours_diff
        ];

        return $data;
    }
}