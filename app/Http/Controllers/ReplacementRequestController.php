<?php

namespace App\Http\Controllers;

use App\Helpers\ModelHelpers;
use App\Helpers\ReplacementRequestHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\replacement_request\FilterReplacementReqRequest;
use App\Http\Requests\replacement_request\StoreReplacementReqRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReplacementRequestController extends Controller
{
    public $config = [
        'model_name' => 'App\ReplacementRequest',
        'instance_name' => 'replacement_request',
        'instance_plural_name' => 'replacement_requests',
        'instance_name_field' => 'name',
        'profession_level_name_field' => null,
        'eager_loading_fields' => ['status', 'replaceable_lesson', 'replacing_lesson', 'initiator', 'messages'],
        'other_lesson_participant' => null,
        'other_lesson_participant_name' => null,
        'boolean_attributes' => [],
        'many_to_many_attributes' => ['is_regular'],
    ];

    public function getReplacementRequests (FilterReplacementReqRequest $request)
    {
        $request->validated();
        $data = ModelHelpers::getInstances(request()->all(), $this->config);

        return view("replacement_request.replacement_requests")->with('data', $data);
    }

    public function getMyReplacementRequests (Request $request)
    {
 
        $data = ReplacementRequestHelpers::getMyReplacementRequests(Auth::user()->id, $this->config);
        
        return view("replacement_request.my_replacement_requests")->with('data', $data);
    }

    public function addReplacementRequest (Request $request)
    {
        $validation = ValidationHelpers::addReplacementRequestValidation($request->all());
        if (! $validation['success']) {
            $replace_rules = json_decode($request->all()['prev_replace_rules'], true);
            return redirect()->route("lesson-replacement", [
                'replace_rules' => $replace_rules,
                'week_data' => $request->week_data,
                'week_dates' => $request->week_dates,
                'is_red_week' => $request->is_red_week,
            ])->withErrors($validation['validator']);
        }

        $new_request = ModelHelpers::addOrUpdateInstance($validation['validated'], $this->config);

        return redirect()->route("my_replacement_requests")->with('new_instance_name', $new_request['new_instance_name']);
    }

    public function updateReplacementRequest (StoreReplacementReqRequest $request)
    {
        $replacement_request = ModelHelpers::addOrUpdateInstance($request->validated(), $this->config);

        return redirect()->back()->with('updated_instance_name', $replacement_request['updated_instance_name']);
    }

    public function deleteReplacementRequest (Request $request)
    {
        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->config['model_name']);

        if ($deleted_instance) {
            $instance_name_field = $this->config['instance_name_field'];
            return redirect()->route("my_replacement_requests")->with('deleted_instance_name', $deleted_instance->$instance_name_field);
        } else {
            return redirect()->route("my_replacement_requests")->with('deleting_instance_not_found', true);
        }
    }

    // public function getTeacherSchedule (ScheduleTeacherRequest $request)
    // {
    //     $data = ModelHelpers::getSchedule($request->validated(), $this->config);
    //     // request()->flash();
    //     if (isset($data['duplicated_lesson'])) {
    //         return redirect()->route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
    //     }

    //     return view("teacher.teacher_schedule")->with('data', $data);
    // }

    // public function getMonthTeacherSchedule (MonthScheduleTeacherRequest $request)
    // {
    //     $data = ModelHelpers::getMonthSchedule($request->validated(), $this->config);
    //     request()->flash();
    //     if (isset($data['duplicated_lesson'])) {
    //         return redirect()->route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
    //     }

    //     return view("teacher.teacher_month_schedule")->with('data', $data);
    // }

    // public function getTeacherReschedule (Request $request)
    // {
    //     $request->flash();
    //     $validation = ValidationHelpers::getTeacherRescheduleValidation($request->all());
    //     if (! $validation['success']) {
    //         $prev_data = json_decode($request->input('prev_data'), true);
    //         return redirect()->route('lesson-rescheduling', $prev_data)->withErrors($validation['validator']);
    //     }

    //     $reschedule_data = LessonHelpers::getReschedulingData($validation['validated']);
    //     $data = ModelHelpers::getModelRechedulingData($validation['validated'], $reschedule_data, $this->config);

    //     return view("teacher.teacher_reschedule")->with('data', $data);
    // }

    // public function exportScheduleToDoc (ExportScheduleToDocTeacherRequest $request)
    // {
    //     $data = $request->validated();
    //     $data['other_participant'] = $this->config['other_lesson_participant'];

    //     $filename = "teacher_schedule.docx";
    //     header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
    //     header( 'Content-Disposition: attachment; filename='.$filename);

    //     $objWriter = DocExportHelpers::scheduleExport($data);
    //     $objWriter->save("php://output");
    // }

    // public function exportMonthScheduleToDoc (Request $request)
    // {
    //     $request->flash();
    //     $validation = ValidationHelpers::exportMonthTeacherScheduleToDocValidation($request->all());
    //     if (! $validation['success']) {
    //         $prev_data = json_decode($request->input('prev_data'), true);
    //         return redirect()->route('teacher-month-schedule', $prev_data)->withErrors($validation['validator']);
    //     }

    //     $data = $validation['validated'];
    //     $data['other_participant'] = $this->config['other_lesson_participant'];

    //     $filename = "teacher_month_schedule.docx";
    //     header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
    //     header( 'Content-Disposition: attachment; filename='.$filename);

    //     $objWriter = DocExportHelpers::monthScheduleExport($data);
    //     $objWriter->save("php://output");
    // }

    // public function exportRescheduleToDoc (Request $request)
    // {
    //     $validation = ValidationHelpers::exportTeacherRescheduleToDocValidation($request->all());
    //     if (! $validation['success']) {
    //         $prev_data = json_decode($request->all()['prev_data'], true);
    //         return redirect()->route('teacher-reschedule', $prev_data)->withErrors($validation['validator']);
    //     }

    //     $data = $validation['validated'];
    //     $data['participant'] = $request->teacher_name;
    //     $data['other_participant'] = $this->config['other_lesson_participant'];
    //     $data['is_reschedule_for'] = 'teacher';

    //     $filename = "teacher_reschedule.docx";
    //     header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
    //     header( 'Content-Disposition: attachment; filename='.$filename);

    //     $objWriter = DocExportHelpers::scheduleExport($data);
    //     $objWriter->save("php://output");
    // }
}
