<?php

namespace App\Http\Controllers;

use App\DocExporters\ManyTables\DocExporterMonthSchedule;
use App\DocExporters\OneTable\WeekSchedule\DocExporterOrdinaryWeekSchedule;
use App\DocExporters\OneTable\WeekSchedule\DocExporterWeekReschedule;
use App\Helpers\ResponseHelpers;
use App\Helpers\ValidationHelpers;
use App\Instances\LessonInstance;
use App\Http\Requests\group\DeleteGroupRequest;
use App\Http\Requests\group\ExportScheduleToDocGroupRequest;
use App\Http\Requests\group\FilterGroupRequest;
use App\Http\Requests\group\MonthScheduleGroupRequest;
use App\Http\Requests\group\ScheduleGroupRequest;
use App\Http\Requests\group\StoreGroupRequest;
use App\Instances\ScheduleElements\GroupScheduleElement;
use Illuminate\Http\Request;


class GroupController extends Controller
{
    public function getGroups (FilterGroupRequest $request)
    {
        $request->validated();
        $data = (new GroupScheduleElement())->getInstances(request()->all());

        return view("group.groups")->with('data', $data);
    }

    public function addGroupForm (Request $request)
    {
        $data = (new GroupScheduleElement())->getInstanceFormData($request->all());

        return view("group.add_group_form")->with('data', $data);
    }

    public function addOrUpdateGroup (StoreGroupRequest $request)
    {
        $data = (new GroupScheduleElement())->addOrUpdateInstance($request->validated());

        $response_content = ResponseHelpers::getContent($data, 'group');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function deleteGroup (DeleteGroupRequest $request)
    {
        $relation_delited_result = (new GroupScheduleElement())->deleteGroupLessonRelation($request->validated()['deleting_id']);
        if (isset($relation_delited_result['there_are_lessons_only_with_this_group'])) {
            $response_content = ResponseHelpers::getContent($relation_delited_result, 'group');
            
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
        }
        
        $deleted_instance = (new GroupScheduleElement())->deleteInstance($request->validated()['deleting_id']);
        $response_content = ResponseHelpers::getContent($deleted_instance, 'group');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function getGroupSchedule (ScheduleGroupRequest $request)
    {
        $data = (new GroupScheduleElement())->getSchedule($request->validated());
        if (isset($data['duplicated_lesson'])) {
            $response_content = ResponseHelpers::getContent($data, 'group');
        
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
        }

        return view("group.group_schedule")->with('data', $data);
    }

    public function getMonthGroupSchedule (MonthScheduleGroupRequest $request)
    {
        $data = (new GroupScheduleElement())->getMonthSchedule($request->validated());
        request()->flash();
        if (isset($data['duplicated_lesson'])) {
            $response_content = ResponseHelpers::getContent($data, 'group');
        
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
        }

        return view("group.group_month_schedule")->with('data', $data);
    }

    public function getGroupReschedule (Request $request)
    {
        $request->flash();
        $validation = ValidationHelpers::getGroupRescheduleValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->input('prev_data'), true);
            return redirect()->route('lesson-rescheduling', $prev_data)->withErrors($validation['validator']);
        }

        $reschedule_data = (new LessonInstance)->getReschedulingData($validation['validated']);
        $data = (new GroupScheduleElement())->getModelRechedulingData($validation['validated'], $reschedule_data);

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons")->with('duplicated_lesson', $data['duplicated_lesson']);
        }

        return view("group.group_reschedule")->with('data', $data);
    }

    public function exportScheduleToDoc (ExportScheduleToDocGroupRequest $request)
    {
        $data = $request->validated();
        $data['other_participant'] = 'teacher';

        $filename = "group_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        //$objWriter = (new GroupScheduleElement())->scheduleExport($data);
        $objWriter = (new DocExporterOrdinaryWeekSchedule($data))->createWriter();
        $objWriter->save("php://output");
    }

    public function exportMonthScheduleToDoc (Request $request)
    {
        $request->flash();
        $validation = ValidationHelpers::exportMonthGroupScheduleToDocValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->input('prev_data'), true);
            return redirect()->route('group-month-schedule', $prev_data)->withErrors($validation['validator']);
        }

        $data = $validation['validated'];
        $data['other_participant'] = 'teacher';

        $filename = "teacher_month_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        //$objWriter = (new GroupScheduleElement())->monthScheduleExport($data);
        $objWriter = (new DocExporterMonthSchedule($data))->createWriter();
        $objWriter->save("php://output");
    }

    public function exportRescheduleToDoc (Request $request)
    {
        $validation = ValidationHelpers::exportGroupRescheduleToDocValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->all()['prev_data'], true);
            return redirect()->route('group-reschedule', $prev_data)->withErrors($validation['validator']);
        }

        $data = $validation['validated'];
        $data['participant'] = $request->group_name;
        $data['other_participant'] = 'teacher';
        $data['is_reschedule_for'] = 'group';

        $filename = "group_reschedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        //$objWriter = (new GroupScheduleElement())->scheduleExport($data);
        $objWriter = (new DocExporterWeekReschedule($data))->createWriter();
        $objWriter->save("php://output");
    }
}
