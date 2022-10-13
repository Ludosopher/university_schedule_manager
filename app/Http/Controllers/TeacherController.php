<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Helpers\ModelHelpers;
use App\Lesson;
use App\WeekDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends ModelController
{
    protected $model_name = 'App\Teacher';
    protected $instance_name = 'teacher';
    protected $instance_plural_name = 'teachers';
    protected $instance_name_field = 'teacher_full_name';
    protected $eager_loading_fields = ['faculty', 'department', 'professional_level', 'position'];
    
    public function getTeachers (Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), $this->model_name::filterRules());
            if ($validator->fails()) {
                return redirect()->route("{$this->instance_name}.{$this->instance_plural_name}")->withErrors($validator)->withInput();
            }
        }
        
        $data = $this->getInstances($request);

        return view("{$this->instance_name}.{$this->instance_plural_name}")->with('data', $data);
    }

    public function addTeacherForm (Request $request)
    {
        $data = $this->getInstanceFormData($request);
        
        return view("{$this->instance_name}.add_{$this->instance_name}_form")->with('data', $data);
    }

    public function addOrUpdateTeacher (Request $request)
    {
        $validator = Validator::make($request->all(), $this->model_name::rules($request), [], $this->model_name::attrNames());
        if ($validator->fails()) {
            if (isset($request->updating_id)) {
                return redirect()->route("{$this->instance_name}-form", ['updating_id' => $request->updating_id])->withErrors($validator)->withInput();    
            }
            return redirect()->route("{$this->instance_name}-form")->withErrors($validator)->withInput(); 
        }
        
        $data = $this->addOrUpdateInstance($request);
                
        if (isset($data['updated_instance_name'])) {
            return redirect()->route("{$this->instance_plural_name}", ['updated_instance_name' => $data['updated_instance_name']]);
        } elseif (isset($data['new_instance_name'])) {
            return redirect()->route("{$this->instance_name}-form", ['new_instance_name' => $data['new_instance_name']]);
        }
    }

    public function deleteTeacher (Request $request)
    {
        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->model_name); 
            
        if ($deleted_instance) {
            $instance_name_field = $this->instance_name_field;
            return redirect()->route("{$this->instance_plural_name}", ['deleted_instance_name' => $deleted_instance->$instance_name_field]);
        } else {
            return redirect()->route("{$this->instance_plural_name}", ['deleting_instance_not_found' => true]);
        }
    }

    public function getSchedule (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schedule_teacher_id' => 'required|integer|exists:App\Teacher,id'
        ]);
        if ($validator->fails()) {
            return redirect()->route("{$this->instance_name}.{$this->instance_plural_name}")->withErrors($validator); 
        }

        $class_periods = ClassPeriod::get();
        $data['class_periods'] = array_combine(range(1, count($class_periods)), array_values($class_periods));
        
        $lessons = Lesson::with(['week_day', 'weekly_period', 'class_period', 'group', 'teacher'])
                                    //->join('class_periods', 'lessons.class_period_id', '=', 'class_periods.id')
                                    // ->orderBy('week_day_id')
                                    // ->orderBy('class_periods.number')                          
                                    ->where('teacher_id', $request->schedule_teacher_id)
                                    ->get();
        
        foreach ($lessons as $lesson) {
            $data['lessons'][$lesson->class_period_id][$lesson->week_day_id] = [
                'weekly_period' => $lesson->weekly_period_id, 
                'name' => $lesson->name,
                'group' => $lesson->group->name
            ]; 
        }

        return view("{$this->instance_name}.{$this->instance_name}_schedule")->with('data', $data);
    }
}
