<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelpers;
use App\Helpers\ResponseHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\replacement_request\DeleteReplacementReqRequest;
use App\Http\Requests\replacement_request\FilterReplacementReqRequest;
use App\Http\Requests\replacement_request\SendReplacementReqRequest;
use App\Http\Requests\replacement_request\StoreReplacementReqRequest;
use App\Services\ReplacementRequestService;
use App\Services\ScheduleServices\TeacherScheduleService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReplacementRequestController extends Controller
{
    /** @var TeacherScheduleService $teacherService */
    private $teacherService;
    /** @var ReplacementRequestService $replacementService */
    private $replacementService;
    

    public function __construct(
        TeacherScheduleService $teacherService,
        ReplacementRequestService $replacementService
        )
    {
        $this->teacherService = $teacherService;
        $this->replacementService = $replacementService;
    }

    /**
     * Display filtered list of replacement requests.
     */
    public function getReplacementRequests (FilterReplacementReqRequest $request): Renderable
    {
        $request->validated();
        $data = $this->replacementService->getInstances(request()->all());

        return view("replacement_request.replacement_requests")->with('data', $data);
    }

    /**
     * Display list of rurrent user replacement requests.
     */
    public function getMyReplacementRequests (Request $request): Renderable
    {
        $data = $this->replacementService->getMyReplacementRequests(Auth::user()->id);
        
        return view("replacement_request.my_replacement_requests")->with('data', $data);
    }

    /**
     * Add replacement request.
     */
    public function addReplacementRequest (Request $request): RedirectResponse
    {
        $validation = ValidationHelpers::addReplacementRequestValidation($request->all());
        if (! $validation['success']) {
            return redirect()->route("lesson-replacement", ResponseHelpers::getLessonReplacementBackData($request->all()))
                             ->withErrors($validation['validator']);
        }

        $new_request = $this->replacementService->addOrUpdateInstance($validation['validated']);

        $response_content = ResponseHelpers::getContent($new_request, 'replacement_request');
        
        return redirect()->route("my_replacement_requests")->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * Update replacement request.
     */
    public function updateReplacementRequest (StoreReplacementReqRequest $request): RedirectResponse
    {
        $replacement_request = $this->replacementService->addOrUpdateInstance($request->validated());

        $response_content = ResponseHelpers::getContent($replacement_request, 'replacement_request');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * Delete replacement request.
     */
    public function deleteReplacementRequest (DeleteReplacementReqRequest $request): RedirectResponse
    {
        $deleted_instance = $this->replacementService->deleteReplacementRequest($request->validated()['deleting_id']);

        $response_content = ResponseHelpers::getContent($deleted_instance, 'replacement_request');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * Send replacement request.
     */
    public function sendReplacementRequest (SendReplacementReqRequest $request): RedirectResponse
    {
        $data = $this->teacherService->getReplacingTeacherSchedule($request->validated());
        MailHelpers::sendReplacementRequest($data);
        $replacement_request = $this->replacementService->addOrUpdateInstance($request->validated());

        $response_content = ResponseHelpers::getContent($replacement_request, 'replacement_request');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => __('replacement_request.replacement_request_sended')."\n".$response_content['message']
        ]);
    }

    /**
     * Open replacement request chat.
     */
    public function openReplacementRequestChat (Request $request): Renderable
    {
        return view("replacement_request.chat")->with('data', $request->all());
    }
}
