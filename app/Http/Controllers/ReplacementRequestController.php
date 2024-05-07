<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelpers;
use App\Helpers\ResponseHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\replacement_request\DeleteReplacementReqRequest;
use App\Http\Requests\replacement_request\FilterReplacementReqRequest;
use App\Http\Requests\replacement_request\SendReplacementReqRequest;
use App\Http\Requests\replacement_request\StoreReplacementReqRequest;
use App\Instances\ReplacementRequestInstance;
use App\Instances\ScheduleElements\TeacherScheduleElement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReplacementRequestController extends Controller
{
    public function getReplacementRequests (FilterReplacementReqRequest $request)
    {
        $request->validated();
        $data = (new ReplacementRequestInstance())->getInstances(request()->all());

        return view("replacement_request.replacement_requests")->with('data', $data);
    }

    public function getMyReplacementRequests (Request $request)
    {
 
        $data = (new ReplacementRequestInstance())->getMyReplacementRequests(Auth::user()->id);
        
        return view("replacement_request.my_replacement_requests")->with('data', $data);
    }

    public function addReplacementRequest (Request $request)
    {
        $validation = ValidationHelpers::addReplacementRequestValidation($request->all());
        if (! $validation['success']) {
            return redirect()->route("lesson-replacement", ResponseHelpers::getLessonReplacementBackData($request->all()))
                             ->withErrors($validation['validator']);
        }

        $new_request = (new ReplacementRequestInstance())->addOrUpdateInstance($validation['validated']);

        $response_content = ResponseHelpers::getContent($new_request, 'replacement_request');
        
        return redirect()->route("my_replacement_requests")->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function updateReplacementRequest (StoreReplacementReqRequest $request)
    {
        $replacement_request = (new ReplacementRequestInstance())->addOrUpdateInstance($request->validated());

        $response_content = ResponseHelpers::getContent($replacement_request, 'replacement_request');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function deleteReplacementRequest (DeleteReplacementReqRequest $request)
    {
        $deleted_instance = (new ReplacementRequestInstance())->deleteReplacementRequest($request->validated()['deleting_id']);

        $response_content = ResponseHelpers::getContent($deleted_instance, 'replacement_request');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function sendReplacementRequest (SendReplacementReqRequest $request)
    {
        $data = (new TeacherScheduleElement)->getReplacingTeacherSchedule($request->validated());
        MailHelpers::sendReplacementRequest($data);
        $replacement_request = (new ReplacementRequestInstance())->addOrUpdateInstance($request->validated());

        $response_content = ResponseHelpers::getContent($replacement_request, 'replacement_request');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => __('replacement_request.replacement_request_sended')."\n".$response_content['message']
        ]);
    }

    public function openReplacementRequestChat (Request $request)
    {
        return view("replacement_request.chat")->with('data', $request->all());
    }
}
