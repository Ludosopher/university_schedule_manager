<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Group;
use App\Helpers\DocExportHelpers;
use App\Helpers\FilterHelpers;
use App\Helpers\LessonHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\TeacherHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\teacher\ExportScheduleToDocTeacherRequest;
use App\Http\Requests\teacher\FilterTeacherRequest;
use App\Http\Requests\teacher\RescheduleTeacherRequest;
use App\Http\Requests\teacher\ScheduleTeacherRequest;
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
        $request->validated();
        $data = $this->getInstances(request()->all());

        return view("teacher.teachers")->with('data', $data);
    }

    public function addTeacherForm (Request $request)
    {
        $data = $this->getInstanceFormData($request);
        
        return view("teacher.add_teacher_form")->with('data', $data);
    }

    public function addOrUpdateTeacher (StoreTeacherRequest $request)
    {
        $data = $this->addOrUpdateInstance($request->validated());
                
        if (isset($data['updated_instance_name'])) {
            return redirect()->route("teachers", ['updated_instance_name' => $data['updated_instance_name']]);
        } elseif (isset($data['new_instance_name'])) {
            return redirect()->route("teacher-form", ['new_instance_name' => $data['new_instance_name']]);
        }
    }

    public function deleteTeacher (Request $request)
    {
        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->model_name); 
            
        if ($deleted_instance) {
            $instance_name_field = $this->instance_name_field;
            return redirect()->route("teachers", ['deleted_instance_name' => $deleted_instance->$instance_name_field]);
        } else {
            return redirect()->route("teachers", ['deleting_instance_not_found' => true]);
        }
    }

    public function getTeacherSchedule (ScheduleTeacherRequest $request)
    {
        $data = $this->getSchedule($request->validated());

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }
       
        return view("teacher.teacher_schedule")->with('data', $data);
    }

    public function getTeacherReschedule (Request $request)
    {
        $request->flash();
        $validation = ValidationHelpers::getTeacherRescheduleValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->input('prev_data'), true);        
            return redirect()->route('lesson-rescheduling', $prev_data)->withErrors($validation['validator']);
        }
                
        $reschedule_data = LessonHelpers::getReschedulingData($validation['validated']);
        $data = $this->getModelRechedulingData($request, $reschedule_data['free_periods']);
        
        return view("teacher.teacher_reschedule")->with('data', $data);
    }

    public function exportScheduleToDoc (ExportScheduleToDocTeacherRequest $request)
    {
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
        $validation = ValidationHelpers::exportTeacherRescheduleToDocValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->all()['prev_data'], true);
            return redirect()->route('teacher-reschedule', $prev_data)->withErrors($validation['validator']);
        }

        $data = $validation['validated'];
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
