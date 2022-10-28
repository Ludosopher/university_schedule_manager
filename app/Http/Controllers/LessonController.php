<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Group;
use App\Helpers\LessonHelpers;
use App\Helpers\ModelHelpers;
use App\Lesson;
use App\Teacher;
use App\WeekDay;
use App\WeeklyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends ModelController
{
    protected $model_name = 'App\Lesson';
    protected $instance_name = 'lesson';
    protected $instance_plural_name = 'lessons';
    protected $instance_name_field = 'name';
    protected $profession_level_name_field = null;
    protected $eager_loading_fields = ['lesson_type', 'week_day', 'weekly_period', 'class_period', 'teacher', 'group'];
    protected $other_lesson_participant = null;
    protected $other_lesson_participant_name = null;
            
    public function getLessons (Request $request)
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

    public function addLessonForm (Request $request)
    {
        $data = $this->getInstanceFormData($request);
        
        return view("{$this->instance_name}.add_{$this->instance_name}_form")->with('data', $data);
    }

    public function addOrUpdateLesson (Request $request)
    {
        $validator = Validator::make($request->all(), $this->model_name::rules($request), [], $this->model_name::attrNames());
        if ($validator->fails()) {
            if (isset($request->updating_id)) {
                return redirect()->route("{$this->instance_name}-form", ['updating_id' => $request->updating_id])->withErrors($validator)->withInput();    
            }
            return redirect()->route("{$this->instance_name}-form")->withErrors($validator)->withInput(); 
        }

        $data = $this->addOrUpdateInstance($request);
                
        if (is_array($data)) {
            if (isset($data['updated_instance_name'])) {
                return redirect()->route("{$this->instance_plural_name}", ['updated_instance_name' => $data['updated_instance_name']]);
            } elseif (isset($data['new_instance_name'])) {
                return redirect()->route("{$this->instance_name}-form", ['new_instance_name' => $data['new_instance_name']]);
            }
        }
    }

    public function deleteLesson (Request $request)
    {
        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->model_name);
            
        if ($deleted_instance) {
            $instance_name_field = $this->instance_name_field;
            return redirect()->route("{$this->instance_plural_name}", ['deleted_instance_name' => $deleted_instance->$instance_name_field]);
        } else {
            return redirect()->route("{$this->instance_plural_name}", ['deleting_instance_not_found' => true]);
        }
    }

    public function getReplacementVariants (Request $request)
    {
        $request->flash();
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), $this->model_name::filterReplacementRules());
            if ($validator->fails()) {
                return redirect()->route("{$this->instance_name}-replacement", ['replace_rules' => json_decode($request->all()['prev_replace_rules'], true)])->withErrors($validator)->withInput();
            }
        }
        
        $data = LessonHelpers::getReplacementData($request->all());
      
        return view("{$this->instance_name}.replacement_lessons")->with('data', $data);
    }

    public function getReschedulingVariants (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|integer|exists:App\Teacher,id',
            'group_id' => 'required|integer|exists:App\Group,id',
            'lesson_id' => 'required|integer|exists:App\Lesson,id',
        ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }
        
        $data = LessonHelpers::getReschedulingData($request->all());

        return view("{$this->instance_name}.{$this->instance_name}_reschedule")->with('data', $data);
    }
    
}
