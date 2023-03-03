<?php

namespace App\Helpers;

use App\Lesson;
use App\Mail\MailReplacementRequest;
use App\ReplacementRequest;
use Log;
use Illuminate\Support\Facades\Mail;

class ResponseHelpers
{
    public static function getContent($data, $instance_name) {
        
        $message = '';
        if (isset($data['updated_instance_name'])) {
            $message = str_replace('?', $data['updated_instance_name'], __($instance_name.'.'.$instance_name.'_updated'));
            $success = true;
        } elseif (isset($data['new_instance_name'])) {
            $message = str_replace('?', $data['new_instance_name'], __($instance_name.'.'.$instance_name.'_added'));
            $success = true;
        } elseif (isset($data['deleted_instance_name'])) {
            $message = str_replace('?', $data['deleted_instance_name'], __($instance_name.'.'.$instance_name.'_removed'));
            $success = true;
        } elseif (isset($data['there_are_lessons_only_with_this_group'])) {
            $message = __('group.group_is_only_one_in_lesson');
            $success = false;
        } elseif (isset($data['duplicated_lesson']) && isset($data['duplicated_lesson']['teacher'])) {
            $message = str_replace(['?-1', '?-2', '?-3', '?-4'], [$data['duplicated_lesson']['week_day'], mb_strtolower($data['duplicated_lesson']['class_period']), mb_strtolower($data['duplicated_lesson']['weekly_period']), $data['duplicated_lesson']['teacher']], __('lesson.is_group_lesson_dublicate'));
            $success = false;
        } elseif (isset($data['duplicated_lesson']) && isset($data['duplicated_lesson']['group'])) {
            $message = str_replace(['?-1', '?-2', '?-3', '?-4'], [$data['duplicated_lesson']['week_day'], mb_strtolower($data['duplicated_lesson']['class_period']), mb_strtolower($data['duplicated_lesson']['weekly_period']), $data['duplicated_lesson']['group']], __('lesson.is_group_lesson_dublicate'));
            $success = false;
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    
    }

    public static function getLessonReplacementBackData($data) {
        
        if (isset($data['prev_replace_rules'])) {
            $replace_rules = json_decode($data['prev_replace_rules'], true);
        } else {
            $replace_rules = $data['replace_rules'];
        }
dd($data);        
        return [
            'replace_rules' => $replace_rules,
            'week_data' => $data['week_data'],
            'week_dates' => $data['week_dates'],
            'is_red_week' => $data['is_red_week'],
        ];
    }


}
