<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Group;
use App\Helpers\DocExportHelpers;
use App\Helpers\FilterHelpers;
use App\Helpers\LessonHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\TeacherHelpers;
<<<<<<< HEAD
use App\Http\Requests\teacher\ExportScheduleToDocTeacherRequest;
use App\Http\Requests\teacher\FilterTeacherRequest;
=======
use App\Helpers\ValidationHelpers;
use App\Http\Requests\teacher\ExportScheduleToDocTeacherRequest;
use App\Http\Requests\teacher\FilterTeacherRequest;
use App\Http\Requests\teacher\RescheduleTeacherRequest;
use App\Http\Requests\teacher\ScheduleTeacherRequest;
>>>>>>> develop
use App\Http\Requests\teacher\StoreTeacherRequest;
use App\Lesson;
use App\Teacher;
use App\User;
use App\WeekDay;
use App\WeeklyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
<<<<<<< HEAD
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
        $data = $this->getInstances($request->validated());

=======
    public $config = [
        'model_name' => 'App\Teacher',
        'instance_name' => 'teacher',
        'instance_plural_name' => 'teachers',
        'instance_name_field' => 'full_name',
        'profession_level_name_field' => 'profession_level_name',
        'eager_loading_fields' => ['faculty', 'department', 'professional_level', 'position'],
        'other_lesson_participant' => 'group',
        'other_lesson_participant_name' => 'groups_name',
        'boolean_attridutes' => [],
    ];

    public function getTeachers (FilterTeacherRequest $request)
    {
        $request->validated();
        $data = ModelHelpers::getInstances(request()->all(), $this->config);

>>>>>>> develop
        return view("teacher.teachers")->with('data', $data);
    }

    public function addTeacherForm (Request $request)
    {
        $data = ModelHelpers::getInstanceFormData($request->all(), $this->config);

        return view("teacher.add_teacher_form")->with('data', $data);
    }

    public function addOrUpdateTeacher (StoreTeacherRequest $request)
    {
<<<<<<< HEAD
        $data = $this->addOrUpdateInstance($request->validated());
                
=======
        $data = ModelHelpers::addOrUpdateInstance($request->validated(), $this->config);

>>>>>>> develop
        if (isset($data['updated_instance_name'])) {
            return redirect()->route("teachers", ['updated_instance_name' => $data['updated_instance_name']]);
        } elseif (isset($data['new_instance_name'])) {
            return redirect()->route("teacher-form", ['new_instance_name' => $data['new_instance_name']]);
        }
    }

    public function deleteTeacher (Request $request)
    {
        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->config['model_name']);

        if ($deleted_instance) {
            $instance_name_field = $this->config['instance_name_field'];
            return redirect()->route("teachers", ['deleted_instance_name' => $deleted_instance->$instance_name_field]);
        } else {
            return redirect()->route("teachers", ['deleting_instance_not_found' => true]);
        }
    }

    public function getTeacherSchedule (ScheduleTeacherRequest $request)
    {
<<<<<<< HEAD
        
        $validator = Validator::make($request->all(), [
            "schedule_teacher_id" => "required|integer|exists:App\Teacher,id",
            'week_number' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return back()->with('shedule_validation_errors', true); 
        }

        $data = $this->getSchedule($request);
=======
        $data = ModelHelpers::getSchedule($request->validated(), $this->config);
>>>>>>> develop

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }
<<<<<<< HEAD
       
        return view("{$this->instance_name}.{$this->instance_name}_schedule")->with('data', $data);
=======

        return view("teacher.teacher_schedule")->with('data', $data);
>>>>>>> develop
    }

    public function getTeacherReschedule (Request $request)
    {
        $request->flash();
<<<<<<< HEAD
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
=======
        $validation = ValidationHelpers::getTeacherRescheduleValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->input('prev_data'), true);
            return redirect()->route('lesson-rescheduling', $prev_data)->withErrors($validation['validator']);
        }

        $reschedule_data = LessonHelpers::getReschedulingData($validation['validated']);
        $data = ModelHelpers::getModelRechedulingData($validation['validated'], $reschedule_data['free_periods'], $this->config);

        return view("teacher.teacher_reschedule")->with('data', $data);
>>>>>>> develop
    }

    public function exportScheduleToDoc (ExportScheduleToDocTeacherRequest $request)
    {
        $data = $request->validated();
<<<<<<< HEAD
        $data['other_participant'] = $this->other_lesson_participant;
        
=======
        $data['other_participant'] = $this->config['other_lesson_participant'];

>>>>>>> develop
        $filename = "teacher_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
        $objWriter->save("php://output");
    }

    public function exportRescheduleToDoc (Request $request)
    {
<<<<<<< HEAD
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
=======
        $validation = ValidationHelpers::exportTeacherRescheduleToDocValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->all()['prev_data'], true);
            return redirect()->route('teacher-reschedule', $prev_data)->withErrors($validation['validator']);
        }

        $data = $validation['validated'];
>>>>>>> develop
        $data['participant'] = $request->teacher_name;
        $data['other_participant'] = $this->config['other_lesson_participant'];
        $data['is_reschedule_for'] = 'teacher';
<<<<<<< HEAD
        
=======

>>>>>>> develop
        $filename = "teacher_reschedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
        $objWriter->save("php://output");
    }

}
