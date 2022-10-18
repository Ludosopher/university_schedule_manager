<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Group;
use App\Helpers\ModelHelpers;
use App\Lesson;
use App\Teacher;
use App\WeekDay;
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

    public function getTeacherSchedule (Request $request)
    {
        $validator = Validator::make($request->all(), [
            "schedule_{$this->instance_name}_id" => "required|integer|exists:{$this->model_name},id"
        ]);
        if ($validator->fails()) {
            return redirect()->route("{$this->instance_name}.{$this->instance_plural_name}")->withErrors($validator); 
        }

        $data = $this->getSchedule($request);

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("{$this->instance_plural_name}", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }
        
        return view("{$this->instance_name}.{$this->instance_name}_schedule")->with('data', $data);
    }

    public function getTeachersForReplacement (Request $request)
    {
        $replacement_lessons = [];
        $group = Group::where('id', $request->group_id)->with('lessons.teacher.lessons')->first();
        $seeking_teacher = Teacher::where('id', $request->teacher_id)->with('lessons')->first();
        
        foreach ($group->lessons as $group_lesson) {
            foreach ($group_lesson->teacher->lessons as $teacher_lesson) {
                if ($teacher_lesson->week_day_id == $request->week_day_id
                    && $teacher_lesson->weekly_period_id == $request->weekly_period_id
                    && $teacher_lesson->class_period_id == $request->class_period_id)
                {
                    unset($replacement_lessons[$group_lesson->teacher->id]);
                    break;
                }

                if ($teacher_lesson->group_id == $request->group_id) {
                    foreach ($seeking_teacher->lessons as $seeking_teacher_lesson) {
                        if ($teacher_lesson->week_day_id != $seeking_teacher_lesson->week_day_id
                            || $teacher_lesson->weekly_period_id != $seeking_teacher_lesson->weekly_period_id
                            || $teacher_lesson->class_period_id != $seeking_teacher_lesson->class_period_id)
                        {
                            $replacement_lessons[$group_lesson->teacher->id] = [
                                'teacher' => $group_lesson->teacher,
                                'lesson' => $teacher_lesson
                            ];
                        }
                    }
                }
            }
        }

        dd($replacement_lessons);
                                        
    }
}
