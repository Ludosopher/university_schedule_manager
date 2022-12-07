<?php

namespace App\Http\Controllers;

use App\Helpers\DocExportHelpers;
use App\Helpers\GroupHelpers;
use App\Helpers\LessonHelpers;
use App\Helpers\ModelHelpers;
use App\Http\Requests\group\ExportScheduleToDocGroupRequest;
use App\Http\Requests\group\FilterGroupRequest;
use App\Http\Requests\group\StoreGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends ModelController
{
    protected $model_name = 'App\Group';
    protected $instance_name = 'group';
    protected $instance_plural_name = 'groups';
    protected $instance_name_field = 'name';
    protected $profession_level_name_field = null;
    protected $eager_loading_fields = ['faculty', 'study_program', 'study_orientation', 'study_degree', 'study_form', 'course'];
    protected $other_lesson_participant = 'teacher';
    protected $other_lesson_participant_name = ['teacher', 'profession_level_name'];

    public function getGroups (FilterGroupRequest $request)
    {
        $data = $this->getInstances($request->validated());

        return view("group.groups")->with('data', $data);
    }

    public function addGroupForm (Request $request)
    {
        $data = $this->getInstanceFormData($request);

        return view("{$this->instance_name}.add_{$this->instance_name}_form")->with('data', $data);
    }

    public function addOrUpdateGroup (StoreGroupRequest $request)
    {

        $data = $this->addOrUpdateInstance($request->validated());

        if (is_array($data)) {
            if (isset($data['updated_instance_name'])) {
                return redirect()->route("{$this->instance_plural_name}", ['updated_instance_name' => $data['updated_instance_name']]);
            } elseif (isset($data['new_instance_name'])) {
                return redirect()->route("{$this->instance_name}-form", ['new_instance_name' => $data['new_instance_name']]);
            }
        }
    }

    public function deleteGroup (Request $request)
    {
        $relation_delited_result = GroupHelpers::deleteGroupLessonRelation($request->deleting_id);
        if (!$relation_delited_result) {
            return redirect()->route("{$this->instance_plural_name}", ['deleting_instance_not_found' => true]);
        }
        if ($relation_delited_result === 'there_are_lessons_only_with_this_group') {
            return redirect()->route("{$this->instance_plural_name}", [$relation_delited_result => true]);
        }

        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->model_name);
        $instance_name_field = $this->instance_name_field;
        return redirect()->route("{$this->instance_plural_name}", ['deleted_instance_name' => $deleted_instance->$instance_name_field]);
    }

    public function getGroupSchedule (Request $request)
    {
        $validator = Validator::make($request->all(), [
            "schedule_group_id" => "required|integer|exists:App\Group,id",
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

    public function getGroupReschedule (Request $request)
    {
        $request->flash();
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|integer|exists:App\Group,id',
            'teacher_id' => 'required|integer|exists:App\Teacher,id',
            'lesson_id' => 'required|integer|exists:App\Lesson,id'
        ]);
            if ($validator->fails()) {
                $prev_data = json_decode($request->input('prev_data'), true);        
                return redirect()->route('lesson-rescheduling', $prev_data)->withErrors($validator);
            }

        $reschedule_data = LessonHelpers::getReschedulingData($request->all());
        $data = $this->getModelRechedulingData($request, $reschedule_data['free_periods']);

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }
        
        return view("{$this->instance_name}.{$this->instance_name}_reschedule")->with('data', $data);
    }

    public function exportScheduleToDoc (ExportScheduleToDocGroupRequest $request)
    {
        $data = $request->validated();
        $data['other_participant'] = $this->other_lesson_participant;
        
        $filename = "group_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
        $objWriter->save("php://output");
    }

    public function exportRescheduleToDoc (Request $request)
    {
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
        $data['participant'] = $request->group_name;
        $data['other_participant'] = $this->other_lesson_participant;
        $data['is_reschedule_for'] = 'group';

        $filename = "group_reschedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
        $objWriter->save("php://output");
    }
}
