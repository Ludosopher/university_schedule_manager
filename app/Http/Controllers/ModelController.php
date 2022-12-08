<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Group;
use App\Helpers\FilterHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\UniversalHelpers;
use App\Lesson;
use App\Teacher;
use App\WeekDay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModelController extends Controller
{
    public function getInstances ($incoming_data)
    {
        $rows_per_page = config('site.rows_per_page');
                
        if (request()->method() == 'POST') {
            request()->flash();
        }
        
        $data['table_properties'] = config("tables.{$this->instance_plural_name}");
        $data['filter_form_fields'] = config("forms.{$this->instance_name}_filter");
        $properties = $this->model_name::getProperties();
        
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
        
        $instances = FilterHelpers::getFilteredQuery($this->model_name::with($this->eager_loading_fields), $incoming_data, $this->instance_name);
        $appends = ModelHelpers::getAppends($incoming_data);
        
        $data['instances'] = $instances->sortable()->paginate($rows_per_page)->appends($appends);
        
        return array_merge($data, $properties);
    }

    public function getInstanceFormData (Request $request)
    {
        $data = $this->model_name::getProperties();
        $data['add_form_fields'] = config("forms.{$this->instance_name}");
        if (isset($request->updating_id)) {
            $updating_instance = $this->model_name::where('id', $request->updating_id)->first();
            if ($updating_instance) {
                $data = array_merge($data, ['updating_instance' => $updating_instance]);
            }
        }
        if (isset($request->new_instance_name)) {
            $data = array_merge($data, ['new_instance_name' => $request->new_instance_name]);
        }

        return $data;
    }

    public function addOrUpdateInstance ($data)
    {
        $instance_name_field = $this->instance_name_field;
        
        // $data = $this->model_name::getProperties();
        // $data['filter_form_fields'] = $this->model_name::getFilterFormFields();
        // $data['add_form_fields'] = $this->model_name::getAddFormFields();
        
        $instance = ModelHelpers::addOrUpdateInstance($data, $this->model_name);
        if (isset($data['updating_id'])) {
            return ['id' => $instance->id, 'updated_instance_name' => $instance->$instance_name_field];
        } else {
            return ['id' => $instance->id, 'new_instance_name' => $instance->$instance_name_field];
        }
    }

    public function getSchedule ($incoming_data)
    {
        $schedule_instance_id_field = "schedule_{$this->instance_name}_id";
        $data = [
            'model_name' => $this->model_name,
            'instance_name' => $this->instance_name,
            'schedule_instance_id' => $incoming_data[$schedule_instance_id_field],
            'instance_name_field' => $this->instance_name_field,
            'profession_level_name_field' => $this->profession_level_name_field,
            'other_lesson_participant' => $this->other_lesson_participant,
            'other_lesson_participant_name' => $this->other_lesson_participant_name,
            'week_number' => $incoming_data['week_number'] ?? null
        ];

        return ModelHelpers::getSchedule($data);
        
    }

    public function getModelRechedulingData(Request $request, $reschedule_periods) {

        $class_periods = ClassPeriod::get();
        $week_days = WeekDay::get();
        $rescheduling_lesson = Lesson::where('id', $request->lesson_id)->first();
        
        $schedule_instance_id_field = "{$this->instance_name}_id";
        $incom_data = [
            'model_name' => $this->model_name,
            'instance_name' => $this->instance_name,
            'schedule_instance_id' => $request->$schedule_instance_id_field,
            'instance_name_field' => $this->instance_name_field,
            'profession_level_name_field' => $this->profession_level_name_field,
            'other_lesson_participant' => $this->other_lesson_participant,
            'other_lesson_participant_name' => $this->other_lesson_participant_name,
            'week_number' => $request->week_number ?? null
        ];
        $schedule_data = ModelHelpers::getSchedule($incom_data);
        if (isset($schedule_data['duplicated_lesson'])) {
            return $schedule_data;
        }
        $schedule_lessons = $schedule_data['lessons'] ?? [];
        
        $data = [
            'rescheduling_lesson_id' => $rescheduling_lesson->id,
            'class_periods' => $class_periods,
            'other_lesson_participant_name' => $this->other_lesson_participant,
            // 'lesson_name' => $rescheduling_lesson->name,
            // 'lesson_week_day' => $rescheduling_lesson->week_day->name,
            // 'lesson_weekly_period' => $rescheduling_lesson->weekly_period->name,
            // 'lesson_class_period' => $rescheduling_lesson->class_period->name,
            'teacher_name' => $rescheduling_lesson->teacher->profession_level_name,
            'teacher_id' => $rescheduling_lesson->teacher->id,
            'group_id' => $request->group_id ?? null
            // 'groups_name' => $rescheduling_lesson->groups_name
        ];

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

        if (isset($request->group_id)) {
            $data['group_name'] = Group::find($request->group_id)->name;
        }

        foreach ($class_periods as $class_period) {
            foreach ($week_days as $week_day) {
                if (isset($schedule_lessons[$class_period->id][$week_day->id])) {
                    $this_lessons = $schedule_lessons[$class_period->id][$week_day->id];
                    foreach ($this_lessons as $weekly_period_id => $this_lesson) {
                        $data['periods'][$class_period->id][$week_day->id][$weekly_period_id] = $this_lesson;
                    }
                }
                if (isset($reschedule_periods[$class_period->id][$week_day->id])) {
                    $this_periods = $reschedule_periods[$class_period->id][$week_day->id];
                    foreach ($this_periods as $weekly_period_id => $this_period) {
                        $data['periods'][$class_period->id][$week_day->id][$weekly_period_id] = $this_period;
                    }
                }
            }    
        }
    
        return $data;
    }

}
