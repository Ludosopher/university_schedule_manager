<?php

namespace App\Helpers;

use App\Notifications\ReplacementRequestStatusChangedNotifi;
use Illuminate\Support\Facades\Notification;
use Log;

class NotificationHelpers
{
    public static function sendReplacementRequestStatusChangedNotifi($request, $old_status_id) {
        
        $replacement_request_statuses = config('enum.replacement_request_statuses');
        
        $replaceable_addressees = $request->replaceable_lesson->teacher->users;
        $replacing_addressees = $request->replacing_lesson->teacher->users;
        $notifi_data = [
            'old_status' => $replacement_request_statuses[$old_status_id],
            'new_status' => $replacement_request_statuses[$request->status_id],
        ];
        
        $notifi_data['teacher_name'] = $request->replaceable_lesson->teacher->first_name_patronymic;
        Notification::send($replaceable_addressees, new ReplacementRequestStatusChangedNotifi($request, $notifi_data));
        foreach ($replaceable_addressees as $addressee) {
            Log::channel('notification_mail')->info('Email notification about replacement request status change was sent to '.$addressee->name.' on '.$addressee->email.'.');
        }
        
        $notifi_data['teacher_name'] = $request->replacing_lesson->teacher->first_name_patronymic;
        Notification::send($replacing_addressees, new ReplacementRequestStatusChangedNotifi($request, $notifi_data));
        foreach ($replacing_addressees as $addressee) {
            Log::channel('notification_mail')->info('Email notification about replacement request status change was sent to '.$addressee->name.' on '.$addressee->email.'.');
        }
    }

    

}
