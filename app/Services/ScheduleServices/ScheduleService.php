<?php

namespace App\Services\ScheduleServices;

use App\Services\Service;
use App\ClassPeriod;
use App\Group;
use App\Helpers\DateHelpers;
use App\Lesson;
use App\Setting;
use App\WeekDay;
use Illuminate\Database\Eloquent\Builder;
use Log;

class ScheduleService extends Service
{
    /** @var array $config */
    protected $config;
 
    /**
     * Get week schedule data.
     */
    public function getSchedule(array $incoming_data): array
    {
        $settings = Setting::pluck('value', 'name');
        $study_periods_data = DateHelpers::getStudyPeriodsData();
        $study_seasons = $data['study_seasons'] = config('enum.study_seasons');
        $data['week_day_ids'] = config('enum.week_day_ids');
        $data['weekly_periods'] = config('enum.weekly_periods');
        $data['weekly_period_ids'] = config('enum.weekly_period_ids');
        $data['weekly_period_colors'] = config('enum.weekly_period_colors');
        $data['class_period_ids'] = config('enum.class_period_ids');
        $data['study_periods'] = $study_periods_data['all_periods'];
        $required_study_period_id = (int)($incoming_data['study_period_id'] ?? $study_periods_data['current_period_id']);
        $data['required_study_period'] = DateHelpers::getRequiredStudyPeriod($study_periods_data['all_periods'], $required_study_period_id);
        $model_name = $this->config['model_name'];
        $instance_name = $data['appelation'] = $this->config['instance_name'];
        $schedule_instance_id = $incoming_data["schedule_{$this->config['instance_name']}_id"];
        $data['schedule_instance_id'] = $schedule_instance_id;
        $instance_name_field = $this->config['instance_name_field'];
        $profession_level_name_field = $this->config['profession_level_name_field'];
        $other_lesson_participant = $data['other_appelation'] = $this->config['other_lesson_participant'];
        $other_lesson_participant_name = $this->config['other_lesson_participant_name'];
       
        $week_number = null;
        if (isset($incoming_data['week_number'])) {
            $week_number = $incoming_data['week_number'];
            $data['is_red_week'] = DateHelpers::weekColorIsRed($week_number);
            $data['week_dates'] = DateHelpers::weekDates($week_number);
        }
        
        $week_border_dates = DateHelpers::weekStartEndDates($week_number);
        if ($week_border_dates) {
            $data['week_data'] = [
                'current_study_season' => DateHelpers::checkWeekToStudyPeriodSeason($data['required_study_period'], $week_number),
                'week_number' => $week_number,
                'start_date' => $week_border_dates['start_date'],
                'end_date' => $week_border_dates['end_date'],
            ];
            $data['week_days_limit'] = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
            $data['class_periods_limit'] = $settings['distance_class_periods_limit'] ?? config('site.class_periods_limits')['distance'];
        } else {
            $data['week_data'] = [
                'week_number' => $week_number,
                'start_date' => null,
                'end_date' => null,
            ];
            $data['week_days_limit'] = $settings['full_time_week_days_limit'] ?? config('site.week_days_limits')['full_time'];
            $data['class_periods_limit'] = $settings['full_time_class_periods_limit'] ?? config('site.class_periods_limits')['full_time'];
        }

        $weekly_period_ids = config('enum.weekly_period_ids');
        $class_periods = ClassPeriod::get();
        $data['class_periods'] = array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()));

        $instance = $model_name::where('id', $schedule_instance_id)->first();
        if ($instance) {
            $data['instance_name'] = $profession_level_name_field !== null ? $instance->$profession_level_name_field : $instance->$instance_name_field;
        }

        if ($instance_name == 'group') {
            $lessons = Lesson::with(['week_day', 'weekly_period', 'class_period', 'lesson_room', 'groups'])->whereHas('groups', function (Builder $query) use ($schedule_instance_id) {
                $query->where('id', $schedule_instance_id);
            })->get();
        } else {
            $lessons = Lesson::with(['lesson_type', $instance_name, 'week_day', 'weekly_period', 'class_period', 'lesson_room'])
                             ->where("{$instance_name}_id", $schedule_instance_id)
                             ->get();
        }

        $data['lessons'] = [];
        foreach ($lessons as $lesson) {
            $check_lesson = $this->checkLesson($lesson, $week_number, '', null, null, $required_study_period_id);
            if (!is_bool($check_lesson)) {
                $lesson = $check_lesson;
            }
            if ($check_lesson) {
                if ((isset($data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$lesson->weekly_period_id]) ||
                 isset($data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$weekly_period_ids['every_week']]))
                 && $data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$lesson->weekly_period_id]['study_period_id'] === $lesson->study_period_id
                 && $lesson->study_period_id === $required_study_period_id)
                {
                    $data['duplicated_lesson'] = [
                        $instance_name => $instance->$instance_name_field,
                        'class_period' => $lesson->class_period->name,
                        'week_day' => $lesson->week_day->name,
                        'weekly_period' => $lesson->weekly_period->name
                    ];
                    return $data;
                } else {
                    if (is_array($other_lesson_participant_name)) {
                        $value = $lesson;
                        foreach ($other_lesson_participant_name as $part) {
                            $value = $value->$part;
                            if (!is_object($value)) {
                                break;
                            }
                        }
                    } else {
                        $value = $lesson->$other_lesson_participant_name;
                    }

                    $data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$lesson->weekly_period_id] = [
                        'id' => $lesson->id,
                        'study_period_id' => $lesson->study_period_id,
                        'week_day_id' => $lesson->week_day_id,
                        'weekly_period_id' => $lesson->weekly_period_id,
                        'real_weekly_period_id' => $lesson->real_weekly_period_id ?? null,
                        'class_period_id' => $lesson->class_period_id,
                        'teacher_id' => $lesson->teacher_id,
                        'type' => $lesson->lesson_type->short_notation,
                        'name' => $lesson->name,
                        'room' => $lesson->lesson_room->number,
                        'date' => isset($lesson->date) ? date('d.m.y', strtotime($lesson->date)) : null,
                        $other_lesson_participant => $value
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * Get month schedule data.
     */
    public function getMonthSchedule(array $incoming_data): array 
    {
        $model_name = $this->config['model_name'];
        $instance_name = $data['appelation'] = $this->config['instance_name'];
        $schedule_instance_id = $incoming_data["schedule_{$this->config['instance_name']}_id"];
        $data['schedule_instance_id'] = $schedule_instance_id;
        $instance_name_field = $this->config['instance_name_field'];
        $profession_level_name_field = $this->config['profession_level_name_field'];
        $other_lesson_participant = $data['other_appelation'] = $this->config['other_lesson_participant'];
        $other_lesson_participant_name = $this->config['other_lesson_participant_name'];
        $settings = Setting::pluck('value', 'name');
        $data['class_periods_limit'] = $settings['distance_class_periods_limit'] ?? config('site.class_periods_limits')['distance'];
        $data['week_days_limit'] = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
        $data['week_day_ids'] = config('enum.week_day_ids');
        $data['weekly_periods'] = config('enum.weekly_periods');
        $data['weekly_period_ids'] = config('enum.weekly_period_ids');
        $data['weekly_period_colors'] = config('enum.weekly_period_colors');
        $data['class_period_ids'] = config('enum.class_period_ids');
        $study_seasons = $data['study_seasons'] = config('enum.study_seasons');
        $data['month_number'] = $incoming_data['month_number'];
        $study_periods_data = DateHelpers::getStudyPeriodsData();
        $required_study_period_id = (int)($incoming_data['study_period_id'] ?? $study_periods_data['current_period_id']);
        $data['required_study_period'] = DateHelpers::getRequiredStudyPeriod($study_periods_data['all_periods'], $required_study_period_id);
                
        $weekly_period_ids = config('enum.weekly_period_ids');
        $class_periods = ClassPeriod::get();
        $data['class_periods'] = array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()));
    
        $instance = $model_name::where('id', $schedule_instance_id)->first();
        if ($instance) {
            $data['instance_name'] = $profession_level_name_field !== null ? $instance->$profession_level_name_field : $instance->$instance_name_field;
        }
    
        if ($instance_name == 'group') {
            $lessons = Lesson::with(['week_day', 'weekly_period', 'class_period', 'lesson_room', 'groups'])->whereHas('groups', function (Builder $query) use ($schedule_instance_id) {
                $query->where('id', $schedule_instance_id);
            })->get();
        } else {
            $lessons = Lesson::with(['lesson_type', $instance_name, 'week_day', 'weekly_period', 'class_period', 'lesson_room'])
                             ->where("{$instance_name}_id", $schedule_instance_id)
                             ->get();
        }
        
        $month_week_numbers = DateHelpers::getMonthWeekNumbers($incoming_data['month_number']);
        
        $month_value = date('n', strtotime($incoming_data['month_number']));
        $months_genitive = config('enum.months');

        $data['month_name'] = __('header.'.$months_genitive[$month_value]).' '.date('Y', strtotime($incoming_data['month_number'])).' '.__('header.of_year');

        foreach ($month_week_numbers as $week_number) {
            
            $data['weeks'][$week_number]['is_red_week'] = DateHelpers::weekColorIsRed($week_number);
            $data['weeks'][$week_number]['week_dates'] = DateHelpers::weekDates($week_number);
                                    
            $week_border_dates = DateHelpers::weekStartEndDates($week_number);
            $data['weeks'][$week_number]['week_data'] = [
                'current_study_season' => DateHelpers::checkWeekToStudyPeriodSeason($data['required_study_period'], $week_number),
                'week_number' => $week_number,
                'start_date' => $week_border_dates['start_date'],
                'end_date' => $week_border_dates['end_date'],
            ];
            
            $data['weeks'][$week_number]['lessons'] = [];
            $iterated_lessons = $lessons->toArray();
            foreach ($iterated_lessons as $key => $lesson) {
                $check_lesson = $this->checkLesson($lesson, $week_number);
                if (!is_bool($check_lesson)) {
                    $lesson = $check_lesson;
                }
                                
                if ($check_lesson) {
                    if (isset($data['weeks'][$week_number]['lessons'][$lesson['class_period_id']][$lesson['week_day_id']][$lesson['weekly_period_id']])
                        || isset($data['weeks'][$week_number]['lessons'][$lesson['class_period_id']][$lesson['week_day_id']][$weekly_period_ids['every_week']]))
                    {
                        $data['duplicated_lesson'] = [
                            $instance_name => $instance->$instance_name_field,
                            'class_period' => $lessons[$key]->class_period->name,
                            'week_day' => $lessons[$key]->week_day->name,
                            'weekly_period' => $lessons[$key]->weekly_period->name
                        ];
                        return $data;
                    } else {
        
                        if (is_array($other_lesson_participant_name)) {
                            $value = $lessons[$key];
                            foreach ($other_lesson_participant_name as $part) {
                                $value = $value->$part;
                                if (!is_object($value)) {
                                    break;
                                }
                            }
                        } else {
                            $value = $lessons[$key]->$other_lesson_participant_name;
                        }
        
                        $data['weeks'][$week_number]['lessons'][$lesson['class_period_id']][$lesson['week_day_id']][$lesson['weekly_period_id']] = [
                            'id' => $lesson['id'],
                            'week_day_id' => $lesson['week_day_id'],
                            'weekly_period_id' => $lesson['weekly_period_id'],
                            'class_period_id' => $lesson['class_period_id'],
                            'teacher_id' => $lesson['teacher_id'],
                            'type' => $lessons[$key]->lesson_type->short_notation,
                            'name' => $lesson['name'],
                            'room' => $lessons[$key]->lesson_room->number,
                            'date' => isset($lesson['date']) ? date('d.m.y', strtotime($lesson['date'])) : null,
                            $other_lesson_participant => $value
                        ];
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Get reschedule data for a model subject.
     */
    public function getModelRechedulingData(array $incoming_data, array $reschedule_data): array 
    {
        $class_periods = ClassPeriod::get();
        $week_days = WeekDay::get();
        $rescheduling_lesson = Lesson::where('id', $incoming_data['lesson_id'])->first();
        $incoming_data["schedule_{$this->config['instance_name']}_id"] = $incoming_data["{$this->config['instance_name']}_id"];

        $schedule_data = $this->getSchedule($incoming_data);
        if (isset($schedule_data['duplicated_lesson'])) {
            return $schedule_data;
        }
        $schedule_lessons = $schedule_data['lessons'] ?? [];

        $data = [
            'rescheduling_lesson_id' => $rescheduling_lesson->id,
            'class_periods' => $class_periods,
            'other_lesson_participant_name' => $this->config['other_lesson_participant'],
            'teacher_name' => $rescheduling_lesson->teacher->profession_level_name,
            'teacher_id' => $rescheduling_lesson->teacher->id,
            'group_id' => $incoming_data['group_id'] ?? null,
            'week_day_ids' => $schedule_data['week_day_ids'],
            'weekly_periods' => $schedule_data['weekly_periods'],
            'weekly_period_ids' => $schedule_data['weekly_period_ids'],
            'weekly_period_colors' => $schedule_data['weekly_period_colors'],
            'class_period_ids' => $schedule_data['class_period_ids'],
            'week_days_limit' => $schedule_data['week_days_limit'],
            'class_periods_limit' => $schedule_data['class_periods_limit'],
            'appelation' => $this->config['instance_name'],
            'rescheduling_lesson_date' => $incoming_data['rescheduling_lesson_date'] ?? null,
        ];

        $data['week_dates'] = $reschedule_data['week_dates'];
        $data['is_red_week'] = $reschedule_data['is_red_week'];
        $data['week_data'] = $reschedule_data['week_data'];
        $data['current_study_period_border_weeks'] = DateHelpers::getCurrentStudyPeriodBorderWeeks();

        if (isset($incoming_data['group_id'])) {
            $data['group_name'] = Group::find($incoming_data['group_id'])->name;
        }

        foreach ($class_periods as $class_period) {
            foreach ($week_days as $week_day) {
                if (isset($schedule_lessons[$class_period->id][$week_day->id])) {
                    $this_lessons = $schedule_lessons[$class_period->id][$week_day->id];
                    foreach ($this_lessons as $weekly_period_id => $this_lesson) {
                        $data['periods'][$class_period->id][$week_day->id][$weekly_period_id] = $this_lesson;
                    }
                }
                if (isset($reschedule_data['free_periods'][$class_period->id][$week_day->id])) {
                    $this_periods = $reschedule_data['free_periods'][$class_period->id][$week_day->id];
                    foreach ($this_periods as $weekly_period_id => $this_period) {
                        $data['periods'][$class_period->id][$week_day->id][$weekly_period_id] = $this_period;
                    }
                }
            }
        }

        return $data;
    }
    
}