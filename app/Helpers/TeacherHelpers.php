<?php

namespace App\Helpers;

use App\Http\Controllers\TeacherController;
use App\Lesson;
use Illuminate\Support\Facades\Auth;

class TeacherHelpers
{
    public static function getReplacingTeacherSchedule($data) {
        
        $teacher_controller = new TeacherController();
        $config = $teacher_controller->config;
        $weekly_period_ids = config('enum.weekly_period_ids');
        $incom_replacing_data = false;

        $incom_replaceable_data = [
            'schedule_teacher_id' => $data['replacing_teacher_id'], 
            'week_number' => null,
        ];
        
        if (! $data['is_regular'] && isset($data['replaceable_date']) && isset($data['replacing_date'])) {
            $incom_replaceable_data = [
                'schedule_teacher_id' => $data['replacing_teacher_id'], 
                'week_number' => DateHelpers::getWeekNumberFromDate($data['replaceable_date']),
            ];
            if (date('W', strtotime($data['replaceable_date'])) !== date('W', strtotime($data['replacing_date']))) {
                $incom_replacing_data = [
                    'schedule_teacher_id' => $data['replacing_teacher_id'], 
                    'week_number' => DateHelpers::getWeekNumberFromDate($data['replacing_date']),
                ];
                $replacing_schedule_data = ModelHelpers::getSchedule($incom_replacing_data, $config);
            }
        }
                
        $replaceable_schedule_data = ModelHelpers::getSchedule($incom_replaceable_data, $config);
        
        $replaceable_lesson = Lesson::with(['lesson_type', 'lesson_room', 'teacher', 'class_period', 'week_day', 'weekly_period'])
                                    ->where('id', $data['replaceable_lesson_id'])
                                    ->first();
        $replacing_lesson = Lesson::with(['lesson_type', 'lesson_room', 'teacher', 'teacher.users', 'class_period', 'week_day', 'weekly_period'])
                                   ->where('id', $data['replacing_lesson_id'])
                                   ->first();

        if ($data['is_regular']) {
            $replaceable_weekly_period_id = $replaceable_lesson->weekly_period_id;
            $replacing_weekly_period_id = $replacing_lesson->weekly_period_id;
            $replaceable_lesson_description = 'Вам проводить своё занятие: "'.$replacing_lesson->name.'", '.mb_strtolower($replaceable_lesson->weekly_period->name).', '.mb_strtolower($replaceable_lesson->week_day->name).', '.mb_strtolower($replaceable_lesson->class_period->name).' пара.';
            $replacing_lesson_description = 'тогда как я буду проводить своё: "'.$replaceable_lesson->name.'", '.mb_strtolower($replacing_lesson->weekly_period->name).', '.mb_strtolower($replacing_lesson->week_day->name).', '.mb_strtolower($replacing_lesson->class_period->name).' пара.';
        } else {
            $replaceable_date = '';
            if (isset($replaceable_schedule_data['week_dates'])) {
                $replaceable_date = date('d.m.Y', strtotime($replaceable_schedule_data['week_dates'][$replaceable_lesson->week_day_id]));
            }
            if ($incom_replacing_data) {
                $replacing_date = '';
                if (isset($replacing_schedule_data['week_dates'])) {
                    $replacing_date = date('d.m.Y', strtotime($replacing_schedule_data['week_dates'][$replacing_lesson->week_day_id]));
                }
            } else {
                $replacing_date = date('d.m.Y', strtotime($replaceable_schedule_data['week_dates'][$replacing_lesson->week_day_id]));
            }
            $replaceable_weekly_period_id = $weekly_period_ids['every_week'];
            $replacing_weekly_period_id = $weekly_period_ids['every_week'];
            $replaceable_lesson_description = 'провести своё вместо моего: '.$replaceable_date.', "'.$replacing_lesson->name.'", '.mb_strtolower($replaceable_lesson->week_day->name).', '.mb_strtolower($replaceable_lesson->class_period->name).' пара.';
            $replacing_lesson_description = 'тогда как я проведу моё вместо Вашего: '.$replacing_date.', "'.$replaceable_lesson->name.'", '.mb_strtolower($replacing_lesson->week_day->name).', '.mb_strtolower($replacing_lesson->class_period->name).' пара.';
        }

        $mails_to = [];
        foreach ($replacing_lesson->teacher->users as $user) {
            $mails_to[] = $user->email;
        }

        $result = [
            'mails_to' => $mails_to,//[Auth::user()->email], // $mails_to
            'addressee_name' => $replacing_lesson->teacher->first_name_patronymic,
            'requester_name' => $replaceable_lesson->teacher->profession_level_name,
            'replaceable_lesson_description' => $replaceable_lesson_description,
            'replacing_lesson_description' => $replacing_lesson_description,
            'group' => $replaceable_lesson->groups_name,
        ];

        if (! isset($replaceable_schedule_data['lessons'][$replaceable_lesson->class_period_id][$replaceable_lesson->week_day_id][$replaceable_lesson->weekly_period_id])
            && ! isset($replaceable_schedule_data['lessons'][$replaceable_lesson->class_period_id][$replaceable_lesson->week_day_id][$weekly_period_ids['every_week']]))
        {
            $replaceable_schedule_data['lessons'][$replaceable_lesson->class_period_id][$replaceable_lesson->week_day_id][$replaceable_weekly_period_id] = [
                'id' => $replaceable_lesson->id,
                'week_day_id' => $replaceable_lesson->week_day_id,
                'weekly_period_id' => $replaceable_weekly_period_id,
                'class_period_id' => $replaceable_lesson->class_period_id,
                'teacher_id' => $replaceable_lesson->teacher_id,
                'teacher_name' => $replaceable_lesson->teacher->profession_level_name,
                'type' => $replaceable_lesson->lesson_type->short_notation,
                'name' => $replaceable_lesson->name,
                'room' => $replaceable_lesson->lesson_room->number,
                'date' => $replaceable_lesson->date,
                'is_replaceable' => true,
            ];

            if (! $incom_replacing_data) {
                $replaceable_schedule_data['lessons'][$replacing_lesson->class_period_id][$replacing_lesson->week_day_id][$replacing_weekly_period_id]['is_replacing'] = true;
                $result['schedule_data'] = [
                    'replaceable' => $replaceable_schedule_data,
                ];
            } else {
                $replacing_schedule_data['lessons'][$replacing_lesson->class_period_id][$replacing_lesson->week_day_id][$replacing_weekly_period_id]['is_replacing'] = true;
                $result['schedule_data'] = [
                    'replaceable' => $replaceable_schedule_data,
                    'replacing' => $replacing_schedule_data,
                ];
            }

            return $result;
        }

        return false;
    }


}
