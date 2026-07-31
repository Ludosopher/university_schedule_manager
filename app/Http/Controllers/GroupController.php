<?php

namespace App\Http\Controllers;

use App\DocExporters\ManyTables\DocExporterMonthSchedule;
use App\DocExporters\OneTable\WeekSchedule\DocExporterOrdinaryWeekSchedule;
use App\DocExporters\OneTable\WeekSchedule\DocExporterWeekReschedule;
use App\Factories\DocExporterFactory;
use App\Helpers\ResponseHelpers;
use App\Helpers\ValidationHelpers;
use App\Services\LessonService;
use App\Http\Requests\group\DeleteGroupRequest;
use App\Http\Requests\group\ExportScheduleToDocGroupRequest;
use App\Http\Requests\group\FilterGroupRequest;
use App\Http\Requests\group\MonthScheduleGroupRequest;
use App\Http\Requests\group\ScheduleGroupRequest;
use App\Http\Requests\group\StoreGroupRequest;
use App\Services\ScheduleServices\GroupScheduleService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /** @var GroupScheduleService $groupService */
    private $groupService;
    /** @var LessonService $lessonService */
    private $lessonService;
    

    public function __construct(
        GroupScheduleService $groupService,
        LessonService $lessonService
        )
    {
        $this->groupService = $groupService;
        $this->lessonService = $lessonService;
    }    

    /**
     * Display filtered list of of study groups.
     */    
    public function getGroups (FilterGroupRequest $request): Renderable
    {
        $request->validated();
        $data = $this->groupService->getInstances(request()->all());

        return view("group.groups")->with('data', $data);
    }

    /**
     * Display the form for adding or updating a study group.
     */
    public function addGroupForm (Request $request): Renderable
    {
        $data = $this->groupService->getInstanceFormData($request->all());

        return view("group.add_group_form")->with('data', $data);
    }

    /**
     * Add or update study group.
     */
    public function addOrUpdateGroup (StoreGroupRequest $request): RedirectResponse
    {
        $data = $this->groupService->addOrUpdateInstance($request->validated());

        $response_content = ResponseHelpers::getContent($data, 'group');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * Delete teacher study group.
     */
    public function deleteGroup (DeleteGroupRequest $request): RedirectResponse
    {
        $relation_delited_result = $this->groupService->deleteGroupLessonRelation($request->validated()['deleting_id']);
        if (isset($relation_delited_result['there_are_lessons_only_with_this_group'])) {
            $response_content = ResponseHelpers::getContent($relation_delited_result, 'group');
            
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
        }
        
        $deleted_instance = $this->groupService->deleteInstance($request->validated()['deleting_id']);
        $response_content = ResponseHelpers::getContent($deleted_instance, 'group');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * Display the study group week schedule.
     *
     * @return Renderable|RedirectResponse
     */
    public function getGroupSchedule (ScheduleGroupRequest $request)
    {
        $data = $this->groupService->getSchedule($request->validated());
        if (isset($data['duplicated_lesson'])) {
            $response_content = ResponseHelpers::getContent($data, 'group');
        
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
        }

        return view("group.group_schedule")->with('data', $data);
    }

    /**
     * Display the study group month schedule.
     *
     * @return Renderable|RedirectResponse
     */
    public function getMonthGroupSchedule (MonthScheduleGroupRequest $request)
    {
        $data = $this->groupService->getMonthSchedule($request->validated());
        request()->flash();
        if (isset($data['duplicated_lesson'])) {
            $response_content = ResponseHelpers::getContent($data, 'group');
        
            return redirect()->back()->with('response', [
                'success' => $response_content['success'],
                'message' => $response_content['message']
            ]);
        }

        return view("group.group_month_schedule")->with('data', $data);
    }

    /**
     * Display reschedule variants in the study group week schedule.
     *
     * @return Renderable|RedirectResponse
     */
    public function getGroupReschedule (Request $request)
    {
        $request->flash();
        $validation = ValidationHelpers::getGroupRescheduleValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->input('prev_data'), true);
            return redirect()->route('lesson-rescheduling', $prev_data)->withErrors($validation['validator']);
        }

        $reschedule_data = $this->lessonService->getReschedulingData($validation['validated']);
        $data = $this->groupService->getModelRechedulingData($validation['validated'], $reschedule_data);

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("lessons")->with('duplicated_lesson', $data['duplicated_lesson']);
        }

        return view("group.group_reschedule")->with('data', $data);
    }

    /**
     * Export study group week schedule to a Word document.
     */
    public function exportScheduleToDoc (ExportScheduleToDocGroupRequest $request): void
    {
        $data = $request->validated();
        $data['other_participant'] = 'teacher';

        $filename = "group_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExporterFactory::createRegWeekScheduleDocExporter($data)->createWriter();
        $objWriter->save("php://output");
    }

    /**
     * Export study group month schedule to a Word document.
     * @return Renderable|RedirectResponse
     */
    public function exportMonthScheduleToDoc (Request $request)
    {
        $request->flash();
        $validation = ValidationHelpers::exportMonthGroupScheduleToDocValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->input('prev_data'), true);
            return redirect()->route('group-month-schedule', $prev_data)->withErrors($validation['validator']);
        }

        $data = $validation['validated'];
        $data['other_participant'] = 'teacher';

        $filename = "teacher_month_schedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExporterFactory::createMonthScheduleDocExporter($data)->createWriter();
        $objWriter->save("php://output");
    }

    /**
     * Export study group week reschedule variants to a Word document.
     * @return Renderable|RedirectResponse
     */
    public function exportRescheduleToDoc (Request $request)
    {
        $validation = ValidationHelpers::exportGroupRescheduleToDocValidation($request->all());
        if (! $validation['success']) {
            $prev_data = json_decode($request->all()['prev_data'], true);
            return redirect()->route('group-reschedule', $prev_data)->withErrors($validation['validator']);
        }

        $data = $validation['validated'];
        $data['participant'] = $request->group_name;
        $data['other_participant'] = 'teacher';
        $data['is_reschedule_for'] = 'group';

        $filename = "group_reschedule.docx";
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document" );
        header( 'Content-Disposition: attachment; filename='.$filename);

        $objWriter = DocExporterFactory::createWeekRescheduleDocExporter($data)->createWriter();
        $objWriter->save("php://output");
    }
}
