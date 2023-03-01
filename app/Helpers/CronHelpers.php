<?php

namespace App\Helpers;

use App\Lesson;
use App\Mail\MailReplacementRequest;
use App\ReplacementRequest;
use Log;
use Illuminate\Support\Facades\Mail;

class CronHelpers
{
    public static function replacementRequestStatusesUpdate() {

        Log::channel('cron')->info('CronHelpers::replacementRequestStatusesUpdate function is started.');
        $replacement_request_status_ids = config('enum.replacement_request_status_ids');
        
        $replace_reqs = ReplacementRequest::where('status_id', $replacement_request_status_ids['permitted'])
                            ->whereNotNull('replaceable_date')
                            ->whereNotNull('replacing_date')
                            ->where(function($req) {
                                $req->orWhere(function($r) {
                                        $r->where('replaceable_date', date('Y-m-d'))
                                        ->where('replacing_date', '>=', date('Y-m-d'));
                                    })
                                    ->orWhere(function($r) {
                                        $r->where('replacing_date', date('Y-m-d'))
                                        ->where('replaceable_date', '>=', date('Y-m-d'));
                                    });  
                                })
                            ->get();
        
        $in_realization_req_ids = [];
        foreach ($replace_reqs as $replace_req) {
            $replace_req->status_id = $replacement_request_status_ids['in_realization'];
            $in_realization_req_ids[] = $replace_req->id;
            $replace_req->save();
        }                   
        Log::channel('cron')->info('The status "In realization" is assigned to the following replacement requests: '.implode(', ', $in_realization_req_ids).'.');
        
        $replace_reqs = ReplacementRequest::whereIn('status_id', [$replacement_request_status_ids['permitted'], $replacement_request_status_ids['in_realization']])
                            ->whereNotNull('replaceable_date')
                            ->whereNotNull('replacing_date')                    
                            ->where('replaceable_date', '<', date('Y-m-d'))
                            ->where('replacing_date', '<', date('Y-m-d'))
                            ->get();
        
                            $completed_req_ids = [];
        foreach ($replace_reqs as $replace_req) {
            $replace_req->status_id = $replacement_request_status_ids['completed'];
            $completed_req_ids[] = $replace_req->id;
            $replace_req->save();
        }
        Log::chanel('cron')->info('The status "Completed" is assigned to the following replacement requests: '.implode(', ', $completed_req_ids).'.');                   
    }


}
