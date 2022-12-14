<?php

namespace App\Helpers;

use App\ClassPeriod;
use App\Group;
use App\Lesson;
use App\Teacher;
use App\WeekDay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class ModelHelpers
{
    public static function addOrUpdate($data, $model) {

        if (isset($data['updating_id'])) {
            $instance = $model::where('id', $data['updating_id'])->first();
        } else {
            $instance = new $model();
        }

        $db_fields = Schema::getColumnListing($instance->getTable());

        foreach ($db_fields as $field) {
            if (isset($data[$field]) && $instance->$field != $data[$field]) {
                $instance->$field = $data[$field];
            }
        }

        $instance->save();

        return $instance;
    }

    public static function deleteInstance($id, $model) {
        $deleting_instance = $model::where('id', $id)->first();
        if ($deleting_instance) {
            $model::where('id', $id)->delete();
            return $deleting_instance;
        }

        return false;
    }

    public static function getAppends($data) {
        $appends = [];
        foreach ($data as $key => $value) {
            if ($key != '_token'
                && $key != 'deleted_instance_name'
                && $key != 'deleting_instance_not_found'
                && $key != 'updated_instance_name'
                && $value != null)
            {
                $appends[$key] = $value;
            }
        }

        return $appends;
    }

    public static function getSchedule($incoming_data, $config) {

        $model_name = $config['model_name'];
        $instance_name = $config['instance_name'];
        $schedule_instance_id = $incoming_data["schedule_{$config['instance_name']}_id"];
        $data['schedule_instance_id'] = $schedule_instance_id;
        $instance_name_field = $config['instance_name_field'];
        $profession_level_name_field = $config['profession_level_name_field'];
        $other_lesson_participant = $config['other_lesson_participant'];
        $other_lesson_participant_name = $config['other_lesson_participant_name'];
        
        $week_number = null;
        if (isset($incoming_data['week_number'])) {
            $week_number = $incoming_data['week_number'];
            $data['is_red_week'] = UniversalHelpers::weekColorIsRed($week_number);
            $data['week_dates'] = UniversalHelpers::weekDates($week_number);
        }
        
        $week_border_dates = UniversalHelpers::weekStartEndDates($week_number);
        if ($week_border_dates) {
            $data['week_data'] = [
                'week_number' => $week_number,
                'start_date' => $week_border_dates['start_date'],
                'end_date' => $week_border_dates['end_date'],
            ];
        } else {
            $data['week_data'] = [
                'week_number' => $week_number,
                'start_date' => null,
                'end_date' => null,
            ];
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

            if (! UniversalHelpers::testLessonDate($week_number, $lesson)) {
                continue;
            };
            
            $week_schedule_lesson = UniversalHelpers::getWeeklyScheduleLesson($week_number, $lesson);
            if (isset($week_schedule_lesson)) {
                if ($week_schedule_lesson) {
                    $lesson = $week_schedule_lesson;
                } else {
                    continue;
                }
            }

            if (isset($data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$lesson->weekly_period_id])
                || isset($data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$weekly_period_ids['every_week']]))
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
                    'week_day_id' => $lesson->week_day_id,
                    'weekly_period_id' => $lesson->weekly_period_id,
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

        return $data;
    }

    public static function getMonthSchedule($incoming_data, $config) {

        $model_name = $config['model_name'];
        $instance_name = $config['instance_name'];
        $schedule_instance_id = $incoming_data["schedule_{$config['instance_name']}_id"];
        // $data['schedule_instance_id'] = $schedule_instance_id;
        $instance_name_field = $config['instance_name_field'];
        $profession_level_name_field = $config['profession_level_name_field'];
        $other_lesson_participant = $config['other_lesson_participant'];
        $other_lesson_participant_name = $config['other_lesson_participant_name'];
        
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
        
        $month_week_numbers = UniversalHelpers::getMonthWeekNumbers($incoming_data['month_number']);
        
        $momth_value = date('n', strtotime($incoming_data['month_number']));
        $months_genitive = config('enum.months');
        $data['month_name'] = date("{$months_genitive[$momth_value]} Y").' ????????';

        foreach ($month_week_numbers as $week_number) {
            
            $data['weeks'][$week_number]['is_red_week'] = UniversalHelpers::weekColorIsRed($week_number);
            $data['weeks'][$week_number]['week_dates'] = UniversalHelpers::weekDates($week_number);
            
            
            $week_border_dates = UniversalHelpers::weekStartEndDates($week_number);
            $data['weeks'][$week_number]['week_data'] = [
                'week_number' => $week_number,
                'start_date' => $week_border_dates['start_date'],
                'end_date' => $week_border_dates['end_date'],
            ];
            
            $data['weeks'][$week_number]['lessons'] = [];
            $iterated_lessons = $lessons->toArray();
           
            foreach ($iterated_lessons as $key => $lesson) {

                if (! UniversalHelpers::testLessonDate($week_number, $lessons[$key])) {
                    continue;
                };
                
                $week_schedule_lesson = UniversalHelpers::getMonthWeeklyScheduleLesson($week_number, $lesson);
                if (isset($week_schedule_lesson)) {
                    if ($week_schedule_lesson) {
                        $lesson = $week_schedule_lesson;
                    } else {
                        continue;
                    }
                }
    
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

        return $data;
    }

    public static function getInstanceFormData ($incoming_data, $config)
    {
        $model_name = $config['model_name'];
        $data = $model_name::getProperties();
        $data['add_form_fields'] = config("forms.{$config['instance_name']}");
        if (isset($incoming_data['updating_id'])) {
            $updating_instance = $model_name::where('id', $incoming_data['updating_id'])->first();
            if ($updating_instance) {
                $data = array_merge($data, ['updating_instance' => $updating_instance]);
            }
        }
        if (isset($incoming_data['new_instance_name'])) {
            $data = array_merge($data, ['new_instance_name' => $incoming_data['new_instance_name']]);
        }

        return $data;
    }

    public static function addOrUpdateInstance ($data, $config)
    {
        $instance_name_field = $config['instance_name_field'];
        $model_name = $config['model_name'];

        $instance = self::addOrUpdate($data, $model_name);
        if (isset($data['updating_id'])) {
            return ['id' => $instance->id, 'updated_instance_name' => $instance->$instance_name_field];
        } else {
            return ['id' => $instance->id, 'new_instance_name' => $instance->$instance_name_field];
        }
    }

    public static function getInstances ($incoming_data, $config)
    {
        $rows_per_page = config('site.rows_per_page');
        $model_name = $config['model_name'];

        if (count($incoming_data)) {
            request()->flash();
        }

        $data['table_properties'] = config("tables.{$config['instance_plural_name']}");
        $data['filter_form_fields'] = config()->has("forms.{$config['instance_name']}_filter") ? config("forms.{$config['instance_name']}_filter") : [];
        $properties = $model_name::getProperties();

        if (!isset($incoming_data['sort'])) {
            if (isset($incoming_data['deleted_instance_name'])) {
                $data['deleted_instance_name'] = $incoming_data['deleted_instance_name'];
            }
            if (isset($incoming_data['deleting_instance_not_found'])) {
                $data['deleting_instance_not_found'] = true;
            }
            if (isset($incoming_data['updated_instance_name'])) {
                $data['updated_instance_name'] = $incoming_data['updated_instance_name'];
            }
            if (isset($incoming_data['duplicated_lesson'])) {
                $data['duplicated_lesson'] = $incoming_data['duplicated_lesson'];
            }
            if (isset($incoming_data['there_are_lessons_only_with_this_group'])) {
                $data['there_are_lessons_only_with_this_group'] = $incoming_data['there_are_lessons_only_with_this_group'];
            }
        }

        $instances = FilterHelpers::getFilteredQuery($model_name::with($config['eager_loading_fields']), $incoming_data, $config['instance_name']);
        $appends = self::getAppends($incoming_data);
        $data['instances'] = $instances->sortable()->paginate($rows_per_page)->appends($appends);

        return array_merge($data, $properties);
    }

    public static function getModelRechedulingData($incoming_data, $reschedule_data, $config) {

        $class_periods = ClassPeriod::get();
        $week_days = WeekDay::get();
        $rescheduling_lesson = Lesson::where('id', $incoming_data['lesson_id'])->first();
        $incoming_data["schedule_{$config['instance_name']}_id"] = $incoming_data["{$config['instance_name']}_id"];

        $schedule_data = ModelHelpers::getSchedule($incoming_data, $config);
        if (isset($schedule_data['duplicated_lesson'])) {
            return $schedule_data;
        }
        $schedule_lessons = $schedule_data['lessons'] ?? [];

        $data = [
            'rescheduling_lesson_id' => $rescheduling_lesson->id,
            'class_periods' => $class_periods,
            'other_lesson_participant_name' => $config['other_lesson_participant'],
            'teacher_name' => $rescheduling_lesson->teacher->profession_level_name,
            'teacher_id' => $rescheduling_lesson->teacher->id,
            'group_id' => $incoming_data['group_id'] ?? null
        ];

        $data['week_dates'] = $reschedule_data['week_dates'];
        $data['is_red_week'] = $reschedule_data['is_red_week'];
        $data['week_data'] = $reschedule_data['week_data'];

        // $week_number = null;
        // if (isset($incoming_data['week_number'])) {
        //     $week_number = $incoming_data['week_number'];
        //     $data['is_red_week'] = UniversalHelpers::weekColorIsRed($week_number);
        //     $data['week_dates'] = UniversalHelpers::weekDates($week_number);
        // }
        // $week_border_dates = UniversalHelpers::weekStartEndDates($week_number);
        // if ($week_border_dates) {
        //     $data['week_data'] = [
        //         'week_number' => $week_number,
        //         'start_date' => $week_border_dates['start_date'],
        //         'end_date' => $week_border_dates['end_date'],
        //     ];
        // } else {
        //     $data['week_data'] = [
        //         'week_number' => $week_number,
        //         'start_date' => null,
        //         'end_date' => null,
        //     ];
        // }

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

    // public static function addOrUpdateManyToMany($model_id, $model_name, $attribute_ids, $attribute_name) {

    //     $model = $model_name::find($model_id);
    //     $model->$attribute_name()->sync($attribute_ids);

    //     return true;
    // }

    public static function addOrUpdateManyToManyAttributes($data, $model_id, $model_name, $attributes) {

        foreach ($attributes as $field => $attribute) {
            if (isset($data[$field])) {
                $model = $model_name::find($model_id);
                $model->$attribute()->sync($data[$field]);
            }
        }
        return true;
    }

    public static function deleteManyToManyAttributes($model_id, $model_name, $attributes) {
        $model = $model_name::with($attributes)->find($model_id);
        if ($model) {
            foreach ($attributes as $attribute) {
                $model->$attribute()->detach();
            }
            return true;
        }
        return false;
    }

    public static function getManyToManyData($data, $attributes) {

        $instance = $data['updating_instance'];
        foreach ($attributes as $field => $attribute) {
            $attribute_ids = [];
            foreach ($instance->$attribute as $elem) {
                $attribute_ids[] = $elem->id;
            }
            $data['updating_instance']->$field = $attribute_ids;
        }
        return $data;
    }

}
