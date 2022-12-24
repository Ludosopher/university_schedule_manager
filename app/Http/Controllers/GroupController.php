<?php

namespace App\Http\Controllers;

use App\Helpers\DocExportHelpers;
use App\Helpers\GroupHelpers;
use App\Helpers\LessonHelpers;
use App\Helpers\ModelHelpers;
<<<<<<< HEAD
use App\Http\Requests\group\ExportScheduleToDocGroupRequest;
use App\Http\Requests\group\FilterGroupRequest;
=======
use App\Helpers\ValidationHelpers;
use App\Http\Requests\group\ExportScheduleToDocGroupRequest;
use App\Http\Requests\group\FilterGroupRequest;
use App\Http\Requests\group\MonthScheduleGroupRequest;
use App\Http\Requests\group\ScheduleGroupRequest;
>>>>>>> develop
use App\Http\Requests\group\StoreGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
<<<<<<< HEAD
        $data = $this->getInstances($request->validated());
=======
        $request->validated();
        $data = ModelHelpers::getInstances(request()->all(), $this->config);
>>>>>>> develop

        return view("group.groups")->with('data', $data);
    }

    public function addGroupForm (Request $request)
    {
        $data = ModelHelpers::getInstanceFormData($request->all(), $this->config);

        return view("group.add_group_form")->with('data', $data);
    }

    public function addOrUpdateGroup (StoreGroupRequest $request)
    {
<<<<<<< HEAD

        $data = $this->addOrUpdateInstance($request->validated());
=======
        $data = ModelHelpers::addOrUpdateInstance($request->validated(), $this->config);
>>>>>>> develop

        if (is_array($data)) {
            if (isset($data['updated_instance_name'])) {
                return redirect()->route("groups", ['updated_instance_name' => $data['updated_instance_name']]);
            } elseif (isset($data['new_instance_name'])) {
                return redirect()->route("group-add-form", ['new_instance_name' => $data['new_instance_name']]);
            }
        }
    }

    public function deleteGroup (Request $request)
    {
        $relation_delited_result = GroupHelpers::deleteGroupLessonRelation($request->deleting_id);
        if (!$relation_delited_result) {
            return redirect()->route("groups", ['deleting_instance_not_found' => true]);
        }
        if ($relation_delited_result === 'there_are_lessons_only_with_this_group') {
            return redirect()->route("groups", [$relation_delited_result => true]);
        }

        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->config['model_name']);
        $instance_name_field = $this->config['instance_name_field'];
        return redirect()->route("groups", ['deleted_instance_name' => $deleted_instance->$instance_name_field]);
    }

    public function getGroupSchedule (ScheduleGroupRequest $request)
    {
<<<<<<< HEAD
        $validator = Validator::make($request->all(), [
            "schedule_group_id" => "required|integer|exists:App\Group,id",
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

        return view("group.group_schedule")->with('data', $data);
    }

    public function getMonthGroupSchedule (MonthScheduleGroupRequest $request)
    {
        $data = ModelHelpers::getMonthSchedule($request->validated(), $this->config);
        request()->flash();
        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }

        return view("group.group_month_schedule")->with('data', $data);
    }

    public function getGroupReschedule (Request $request)
    {
        $request->flash();
<<<<<<< HEAD
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|integer|exists:App\Group,id',
            'teacher_id' => 'required|integer|exists:App\Teacher,id',
            'lesson_id' => 'required|integer|exists:App\Lesson,id'
        ]);
            if ($validator->fails()) {
                $prev_data = json_decode($request->input('prev_data'), true);        
                return redirect()->route('lesson-rescheduling', $prev_data)->withErrors($validator);
            }
=======
        $validation = ValidationHelpers::getGroupRescheduleValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->input('prev_data'), true);
            return redirect()->route('lesson-rescheduling', $prev_data)->withErrors($validation['validator']);
        }
>>>>>>> develop

        $reschedule_data = LessonHelpers::getReschedulingData($validation['validated']);
        $data = ModelHelpers::getModelRechedulingData($validation['validated'], $reschedule_data, $this->config);

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }

        return view("group.group_reschedule")->with('data', $data);
    }

    public function exportScheduleToDoc (ExportScheduleToDocGroupRequest $request)
    {
        $data = $request->validated();
<<<<<<< HEAD
        $data['other_participant'] = $this->other_lesson_participant;
        
=======
        $data['other_participant'] = $this->config['other_lesson_participant'];

>>>>>>> develop
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
<<<<<<< HEAD
        $validator = Validator::make($request->all(), [
            'lessons' => 'required|string',
            'group_name' => 'required|string',
            'rescheduling_lesson_id' => 'required|integer|exists:App\Lesson,id'
        ]);
        if ($validator->fails()) {
            $prev_data = json_decode($request->all()['prev_data'], true);
            return redirect()->route('teacher-reschedule', $prev_data)->withErrors($validator); 
        }

        $data = $validator->validated();
=======
        $validation = ValidationHelpers::exportGroupRescheduleToDocValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->all()['prev_data'], true);
            return redirect()->route('group-reschedule', $prev_data)->withErrors($validation['validator']);
        }

        $data = $validation['validated'];
>>>>>>> develop
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
