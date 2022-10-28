<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Group;
use App\Helpers\FilterHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\TeacherHelpers;
use App\Lesson;
use App\Teacher;
use App\WeekDay;
use App\WeeklyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends ModelController
{
    protected $model_name = 'App\Teacher';
    protected $instance_name = 'teacher';
    protected $instance_plural_name = 'teachers';
    protected $instance_name_field = 'full_name';
    protected $profession_level_name_field = 'profession_level_name';
    protected $eager_loading_fields = ['faculty', 'department', 'professional_level', 'position'];
    protected $other_lesson_participant = 'group';
    protected $other_lesson_participant_name = 'name';
    
    public function getTeachers (Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), $this->model_name::filterRules());
            if ($validator->fails()) {
                return redirect()->route("{$this->instance_plural_name}")->withErrors($validator)->withInput();
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

    public function getTeacherSchedule (Request $request)
    {
        $validator = Validator::make($request->all(), [
            "schedule_{$this->instance_name}_id" => "required|integer|exists:{$this->model_name},id"
        ]);
        if ($validator->fails()) {
            return redirect()->route("{$this->instance_name}-schedule")->withErrors($validator); 
        }

        $data = $this->getSchedule($request);

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }
        
        return view("{$this->instance_name}.{$this->instance_name}_schedule")->with('data', $data);
    }

}
