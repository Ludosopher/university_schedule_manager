<?php

namespace App\Http\Controllers;

use App\Helpers\DocExportHelpers;
use App\Helpers\GroupHelpers;
use App\Helpers\LessonHelpers;
use App\Helpers\ModelHelpers;
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

    public function getGroups (Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), $this->model_name::filterRules());
            if ($validator->fails()) {
                return redirect()->route("{$this->instance_plural_name}")->withErrors($validator)->withInput();
            }
        }
        $data = $this->getInstances($request);

        return view("{$this->instance_name}.{$this->instance_plural_name}")->with('data', $data);
    }

    public function addGroupForm (Request $request)
    {
        $data = $this->getInstanceFormData($request);

        return view("{$this->instance_name}.add_{$this->instance_name}_form")->with('data', $data);
    }

    public function addOrUpdateGroup (Request $request)
    {

        $validator = Validator::make($request->all(), $this->model_name::rules($request), [], $this->model_name::attrNames());
        if ($validator->fails()) {
            if (isset($request->updating_id)) {
                return redirect()->route("{$this->instance_name}-form", ['updating_id' => $request->updating_id])->withErrors($validator)->withInput();
            }
            return redirect()->route("{$this->instance_name}-form")->withErrors($validator)->withInput();
        }

        $data = $this->addOrUpdateInstance($request);

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
            "schedule_{$this->instance_name}_id" => "required|integer|exists:{$this->model_name},id",
            'week_number' => 'nullable|string'
        ]);
        if ($validator->fails()) {
            return redirect()->route("{$this->instance_name}-schedule")->withErrors($validator);
        }

        $data = $this->getSchedule($request);

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }

        return view("{$this->instance_name}.{$this->instance_name}_schedule")->with('data', $data);
    }

    public function getGroupReschedule (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|integer|exists:App\Group,id',
            'teacher_id' => 'required|integer|exists:App\Teacher,id',
            'lesson_id' => 'required|integer|exists:App\Lesson,id'
        ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }

        $reschedule_data = LessonHelpers::getReschedulingData($request->all());
        $data = $this->getModelRechedulingData($request, $reschedule_data['free_periods']);

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }
        
        return view("{$this->instance_name}.{$this->instance_name}_reschedule")->with('data', $data);
    }

    public function exportScheduleToDoc (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lessons' => 'required|string',
            'group_name' => 'required|string',
            'week_data' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return redirect()->route("{$this->instance_name}-schedule")->withErrors($validator); 
        }

        $data = $request->all();
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
            return redirect()->back()->withErrors($validator); 
        }

        $data = $request->all();
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
