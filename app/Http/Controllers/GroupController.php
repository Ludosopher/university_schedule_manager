<?php

namespace App\Http\Controllers;

use App\Helpers\DocExportHelpers;
use App\Helpers\GroupHelpers;
use App\Helpers\LessonHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\ResponseHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\group\DeleteGroupRequest;
use App\Http\Requests\group\ExportScheduleToDocGroupRequest;
use App\Http\Requests\group\FilterGroupRequest;
use App\Http\Requests\group\MonthScheduleGroupRequest;
use App\Http\Requests\group\ScheduleGroupRequest;
use App\Http\Requests\group\StoreGroupRequest;
use Illuminate\Http\Request;


class GroupController extends Controller
{
    protected $config = [
        'model_name' => 'App\Group',
        'instance_name' => 'group',
        'instance_plural_name' => 'groups',
        'instance_name_field' => 'name',
        'profession_level_name_field' => null,
        'eager_loading_fields' => ['faculty', 'study_program', 'study_orientation', 'study_degree', 'study_form', 'course'],
        'other_lesson_participant' => 'teacher',
        'other_lesson_participant_name' => ['teacher', 'profession_level_name'],
        'boolean_attributes' => [],
        'many_to_many_attributes' => [],
    ];

    public function getGroups (FilterGroupRequest $request)
    {
        $request->validated();
        $data = ModelHelpers::getInstances(request()->all(), $this->config);

        return view("group.groups")->with('data', $data);
    }

    public function addGroupForm (Request $request)
    {
        $data = ModelHelpers::getInstanceFormData($request->all(), $this->config);

        return view("group.add_group_form")->with('data', $data);
    }

    public function addOrUpdateGroup (StoreGroupRequest $request)
    {
        $data = ModelHelpers::addOrUpdateInstance($request->validated(), $this->config);

        $response_content = ResponseHelpers::getContent($data, $this->config['instance_name']);
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function deleteGroup (DeleteGroupRequest $request)
    {
        $relation_delited_result = GroupHelpers::deleteGroupLessonRelation($request->validated()['deleting_id']);
        if (isset($relation_delited_result['there_are_lessons_only_with_this_group'])) {
            $response_content = ResponseHelpers::getContent($relation_delited_result, $this->config['instance_name']);
            
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
        }
        
        $deleted_instance = ModelHelpers::deleteInstance($request->validated()['deleting_id'], $this->config);
        $response_content = ResponseHelpers::getContent($deleted_instance, $this->config['instance_name']);
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function getGroupSchedule (ScheduleGroupRequest $request)
    {
        $data = ModelHelpers::getSchedule($request->validated(), $this->config);
        if (isset($data['duplicated_lesson'])) {
            $response_content = ResponseHelpers::getContent($data, $this->config['instance_name']);
        
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
        }

        return view("group.group_schedule")->with('data', $data);
    }

    public function getMonthGroupSchedule (MonthScheduleGroupRequest $request)
    {
        $data = ModelHelpers::getMonthSchedule($request->validated(), $this->config);
        request()->flash();
        if (isset($data['duplicated_lesson'])) {
            $response_content = ResponseHelpers::getContent($data, $this->config['instance_name']);
        
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

        $reschedule_data = LessonHelpers::getReschedulingData($validation['validated']);
        $data = ModelHelpers::getModelRechedulingData($validation['validated'], $reschedule_data, $this->config);

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons")->with('duplicated_lesson', $data['duplicated_lesson']);
        }

        return view("group.group_reschedule")->with('data', $data);
    }

    public function exportScheduleToDoc (ExportScheduleToDocGroupRequest $request)
    {
        $data = $request->validated();
        $data['other_participant'] = $this->config['other_lesson_participant'];

        $filename = "group_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
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
        $data['other_participant'] = $this->config['other_lesson_participant'];

        $filename = "teacher_month_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::monthScheduleExport($data);
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
        $data['other_participant'] = $this->config['other_lesson_participant'];
        $data['is_reschedule_for'] = 'group';

        $filename = "group_reschedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
        $objWriter->save("php://output");
    }
}
