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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    protected $config = [
        'model_name' => 'App\Lesson',
        'instance_name' => 'lesson',
        'instance_plural_name' => 'lessons',
        'instance_name_field' => 'name',
        'profession_level_name_field' => null,
        'eager_loading_fields' => ['lesson_type', 'week_day', 'weekly_period', 'class_period', 'teacher', 'groups'],
        'other_lesson_participant' => null,
        'other_lesson_participant_name' => null,
        'boolean_attributes' => [],
        'many_to_many_attributes' => ['group_id' => 'groups'],
    ];

    public function getLessons (FilterLessonRequest $request)
    {
        $request->validated();
        $data = ModelHelpers::getInstances(request()->all(), $this->config);

        return view("lesson.lessons")->with('data', $data);
    }

    public function addLessonForm (Request $request)
    {
        $data = ModelHelpers::getInstanceFormData($request->all(), $this->config);

        if (isset($data['updating_instance'])) {
            $data = ModelHelpers::getManyToManyData($data, $this->config['many_to_many_attributes']);
        }

        return view("lesson.add_lesson_form")->with('data', $data);
    }

    public function addOrUpdateLesson (StoreLessonRequest $request)
    {
        $validated = $request->validated();
        $lesson = ModelHelpers::addOrUpdateInstance($validated, $this->config);
        ModelHelpers::addOrUpdateManyToManyAttributes($validated, $lesson['id'], $this->config['model_name'], $this->config['many_to_many_attributes']);

        if (is_array($lesson)) {
            if (isset($lesson['updated_instance_name'])) {
                return redirect()->route("lessons", ['updated_instance_name' => $lesson['updated_instance_name']]);
            } elseif (isset($lesson['new_instance_name'])) {
                return redirect()->route("lesson-add-form", ['new_instance_name' => $lesson['new_instance_name']]);
            }
        }
    }

    public function deleteLesson (Request $request)
    {
        $attributes = array_values($this->config['many_to_many_attributes']);
        $relations_deleted_result = ModelHelpers::deleteManyToManyAttributes($request->deleting_id, $this->config['model_name'], $attributes);
        if (!$relations_deleted_result) {
            return redirect()->route("lessons", ['deleting_instance_not_found' => true]);
        }
        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->config['model_name']);
        $instance_name_field = $this->config['instance_name_field'];
        return redirect()->route("lessons", ['deleted_instance_name' => $deleted_instance->$instance_name_field]);
    }

    public function getReplacementVariants (Request $request)
    {
        if (isset($request->prev_replace_rules)) {
            $request->flash();
            $replace_rules = json_decode($request->prev_replace_rules, true);

            $validation = ValidationHelpers::getReplacementVariantsValidation($request->all());
            if (! $validation['success']) {
                return redirect()->route("lesson-replacement", [
                    'replace_rules' => $replace_rules,
                    'week_data' => $request->prev_week_data,
                    'week_dates' => $request->prev_week_dates,
                    'is_red_week' => $request->prev_is_red_week,
                ])->withInput()->withErrors($validation['validator']);
            }
            $teacher_id = $replace_rules['teacher_id'];
        } else {
            $teacher_id = $request->replace_rules['teacher_id'];
        }

        $data = LessonHelpers::getReplacementData($request->all());
        $data['in_schedule'] = LessonHelpers::getReplacementSchedule($teacher_id, $data, $request->all());
        
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
        $validation = ValidationHelpers::exportReplacementToDocValidation($request->all());
        if (! $validation['success']) {
            $replace_rules = json_decode($request->all()['prev_replace_rules'], true);
            return redirect()->route("lesson-replacement", [
                'replace_rules' => $replace_rules,
                'week_data' => $request->week_data,
                'week_dates' => $request->week_dates,
                'is_red_week' => $request->is_red_week,
            ])->withErrors($validation['validator']);
        }

        $filename = "replacement.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::replacementExport($validation['validated']);
        $objWriter->save("php://output");
    }

    public function exportReplacementScheduleToDoc (Request $request)
    {
        $validation = ValidationHelpers::exportReplacementScheduleToDocValidation($request->all());
        if (! $validation['success']) {
            $replace_rules = json_decode($request->all()['prev_replace_rules'], true);
            return redirect()->route("lesson-replacement", [
                'replace_rules' => $replace_rules,
                'week_data' => $request->week_data,
                'week_dates' => $request->week_dates,
                'is_red_week' => $request->is_red_week,
            ])->withErrors($validation['validator']);
        }

        $data = $validation['validated'];
        $data['other_participant'] = 'group';

        $filename = "replacement-schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExportHelpers::scheduleExport($data);
        $objWriter->save("php://output");
    }
}
