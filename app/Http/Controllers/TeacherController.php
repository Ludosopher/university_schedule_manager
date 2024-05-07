<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\teacher\DeleteTeacherRequest;
use App\Http\Requests\teacher\ExportScheduleToDocTeacherRequest;
use App\Http\Requests\teacher\FilterTeacherRequest;
use App\Http\Requests\teacher\MonthScheduleTeacherRequest;
use App\Http\Requests\teacher\ScheduleTeacherRequest;
use App\Http\Requests\teacher\StoreTeacherRequest;
use App\Instances\LessonInstance;
use App\Instances\ScheduleElements\TeacherScheduleElement;
use Illuminate\Http\Request;


class TeacherController extends Controller
{
    public function getTeachers (FilterTeacherRequest $request)
    {
        $request->validated();
        $data = (new TeacherScheduleElement())->getInstances(request()->all());

        return view("teacher.teachers")->with('data', $data);
    }

    public function addTeacherForm (Request $request)
    {
        $data = (new TeacherScheduleElement())->getInstanceFormData($request->all());

        return view("teacher.add_teacher_form")->with('data', $data);
    }

    public function addOrUpdateTeacher (StoreTeacherRequest $request)
    {
        $data = (new TeacherScheduleElement())->addOrUpdateInstance($request->validated());

        $response_content = ResponseHelpers::getContent($data, 'teacher');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function deleteTeacher (DeleteTeacherRequest $request)
    {
        $deleted_instance = (new TeacherScheduleElement())->deleteInstance($request->validated()['deleting_id']);

        $response_content = ResponseHelpers::getContent($deleted_instance, 'teacher');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function getTeacherSchedule (ScheduleTeacherRequest $request)
    {
        $data = (new TeacherScheduleElement())->getSchedule($request->validated());
        if (isset($data['duplicated_lesson'])) {
            $response_content = ResponseHelpers::getContent($data, 'teacher');
        
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
        }

        return view("teacher.teacher_schedule")->with('data', $data);
    }

    public function getMonthTeacherSchedule (MonthScheduleTeacherRequest $request)
    {
        $data = (new TeacherScheduleElement())->getMonthSchedule($request->validated());
        request()->flash();
        if (isset($data['duplicated_lesson'])) {
            $response_content = ResponseHelpers::getContent($data, 'teacher');
        
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
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

        $reschedule_data = (new LessonInstance())->getReschedulingData($validation['validated']);
        $data = (new TeacherScheduleElement())->getModelRechedulingData($validation['validated'], $reschedule_data);

        return view("teacher.teacher_reschedule")->with('data', $data);
    }

    public function exportScheduleToDoc (ExportScheduleToDocTeacherRequest $request)
    {
        $data = $request->validated();
        $data['other_participant'] = 'group';

        $filename = "teacher_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = (new TeacherScheduleElement())->scheduleExport($data);
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
        $data['other_participant'] = 'group';

        $filename = "teacher_month_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = (new TeacherScheduleElement())->monthScheduleExport($data);
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
        $data['other_participant'] = 'group';
        $data['is_reschedule_for'] = 'teacher';

        $filename = "teacher_reschedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = (new TeacherScheduleElement())->scheduleExport($data);
        $objWriter->save("php://output");
    }

}
