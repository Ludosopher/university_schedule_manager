<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\teacher\DeleteTeacherRequest;
use App\Http\Requests\teacher\ExportScheduleToDocTeacherRequest;
use App\Http\Requests\teacher\FilterTeacherRequest;
use App\Http\Requests\teacher\MonthScheduleTeacherRequest;
use App\Http\Requests\teacher\ScheduleTeacherRequest;
use App\Http\Requests\teacher\StoreTeacherRequest;
use App\Services\LessonService;
use App\Services\ScheduleServices\TeacherScheduleService;
use App\Factories\DocExporterFactory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
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
     * Display filtered list of teachers.
     */    
    public function getTeachers (FilterTeacherRequest $request): Renderable
    {
        $request->validated();
        $data = $this->teacherService->getInstances(request()->all());

        return view("teacher.teachers")->with('data', $data);
    }

    /**
     * Display the form for adding or updating a teacher.
     */
    public function addTeacherForm (Request $request): Renderable
    {
        $data = $this->teacherService->getInstanceFormData($request->all());

        return view("teacher.add_teacher_form")->with('data', $data);
    }

    /**
     * Add or update teacher.
     */
    public function addOrUpdateTeacher (StoreTeacherRequest $request): RedirectResponse
    {
        $data = $this->teacherService->addOrUpdateInstance($request->validated());

        $response_content = ResponseHelpers::getContent($data, 'teacher');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * Delete teacher.
     */
    public function deleteTeacher (DeleteTeacherRequest $request): RedirectResponse
    {
        $deleted_instance = $this->teacherService->deleteInstance($request->validated()['deleting_id']);

        $response_content = ResponseHelpers::getContent($deleted_instance, 'teacher');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * Display the teacher week schedule.
     *
     * @return Renderable|RedirectResponse
     */
    public function getTeacherSchedule (ScheduleTeacherRequest $request)
    {
        $data = $this->teacherService->getSchedule($request->validated());
        if (isset($data['duplicated_lesson'])) {
            $response_content = ResponseHelpers::getContent($data, 'teacher');
        
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
        }
    
        return view("teacher.teacher_schedule")->with('data', $data);
    }

    /**
     * Display the teacher month schedule.
     *
     * @return Renderable|RedirectResponse
     */
    public function getMonthTeacherSchedule (MonthScheduleTeacherRequest $request)
    {
        $data = $this->teacherService->getMonthSchedule($request->validated());
        request()->flash();
        if (isset($data['duplicated_lesson'])) {
            $response_content = ResponseHelpers::getContent($data, 'teacher');
        
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
        }

        return view("teacher.teacher_month_schedule")->with('data', $data);
    }

    /**
     * Display reschedule variants in the teacher week schedule.
     *
     * @return Renderable|RedirectResponse
     */
    public function getTeacherReschedule (Request $request)
    {
        $request->flash();
        $validation = ValidationHelpers::getTeacherRescheduleValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->input('prev_data'), true);
            return redirect()->route('lesson-rescheduling', $prev_data)->withErrors($validation['validator']);
        }

        $reschedule_data = $this->lessonService->getReschedulingData($validation['validated']);
        $data = $this->teacherService->getModelRechedulingData($validation['validated'], $reschedule_data);

        return view("teacher.teacher_reschedule")->with('data', $data);
    }

    /**
     * Export teacher week schedule to a Word document.
     */
    public function exportScheduleToDoc (ExportScheduleToDocTeacherRequest $request): void
    {
        $data = $request->validated();
        $data['other_participant'] = 'group';

        $filename = "teacher_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExporterFactory::createRegWeekScheduleDocExporter($data)->createWriter();
        $objWriter->save("php://output");
    }

    /**
     * Export teacher month schedule to a Word document.
     * @return void|RedirectResponse
     */
    public function exportMonthScheduleToDoc (Request $request)
    {
        $request->flash();
        $validation = ValidationHelpers::exportMonthTeacherScheduleToDocValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->input('prev_data'), true);
            return redirect()->route('teacher-month-schedule', $prev_data)->withErrors($validation['validator']);
        }

        $data = $validation['validated'];
        $data['other_participant'] = 'group';

        $filename = "teacher_month_schedule.docx";
        
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExporterFactory::createMonthScheduleDocExporter($data)->createWriter();
        $objWriter->save("php://output");
    }

    /**
     * Export teacher week reschedule variants to a Word document.
     * @return void|RedirectResponse
     */
    public function exportRescheduleToDoc (Request $request)
    {
        $validation = ValidationHelpers::exportTeacherRescheduleToDocValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->all()['prev_data'], true);
            return redirect()->route('teacher-reschedule', $prev_data)->withErrors($validation['validator']);
        }

        $data = $validation['validated'];
        $data['participant'] = $request->teacher_name;
        $data['other_participant'] = 'group';
        $data['is_reschedule_for'] = 'teacher';

        $filename = "teacher_reschedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExporterFactory::createWeekRescheduleDocExporter($data)->createWriter();
        $objWriter->save("php://output");
    }
}
