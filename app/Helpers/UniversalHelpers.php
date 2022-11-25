<?php

namespace App\Helpers;

use App\ClassPeriod;
use App\Lesson;
use App\Teacher;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class UniversalHelpers
{
    public static function getWeekData($week_str) {
        $pos = strpos($week_str, '-W');
        if ($pos !== false) {
            $week_data = explode('-W', $week_str);
            if (count($week_data) == 2) {
                return [
                    'year' => $week_data[0],
                    'week' => $week_data[1],
                ];
            }
            return false;
        }
        return false;
    }
    
    public static function getWeekStartEndDates($year, $week)
    {
        return [
            'start_date' => (new DateTime())->setISODate($year, $week)->format('d.m.y'),
            'end_date' => (new DateTime())->setISODate($year, $week, 7)->format('d.m.y') 
        ];
    }

    public static function weekDates($week_str) {
        $week_data = self::getWeekData($week_str);
        if ($week_data) {
            return self::getWeekStartEndDates($week_data['year'], $week_data['week']);
        }
        return false; 
    }

    public static function testDateLesson($week_request, $lesson) {
        if (!isset($week_request) && isset($lesson->date)) {
            return false;
        }
        if (isset($week_request) && isset($lesson->date)) {
            if (date('Y-W', strtotime($lesson->date)) != date('Y-W', strtotime($week_request))) {
                return false;
            }
        }
        return true;
    }

    public static function getWeekNumberFromDate($date) {
        $year = date('Y', strtotime($date));
        $week = date('W', strtotime($date));

        return "{$year}-W{$week}";
    }
}
