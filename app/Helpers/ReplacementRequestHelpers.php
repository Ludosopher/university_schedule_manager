<?php

namespace App\Helpers;

use App\Notifications\ReplacementRequestStatusChanged;
use App\ReplacementRequest;
use App\ReplacementRequestMessage;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;

class ReplacementRequestHelpers
{
    public static function getMyReplacementRequests ($initiator_id, $config)
    {
        $replacement_request_status_ids = config('enum.replacement_request_status_ids');

        $my_requests = ReplacementRequest::with(['status', 'replaceable_lesson.teacher.users', 'replacing_lesson.teacher.users', 'initiator', 'messages'])
                                          ->where(function ($query) use ($initiator_id) {
                                            $query->orWhereHas('replaceable_lesson.teacher.users', function (Builder $que) use ($initiator_id) {
                                                    $que->where('id', $initiator_id);
                                                })->orWhereHas('replacing_lesson.teacher.users', function (Builder $que) use ($initiator_id) {
                                                    $que->where('id', $initiator_id);
                                                });
                                          })->get();

        $result = [
            'my_requests' => [],
            'to_me_requests' => [],
            'user_id' => $initiator_id,
            'user_name' => User::where('id', $initiator_id)->value('name'),
        ];
        foreach ($my_requests as $request) {

            $replaceable_user_ids = $request->replaceable_lesson->teacher->users->pluck('id')->toArray();
            if (in_array($initiator_id, $replaceable_user_ids)) {
                $result['my_requests'][] = $request;
            }

            $replacing_user_ids = $request->replacing_lesson->teacher->users->pluck('id')->toArray();
            if (in_array($initiator_id, $replacing_user_ids) && $request->status_id != $replacement_request_status_ids['in drafting']) {
                $result['to_me_requests'][] = $request;
            }
        }

        $result['table_properties'] = config("tables.{$config['instance_plural_name']}");

        return $result;
    }

    public static function updatingStatus($request) {

        $replacement_request_status_ids = config('enum.replacement_request_status_ids');

        $original_is_sent = $request->getOriginal('is_sent');
        $original_is_agreed = $request->getOriginal('is_agreed');
        $original_is_permitted = $request->getOriginal('is_permitted');
        $original_is_cancelled = $request->getOriginal('is_cancelled');
        $original_is_declined = $request->getOriginal('is_declined');
        $original_is_not_permitted = $request->getOriginal('is_not_permitted');
        //$old_status_id = $request->status_id;
        
        if ($request->is_sent != $original_is_sent && $request->is_sent) {
            $request->status_id = $replacement_request_status_ids['in_consent_waiting'];
            $request->is_cancelled = 0;
        }

        if ($request->is_agreed != $original_is_agreed && $request->is_agreed) {
            $request->status_id = $replacement_request_status_ids['in_permission_waiting'];
            $request->is_declined = 0;
        }

        if ($request->is_permitted != $original_is_permitted && $request->is_permitted) {
            $request->status_id = $replacement_request_status_ids['permitted'];
            $request->is_not_permitted = 0;
        }

        if ($request->is_cancelled != $original_is_cancelled && $request->is_cancelled) {
            $request->status_id = $replacement_request_status_ids['cancelled'];
            $request->is_sent = 0;
        }

        if ($request->is_declined != $original_is_declined && $request->is_declined) {
            $request->status_id = $replacement_request_status_ids['declined'];
            $request->is_agreed = 0;
        }

        if ($request->is_not_permitted != $original_is_not_permitted && $request->is_not_permitted) {
            $request->status_id = $replacement_request_status_ids['not_permitted'];
            $request->is_permitted = 0;
        }
 
        // if ($request->status_id != $old_status_id
        //     && $request->status_id != $replacement_request_status_ids['in_consent_waiting']) {
        //     NotificationHelpers::sendReplacementRequestStatusChangedNotifi($request, $old_status_id);
        // }
    }

    public static function updatadStatus($request) {
        
        $replacement_request_status_ids = config('enum.replacement_request_status_ids');
        
        $original_status_id = $request->getOriginal('status_id');
        if ($request->status_id != $original_status_id
            && $request->status_id != $replacement_request_status_ids['in_consent_waiting']) {
            NotificationHelpers::sendReplacementRequestStatusChangedNotifi($request, $original_status_id);
        }
    }

    public static function deleteReplacementRequest ($deleting_id, $config) {
        
        ReplacementRequestMessage::where('replacement_request_id', $deleting_id)->delete();
        
        return ModelHelpers::deleteInstance($deleting_id, $config);
    }
}
