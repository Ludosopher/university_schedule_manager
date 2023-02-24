<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\ReplacementRequestHelpers;
use App\Helpers\TeacherHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\replacement_request\FilterReplacementReqRequest;
use App\Http\Requests\replacement_request\SendReplacementReqRequest;
use App\Http\Requests\replacement_request\StoreReplacementReqRequest;
use App\Mail\MailReplacementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
            $replace_rules = json_decode($request->all()['prev_replace_rules'], true);
            return redirect()->route("lesson-replacement", [
                'replace_rules' => $replace_rules,
                'week_data' => $request->week_data,
                'week_dates' => $request->week_dates,
                'is_red_week' => $request->is_red_week,
            ])->withErrors($validation['validator']);
        }

        $new_request = ModelHelpers::addOrUpdateInstance($validation['validated'], $this->config);

        return redirect()->route("my_replacement_requests")->with('new_instance_name', $new_request['new_instance_name']);
    }

    public function updateReplacementRequest (StoreReplacementReqRequest $request)
    {
        $replacement_request = ModelHelpers::addOrUpdateInstance($request->validated(), $this->config);

        return redirect()->back()->with('updated_instance_name', $replacement_request['updated_instance_name']);
    }

    public function deleteReplacementRequest (Request $request)
    {
        $deleted_instance = ReplacementRequestHelpers::deleteReplacementRequest($request->deleting_id, $this->config['model_name']);

        if ($deleted_instance) {
            $instance_name_field = $this->config['instance_name_field'];
            return redirect()->route("my_replacement_requests")->with('deleted_instance_name', $deleted_instance->$instance_name_field);
        } else {
            return redirect()->route("my_replacement_requests")->with('deleting_instance_not_found', true);
        }
    }

    public function sendReplacementRequest (SendReplacementReqRequest $request)
    {
        $data = TeacherHelpers::getReplacingTeacherSchedule($request->validated());
        MailHelpers::sendReplacementRequest($data);
        $replacement_request = ModelHelpers::addOrUpdateInstance($request->validated(), $this->config);

        // if ($message_errors) {
        //     return redirect()->back()->with('response', [
        //         'errors' => "При отправке просьбы о замене возникли ошибки: \n".implode("\n", $message_errors),
        //         'results' => [$replacement_request['updated_instance_name']. ' успешно обновлена.'],
        //     ]);
        // }

        return redirect()->back()->with('response', [
            'results' => [
                "Просьба о замене успешно отправлена",
                $replacement_request['updated_instance_name']. 'успешно обновлена.',
            ],
        ]);
    }

    public function openReplacementRequestChat (Request $request)
    {
        return view("replacement_request.chat")->with('data', $request->all());
    }
}
