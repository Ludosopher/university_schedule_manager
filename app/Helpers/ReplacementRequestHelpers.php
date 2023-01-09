<?php

namespace App\Helpers;

use App\ReplacementRequest;
use Illuminate\Database\Eloquent\Builder;

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
            'my' => [],
            'to_me' => []
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

}
