<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\lesson\DeleteLessonRequest;
use App\Http\Requests\lesson\FilterLessonRequest;
use App\Http\Requests\lesson\RescheduleLessonRequest;
use App\Http\Requests\lesson\StoreLessonRequest;
use App\Instances\LessonInstance;
use App\Instances\ScheduleElements\ScheduleElement;
use App\Instances\ScheduleElements\TeacherScheduleElement;
use Illuminate\Http\Request;


class LessonController extends Controller
{
    public function getLessons (FilterLessonRequest $request)
    {
        $request->validated();
        $data = (new LessonInstance())->getInstances(request()->all());

        return view("lesson.lessons")->with('data', $data);
    }

    public function addLessonForm (Request $request)
    {
        $data = (new LessonInstance())->getInstanceFormData($request->all());

        if (isset($data['updating_instance'])) {
            $data = (new LessonInstance())->getManyToManyData($data);
        }
        return view("lesson.add_lesson_form")->with('data', $data);
    }

    public function addOrUpdateLesson (StoreLessonRequest $request)
    {
        $validated = $request->validated();
        $lesson = (new LessonInstance())->addOrUpdateInstance($validated);
        (new LessonInstance())->addOrUpdateManyToManyAttributes($validated, $lesson['id']);

        $response_content = ResponseHelpers::getContent($lesson, 'lesson');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function deleteLesson (DeleteLessonRequest $request)
    {
        (new LessonInstance())->deleteManyToManyAttributes($request->validated()['deleting_id']);
        
        $deleted_instance = (new LessonInstance())->deleteInstance($request->validated()['deleting_id']);
        $response_content = ResponseHelpers::getContent($deleted_instance, 'lesson');
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

        $data = (new TeacherScheduleElement)->getReplacementData($request->all());
        $data['in_schedule'] = (new TeacherScheduleElement)->getReplacementSchedule($teacher_id, $data, $request->all());
        
        return view("lesson.replacement_lessons")->with('data', $data);
    }

    public function getReschedulingVariants (RescheduleLessonRequest $request)
    {
        $request->flash();
        $data = (new LessonInstance)->getReschedulingData($request->validated());

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

        $objWriter = (new ScheduleElement)->replacementExport($validation['validated']);
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

        $objWriter = (new ScheduleElement)->scheduleExport($data);
        $objWriter->save("php://output");
    }
}
