<?php

namespace App\Http\Controllers;

use App\Helpers\DocExportHelpers;
use App\Helpers\LessonHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\ResponseHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\lesson\DeleteLessonRequest;
use App\Http\Requests\lesson\FilterLessonRequest;
use App\Http\Requests\lesson\RescheduleLessonRequest;
use App\Http\Requests\lesson\StoreLessonRequest;
use Illuminate\Http\Request;


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

        $response_content = ResponseHelpers::getContent($lesson, $this->config['instance_name']);
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function deleteLesson (DeleteLessonRequest $request)
    {
        $attributes = array_values($this->config['many_to_many_attributes']);
        ModelHelpers::deleteManyToManyAttributes($request->validated()['deleting_id'], $this->config['model_name'], $attributes);
        
        $deleted_instance = ModelHelpers::deleteInstance($request->validated()['deleting_id'], $this->config);
        $response_content = ResponseHelpers::getContent($deleted_instance, $this->config['instance_name']);
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function getReplacementVariants (Request $request)
    {
        $validation = ValidationHelpers::getReplacementVariantsValidation($request->all());
        if (isset($request->prev_replace_rules)) {
            $request->flash();
            $replace_rules = json_decode($request->prev_replace_rules, true);
            if (! $validation['success']) {
                return redirect()->route("lesson-replacement", ResponseHelpers::getLessonReplacementBackData($request->all()))
                                 ->withInput()->withErrors($validation['validator']);
            }
            $teacher_id = $replace_rules['teacher_id'];
        } else {
            if (! $validation['success']) {
                return redirect()->back()->withErrors($validation['validator']);
            }
            $teacher_id = $request->replace_rules['teacher_id'];
        }

        $data = LessonHelpers::getReplacementData($request->all());
        $data['in_schedule'] = LessonHelpers::getReplacementSchedule($teacher_id, $data, $request->all());
        
        return view("lesson.replacement_lessons")->with('data', $data);
    }

    public function getReschedulingVariants (RescheduleLessonRequest $request)
    {
//dd($request->validated());        
        $request->flash();
        $data = LessonHelpers::getReschedulingData($request->validated());

        return view("lesson.lesson_reschedule")->with('data', $data);
    }

    public function exportReplacementToDoc (Request $request)
    {
        $validation = ValidationHelpers::exportReplacementToDocValidation($request->all());
        if (! $validation['success']) {
            return redirect()->route("lesson-replacement", ResponseHelpers::getLessonReplacementBackData($request->all()))
                             ->withErrors($validation['validator']);
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
            return redirect()->route("lesson-replacement", ResponseHelpers::getLessonReplacementBackData($request->all()))
                             ->withErrors($validation['validator']);
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
