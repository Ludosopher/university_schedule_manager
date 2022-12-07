<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Group;
use App\Helpers\DocExportHelpers;
use App\Helpers\LessonHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\ValidationHelpers;
use App\Helpers\ValidatorHelpers;
use App\Http\Requests\lesson\FilterLessonRequest;
use App\Http\Requests\lesson\RescheduleLessonRequest;
use App\Http\Requests\lesson\StoreLessonRequest;
use App\Lesson;
use App\Teacher;
use App\WeekDay;
use App\WeeklyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends ModelController
{
    protected $model_name = 'App\Lesson';
    protected $instance_name = 'lesson';
    protected $instance_plural_name = 'lessons';
    protected $instance_name_field = 'name';
    protected $profession_level_name_field = null;
    protected $eager_loading_fields = ['lesson_type', 'week_day', 'weekly_period', 'class_period', 'teacher', 'groups'];
    protected $other_lesson_participant = null;
    protected $other_lesson_participant_name = null;
            
    public function getLessons (FilterLessonRequest $request)
    {
        $data = $this->getInstances($request->validated());

        return view("lesson.lessons")->with('data', $data);
    }

    public function addLessonForm (Request $request)
    {
        $data = $this->getInstanceFormData($request);
        
        if (isset($data['updating_instance'])) {
            $data = LessonHelpers::getGroupsData($data);    
        }
         
        return view("lesson.add_lesson_form")->with('data', $data);
    }

    public function addOrUpdateLesson (StoreLessonRequest $request)
    {
        $validated = $request->validated();
        $data = $this->addOrUpdateInstance($validated);
        LessonHelpers::addOrUpdateLessonGroups($validated['group_id'], $data['id']);
                
        if (is_array($data)) {
            if (isset($data['updated_instance_name'])) {
                return redirect()->route("lessons", ['updated_instance_name' => $data['updated_instance_name']]);
            } elseif (isset($data['new_instance_name'])) {
                return redirect()->route("lesson-form", ['new_instance_name' => $data['new_instance_name']]);
            }
        }
    }

    public function deleteLesson (Request $request)
    {
        $relations_deleted_result = LessonHelpers::deleteLessonGroupsRelation($request->deleting_id);
        if (!$relations_deleted_result) {
            return redirect()->route("lessons", ['deleting_instance_not_found' => true]);
        }
        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->model_name);
        $instance_name_field = $this->instance_name_field;
        return redirect()->route("lessons", ['deleted_instance_name' => $deleted_instance->$instance_name_field]);
    }

    public function getReplacementVariants (Request $request)
    {
        if (isset($request->prev_replace_rules)) {
            $request->flash();
            $replace_rules = json_decode($request->all()['prev_replace_rules'], true);
            $parametrs = ValidationHelpers::getReplacementValidationParametrs();
            $validator = Validator::make($request->all(), $parametrs['rules'], $parametrs['messages'], $parametrs['attributes']);
            if ($validator->fails()) {
                return redirect()->route("{$this->instance_name}-replacement", ['replace_rules' => $replace_rules])->withErrors($validator)->withInput();
            }
            $teacher_id = $replace_rules['teacher_id'];
        } else {
            $teacher_id = $request->all()['replace_rules']['teacher_id'];
        }

        $data = LessonHelpers::getReplacementData($request->all());
        $data['in_schedule'] = LessonHelpers::getReplacementSchedule($teacher_id, $data['replacement_lessons'], $request->week_data);
       
        return view("lesson.replacement_lessons")->with('data', $data);
    }

    public function getReschedulingVariants (RescheduleLessonRequest $request)
    {
        $request->flash();

        $data = LessonHelpers::getReschedulingData($request->validated());

        return view("lesson.lesson_reschedule")->with('data', $data);
    }
    
    public function exportReplacementToDoc (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'replacement_lessons' => 'required|string',
            'header_data' => 'required|string',
        ]);
        if ($validator->fails()) {
            $replace_rules = json_decode($request->all()['prev_replace_rules'], true);
            return redirect()->route("lesson-replacement", ['replace_rules' => $replace_rules])->withErrors($validator); 
        }

        $filename = "replacement.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::replacementExport($validator->validated());
        $objWriter->save("php://output");
    }

    public function exportReplacementScheduleToDoc (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lessons' => 'required|string',
            'header_data' => 'required|string',
            'week_data' => 'nullable|string',
            'replaceable_lesson_id' => 'required|integer|exists:App\Lesson,id'
        ]);
        if ($validator->fails()) {
            $replace_rules = json_decode($request->all()['prev_replace_rules'], true);
            return redirect()->route("lesson-replacement", ['replace_rules' => $replace_rules])->withErrors($validator); 
        }

        $data = $validator->validated();
        $data['other_participant'] = 'group';

        $filename = "replacement-schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
        $objWriter->save("php://output");
    }
}
