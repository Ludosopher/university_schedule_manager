<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Group;
use App\Helpers\DocExportHelpers;
use App\Helpers\FilterHelpers;
use App\Helpers\LessonHelpers;
use App\Helpers\MailHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\TeacherHelpers;
use App\Helpers\UniversalHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\teacher\ExportMonthScheduleToDocTeacherRequest;
use App\Http\Requests\teacher\ExportScheduleToDocTeacherRequest;
use App\Http\Requests\teacher\FilterTeacherRequest;
use App\Http\Requests\teacher\MonthScheduleTeacherRequest;
use App\Http\Requests\teacher\RescheduleTeacherRequest;
use App\Http\Requests\teacher\ScheduleTeacherRequest;
use App\Http\Requests\teacher\StoreTeacherRequest;
use App\Lesson;
use App\Mail\MailReplacementRequest;
use App\ReplacementRequest;
use App\Teacher;
use App\User;
use App\WeekDay;
use App\WeeklyPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public $config = [
        'model_name' => 'App\Teacher',
        'instance_name' => 'teacher',
        'instance_plural_name' => 'teachers',
        'instance_name_field' => 'full_name',
        'profession_level_name_field' => 'profession_level_name',
        'eager_loading_fields' => ['faculty', 'department', 'professional_level', 'position'],
        'other_lesson_participant' => 'group',
        'other_lesson_participant_name' => 'groups_name',
        'boolean_attributes' => [],
        'many_to_many_attributes' => [],
    ];

    public function getTeachers (FilterTeacherRequest $request)
    {
        $request->validated();
        $data = ModelHelpers::getInstances(request()->all(), $this->config);

        return view("teacher.teachers")->with('data', $data);
    }

    public function addTeacherForm (Request $request)
    {
        $data = ModelHelpers::getInstanceFormData($request->all(), $this->config);

        return view("teacher.add_teacher_form")->with('data', $data);
    }

    public function addOrUpdateTeacher (StoreTeacherRequest $request)
    {
        $data = ModelHelpers::addOrUpdateInstance($request->validated(), $this->config);

        if (isset($data['updated_instance_name'])) {
            return redirect()->route("teachers")->with('updated_instance_name', $data['updated_instance_name']); // route("teachers", ['updated_instance_name' => $data['updated_instance_name']]);
        } elseif (isset($data['new_instance_name'])) {
            return redirect()->route("teacher-add-form")->with('new_instance_name', $data['new_instance_name']); //route("teacher-add-form", ['new_instance_name' => $data['new_instance_name']]);
        }
    }

    public function deleteTeacher (Request $request)
    {
        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->config['model_name']);

        if ($deleted_instance) {
            $instance_name_field = $this->config['instance_name_field'];
            return redirect()->route("teachers")->with('deleted_instance_name', $deleted_instance->$instance_name_field); //route("teachers", ['deleted_instance_name' => $deleted_instance->$instance_name_field]);
        } else {
            return redirect()->route("teachers")->with('deleting_instance_not_found', true); //route("teachers", ['deleting_instance_not_found' => true]);
        }
    }

    public function getTeacherSchedule (ScheduleTeacherRequest $request)
    {
        $data = ModelHelpers::getSchedule($request->validated(), $this->config);
        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons")->with('duplicated_lesson', $data['duplicated_lesson']); //route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }

        return view("teacher.teacher_schedule")->with('data', $data);
    }

    public function getMonthTeacherSchedule (MonthScheduleTeacherRequest $request)
    {
        $data = ModelHelpers::getMonthSchedule($request->validated(), $this->config);
        request()->flash();
        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons")->with('duplicated_lesson', $data['duplicated_lesson']); //route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }

        return view("teacher.teacher_month_schedule")->with('data', $data);
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
        $data = ModelHelpers::getModelRechedulingData($validation['validated'], $reschedule_data, $this->config);

        return view("teacher.teacher_reschedule")->with('data', $data);
    }

    public function exportScheduleToDoc (ExportScheduleToDocTeacherRequest $request)
    {
        $data = $request->validated();
        $data['other_participant'] = $this->config['other_lesson_participant'];

        $filename = "teacher_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
        $objWriter->save("php://output");
    }

    public function exportMonthScheduleToDoc (Request $request)
    {
        $request->flash();
        $validation = ValidationHelpers::exportMonthTeacherScheduleToDocValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->input('prev_data'), true);
            return redirect()->route('teacher-month-schedule', $prev_data)->withErrors($validation['validator']);
        }

        $data = $validation['validated'];
        $data['other_participant'] = $this->config['other_lesson_participant'];

        $filename = "teacher_month_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::monthScheduleExport($data);
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
        $data['other_participant'] = $this->config['other_lesson_participant'];
        $data['is_reschedule_for'] = 'teacher';

        $filename = "teacher_reschedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
        $objWriter->save("php://output");
    }

}
