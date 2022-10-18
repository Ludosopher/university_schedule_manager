<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Helpers\FilterHelpers;
use App\Helpers\ModelHelpers;
use App\Lesson;
use App\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModelController extends Controller
{
    public function getInstances (Request $request)
    {
        $rows_per_page = config('site.rows_per_page');
                
        $request->flash();
        $data['table_properties'] = config("tables.{$this->instance_plural_name}");
        $data['filter_form_fields'] = $this->model_name::getFilterFormFields();
        $properties = $this->model_name::getProperties();
        
        if (!isset($request->sort)) {
            if (isset($request->deleted_instance_name)) {
                $data['deleted_instance_name'] = $request->deleted_instance_name;    
            }
            if (isset($request->deleting_instance_not_found)) {
                $data['deleting_instance_not_found'] = true;   
            }
            if (isset($request->updated_instance_name)) {
                $data['updated_instance_name'] = $request->updated_instance_name;    
            }
            if (isset($request->duplicated_lesson)) {
                $data['duplicated_lesson'] = $request->duplicated_lesson;    
            }
        }
        
        $instances = FilterHelpers::getFilteredQuery($this->model_name::with($this->eager_loading_fields), $request->all(), $this->model_name);
        $appends = ModelHelpers::getAppends($request);

        $data['instances'] = $instances->sortable()->paginate($rows_per_page)->appends($appends);

        return array_merge($data, $properties);
    }
    
    public function getInstanceFormData (Request $request)
    {
        $data = $this->model_name::getProperties();
        $data['add_form_fields'] = $this->model_name::getAddFormFields();
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

    public function addOrUpdateInstance (Request $request)
    {
        $instance_name_field = $this->instance_name_field;
        
        $data = $this->model_name::getProperties();
        $data['filter_form_fields'] = $this->model_name::getFilterFormFields();
        $data['add_form_fields'] = $this->model_name::getAddFormFields();
        
        $instance = ModelHelpers::addOrUpdateInstance($request->all(), $this->model_name);
        if (isset($request->updating_id)) {
            return ['updated_instance_name' => $instance->$instance_name_field];
        } else {
            return ['new_instance_name' => $instance->$instance_name_field];
        }
    }

    public function getSchedule (Request $request)
    {
        $weekly_period_ids = config('enum.weekly_period_ids');
        $schedule_instance_id = "schedule_{$this->instance_name}_id";
        $instance_name_field = $this->instance_name_field;
        $profession_level_name_field = $this->profession_level_name_field;
        $other_lesson_participant = $this->other_lesson_participant;
        $other_lesson_participant_name = $this->other_lesson_participant_name;
        
        $class_periods = ClassPeriod::get();
        $data['class_periods'] = array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()));

        $instance = $this->model_name::where('id', $request->$schedule_instance_id)->first();
        if ($instance) {
            $data['instance_name'] = $profession_level_name_field !== null ? $instance->$profession_level_name_field : $instance->$instance_name_field;    
        }
       
        $lessons = Lesson::with(['lesson_type', $this->instance_name, 'week_day', 'weekly_period', 'class_period'])
                        ->where("{$this->instance_name}_id", $request->$schedule_instance_id)
                        ->get();
        
        foreach ($lessons as $lesson) {
            if (isset($data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$lesson->weekly_period_id])
                || (isset($data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$weekly_period_ids['every_week']]))) 
            {
                $data['duplicated_lesson'] = [
                    $this->instance_name => $instance->$instance_name_field,
                    'class_period' => $lesson->class_period->number,
                    'week_day' => $lesson->week_day->name,
                    'weekly_period' => $lesson->weekly_period->name
                ];
                return $data;    
            } else {
                $data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$lesson->weekly_period_id] = [
                    'week_day_id' => $lesson->week_day_id,
                    'weekly_period_id' => $lesson->weekly_period_id,
                    'class_period_id' => $lesson->class_period_id,
                    'group_id' => $lesson->group_id,
                    'teacher_id' => $lesson->teacher_id,
                    'type' => $lesson->lesson_type->name,
                    'name' => $lesson->name,
                    $other_lesson_participant => $lesson->$other_lesson_participant->$other_lesson_participant_name
                ];
            }
        }

        return $data;
    }
}
