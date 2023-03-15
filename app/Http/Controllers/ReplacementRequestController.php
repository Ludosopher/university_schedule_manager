<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\ReplacementRequestHelpers;
use App\Helpers\ResponseHelpers;
use App\Helpers\TeacherHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\replacement_request\DeleteReplacementReqRequest;
use App\Http\Requests\replacement_request\FilterReplacementReqRequest;
use App\Http\Requests\replacement_request\SendReplacementReqRequest;
use App\Http\Requests\replacement_request\StoreReplacementReqRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReplacementRequestController extends Controller
{
    public $config = [
        'model_name' => 'App\ReplacementRequest',
        'instance_name' => 'replacement_request',
        'instance_plural_name' => 'replacement_requests',
        'instance_name_field' => 'name',
        'profession_level_name_field' => null,
        'eager_loading_fields' => ['status', 'replaceable_lesson', 'replacing_lesson', 'initiator', 'messages'],
        'other_lesson_participant' => null,
        'other_lesson_participant_name' => null,
        'boolean_attributes' => [],
        'many_to_many_attributes' => ['is_regular'],
    ];

    public function getReplacementRequests (FilterReplacementReqRequest $request)
    {
        $request->validated();
        $data = ModelHelpers::getInstances(request()->all(), $this->config);
//dd($data);
        return view("replacement_request.replacement_requests")->with('data', $data);
    }

    public function getMyReplacementRequests (Request $request)
    {
 
        $data = ReplacementRequestHelpers::getMyReplacementRequests(Auth::user()->id, $this->config);
        
        return view("replacement_request.my_replacement_requests")->with('data', $data);
    }

    public function addReplacementRequest (Request $request)
    {
        $validation = ValidationHelpers::addReplacementRequestValidation($request->all());
        if (! $validation['success']) {
            return redirect()->route("lesson-replacement", ResponseHelpers::getLessonReplacementBackData($request->all()))
                             ->withErrors($validation['validator']);
        }

        $new_request = ModelHelpers::addOrUpdateInstance($validation['validated'], $this->config);

        $response_content = ResponseHelpers::getContent($new_request, $this->config['instance_name']);
        
        return redirect()->route("my_replacement_requests")->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function updateReplacementRequest (StoreReplacementReqRequest $request)
    {
        $replacement_request = ModelHelpers::addOrUpdateInstance($request->validated(), $this->config);

        $response_content = ResponseHelpers::getContent($replacement_request, $this->config['instance_name']);
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function deleteReplacementRequest (DeleteReplacementReqRequest $request)
    {
        $deleted_instance = ReplacementRequestHelpers::deleteReplacementRequest($request->validated()['deleting_id'], $this->config);

        $response_content = ResponseHelpers::getContent($deleted_instance, $this->config['instance_name']);
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function sendReplacementRequest (SendReplacementReqRequest $request)
    {
        $data = TeacherHelpers::getReplacingTeacherSchedule($request->validated());
        MailHelpers::sendReplacementRequest($data);
        $replacement_request = ModelHelpers::addOrUpdateInstance($request->validated(), $this->config);

        $response_content = ResponseHelpers::getContent($replacement_request, $this->config['instance_name']);
        
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
