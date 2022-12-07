<?php

namespace App\Helpers;

use App\ClassPeriod;
use App\Lesson;
use App\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class ModelHelpers
{
    public static function addOrUpdateInstance($data, $model) {

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

    public static function getSchedule($incom_data) {

        $model_name = $incom_data['model_name'];
        $instance_name = $incom_data['instance_name'];
        $schedule_instance_id = $incom_data['schedule_instance_id'];
        $data['schedule_instance_id'] = $schedule_instance_id;
        $instance_name_field = $incom_data['instance_name_field'];
        $profession_level_name_field = $incom_data['profession_level_name_field'];
        $other_lesson_participant = $incom_data['other_lesson_participant'];
        $other_lesson_participant_name = $incom_data['other_lesson_participant_name'];
                        
        $week_dates = UniversalHelpers::weekDates($incom_data['week_number']);
        if ($week_dates) {
            $data['week_data'] = [
                'week_number' => $incom_data['week_number'],
                'start_date' => UniversalHelpers::weekDates($incom_data['week_number'])['start_date'],
                'end_date' => UniversalHelpers::weekDates($incom_data['week_number'])['end_date'],
            ];
        } else {
            $data['week_data'] = [
                'week_number' => $incom_data['week_number'],
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
            
            if (!UniversalHelpers::testDateLesson($incom_data['week_number'], $lesson)) {
                continue;
            };
            
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
}
