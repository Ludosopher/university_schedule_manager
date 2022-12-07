<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Group;
use App\Helpers\DocExportHelpers;
use App\Helpers\FilterHelpers;
use App\Helpers\LessonHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\TeacherHelpers;
use App\Http\Requests\teacher\ExportScheduleToDocTeacherRequest;
use App\Http\Requests\teacher\FilterTeacherRequest;
use App\Http\Requests\teacher\StoreTeacherRequest;
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
    protected $other_lesson_participant_name = 'groups_name';
    
    public function getTeachers (FilterTeacherRequest $request)
    {
        // if ($request->isMethod('post')) {
        //     $validator = Validator::make($request->all(), $this->model_name::filterRules(), [], $this->model_name::filterAttrNames());
        //     if ($validator->fails()) {
        //         return redirect()->route("{$this->instance_plural_name}")->withErrors($validator)->withInput();
        //     }
        // }
     
        $data = $this->getInstances($request->validated());

        return view("teacher.teachers")->with('data', $data);
    }

    public function addTeacherForm (Request $request)
    {
        $data = $this->getInstanceFormData($request);
        
        return view("{$this->instance_name}.add_{$this->instance_name}_form")->with('data', $data);
    }

    public function addOrUpdateTeacher (StoreTeacherRequest $request)
    {
        //$validator = Validator::make($request->all(), $this->model_name::rules($request), [], $this->model_name::attrNames())->validated();
        // if ($validator->fails()) {
        //     if (isset($request->updating_id)) {
        //         return redirect()->route("{$this->instance_name}-form", ['updating_id' => $request->updating_id])->withErrors($validator)->withInput();    
        //     }
        //     return redirect()->route("{$this->instance_name}-form")->withErrors($validator)->withInput(); 
        // }

        $data = $this->addOrUpdateInstance($request->validated());
                
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
            "schedule_teacher_id" => "required|integer|exists:App\Teacher,id",
            'week_number' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return back()->with('shedule_validation_errors', true); 
        }

        $data = $this->getSchedule($request);

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }
       
        return view("{$this->instance_name}.{$this->instance_name}_schedule")->with('data', $data);
    }

    public function getTeacherReschedule (Request $request)
    {
        $request->flash();
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|integer|exists:App\Teacher,id',
            'lesson_id' => 'required|integer|exists:App\Lesson,id',
            'week_number' => 'nullable|string'
        ]);
        if ($validator->fails()) {
            $prev_data = json_decode($request->input('prev_data'), true);        
            return redirect()->route('lesson-rescheduling', $prev_data)->withErrors($validator); 
        }
        
        $reschedule_data = LessonHelpers::getReschedulingData($request->all());
        $data = $this->getModelRechedulingData($request, $reschedule_data['free_periods']);
        
        return view("{$this->instance_name}.{$this->instance_name}_reschedule")->with('data', $data);
    }

    public function exportScheduleToDoc (ExportScheduleToDocTeacherRequest $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'lessons' => 'required|array',
        //     'teacher_name' => 'required|string',
        //     'week_data' => 'nullable|string',
        // ])->validate();
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator);
        // }

        $data = $request->validated();
        $data['other_participant'] = $this->other_lesson_participant;
        
        $filename = "teacher_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
        $objWriter->save("php://output");
    }

    public function exportRescheduleToDoc (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lessons' => 'required|string',
            'teacher_name' => 'required|string',
            'rescheduling_lesson_id' => 'required|integer|exists:App\Lesson,id'
        ]);
        if ($validator->fails()) {
            $prev_data = json_decode($request->all()['prev_data'], true);
            return redirect()->route('teacher-reschedule', $prev_data)->withErrors($validator);
        }

        $data = $validator->validated();
        $data['participant'] = $request->teacher_name;
        $data['other_participant'] = $this->other_lesson_participant;
        $data['is_reschedule_for'] = 'teacher';
        
        $filename = "teacher_reschedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
        $objWriter->save("php://output");
    }

}
