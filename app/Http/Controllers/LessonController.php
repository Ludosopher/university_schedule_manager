<?php

namespace App\Http\Controllers;

use App\DocExporters\OneTable\WeekSchedule\DocExporterReplacementWeekSchedule;
use App\DocExporters\OneTable\DocExporterTable;
use App\Factories\DocExporterFactory;
use App\Helpers\ResponseHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\lesson\DeleteLessonRequest;
use App\Http\Requests\lesson\FilterLessonRequest;
use App\Http\Requests\lesson\RescheduleLessonRequest;
use App\Http\Requests\lesson\StoreLessonRequest;
use App\Services\LessonService;
use App\Services\ScheduleServices\TeacherScheduleService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class LessonController extends Controller
{
    /** @var TeacherScheduleService $teacherService */
    private $teacherService;
    /** @var LessonService $lessonService */
    private $lessonService;
    

    public function __construct(
        TeacherScheduleService $teacherService,
        LessonService $lessonService
        )
    {
        $this->teacherService = $teacherService;
        $this->lessonService = $lessonService;
    }    

    /**
     * Get filtered list of lessons and render the lessons index view.
     */
    public function getLessons (FilterLessonRequest $request): Renderable
    {
        $request->validated();
        $data = $this->lessonService->getInstances(request()->all());

        return view("lesson.lessons")->with('data', $data);
    }

    /**
     * Display the form for adding or updating a lesson.
     */
    public function addLessonForm (Request $request): Renderable
    {
        $data = $this->lessonService->getInstanceFormData($request->all());

        if (isset($data['updating_instance'])) {
            $data = $this->lessonService->getManyToManyData($data);
        }
        return view("lesson.add_lesson_form")->with('data', $data);
    }

    /**
     * Add or update lesson.
     */
    public function addOrUpdateLesson (StoreLessonRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $lesson = $this->lessonService->addOrUpdateInstance($validated);
        $this->lessonService->addOrUpdateManyToManyAttributes($validated, $lesson['id']);

        $response_content = ResponseHelpers::getContent($lesson, 'lesson');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * Delete lesson.
     */
    public function deleteLesson (DeleteLessonRequest $request): RedirectResponse
    {
        $this->lessonService->deleteManyToManyAttributes($request->validated()['deleting_id']);
        
        $deleted_instance = $this->lessonService->deleteInstance($request->validated()['deleting_id']);
        $response_content = ResponseHelpers::getContent($deleted_instance, 'lesson');
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * Display the lesson week schedule replacement variants.
     *
     * @return Renderable|RedirectResponse
     */
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

        $data = $this->teacherService->getReplacementData($request->all());
        $data['in_schedule'] = $this->teacherService->getReplacementSchedule($teacher_id, $data, $request->all());
        
        return view("lesson.replacement_lessons")->with('data', $data);
    }

    /**
     * Display the lesson week schedule rescheduling variants.
     */
    public function getReschedulingVariants (RescheduleLessonRequest $request): Renderable
    {
        $request->flash();
        $data = $this->lessonService->getReschedulingData($request->validated());

        return view("lesson.lesson_reschedule")->with('data', $data);
    }

    /**
     * Export the lesson week schedule replacement variants table to a Word document.
     *
     * @return void|RedirectResponse
     */
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

        $objWriter = DocExporterFactory::createTableDocExporter($validation['validated'])->createWriter();
        $objWriter->save("php://output");
    }

    /**
     * Export the lesson week schedule replacement variants matrix to a Word document.
     *
     * @return void|RedirectResponse
     */
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

        $objWriter = DocExporterFactory::createWeekScheduleReplaceDocExporter($data)->createWriter();
        $objWriter->save("php://output");
    }
}
