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

    public static function getWeekDates($year, $week)
    {
        return [
            1 => (new DateTime())->setISODate($year, $week, 1)->format('Y-m-d H:i:s'),
            2 => (new DateTime())->setISODate($year, $week, 2)->format('Y-m-d H:i:s'),
            3 => (new DateTime())->setISODate($year, $week, 3)->format('Y-m-d H:i:s'),
            4 => (new DateTime())->setISODate($year, $week, 4)->format('Y-m-d H:i:s'),
            5 => (new DateTime())->setISODate($year, $week, 5)->format('Y-m-d H:i:s'),
            6 => (new DateTime())->setISODate($year, $week, 6)->format('Y-m-d H:i:s'),
            // 'Воскресенье' => (new DateTime())->setISODate($year, $week, 7)->format('d.m.y') 
        ];
    }

    public static function weekStartEndDates($week_str) {
        $week_data = self::getWeekData($week_str);
        if ($week_data) {
            return self::getWeekStartEndDates($week_data['year'], $week_data['week']);
        }
        return false; 
    }

    public static function weekDates($week_str) {
        $week_data = self::getWeekData($week_str);
        if ($week_data) {
            return self::getWeekDates($week_data['year'], $week_data['week']);
        }
        return false; 
    }

    public static function testLessonDate($week_number, $lesson) {
        if (!isset($week_number) && isset($lesson->date)) {
            return false;
        }
        if (isset($week_number) && isset($lesson->date)) {
            if (date('Y-W', strtotime($lesson->date)) != date('Y-W', strtotime($week_number))) {
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

    public static function preparingBooleans($data, $boolean_attributes) {
        foreach ($boolean_attributes as $attribute) {
            if (! isset($data[$attribute])) {
                $data[$attribute] = false;
            }
        }
        return $data;
    }

    public static function weekColorIsRed($week_number_str) {
        
        $red_week_is_odd = config('site.red_week_is_odd');
        $week_data = self::getWeekData($week_number_str);

        $this_week_is_odd = (int)$week_data['week'] % 2;

        if ($red_week_is_odd == $this_week_is_odd) {
            return true;
        }
        return false;
    }

    public static function getMonthWeeklyScheduleLesson ($week_number, $lesson) {
        
        if (! isset($week_number)) {
            return;
        }
        
        $weekly_period_ids = config('enum.weekly_period_ids');
        $week_is_red = self::weekColorIsRed($week_number);

        if (($week_is_red && $lesson['weekly_period_id'] != $weekly_period_ids['blue_week'])
            || 
            (! $week_is_red && $lesson['weekly_period_id'] != $weekly_period_ids['red_week'])) 
        {
            $lesson['weekly_period_id'] = $weekly_period_ids['every_week'];
            return $lesson;
        }
        
        return false;
    }

    public static function getWeeklyScheduleLesson ($week_number, $lesson) {
        
        if (! isset($week_number)) {
            return;
        }
        
        $weekly_period_ids = config('enum.weekly_period_ids');
        $week_is_red = self::weekColorIsRed($week_number);

        if (($week_is_red && $lesson->weekly_period_id != $weekly_period_ids['blue_week'])
            || 
            (! $week_is_red && $lesson->weekly_period_id != $weekly_period_ids['red_week'])) 
        {
            $lesson->weekly_period_id = $weekly_period_ids['every_week'];
            return $lesson;
        }
        
        return false;
    }

    public static function getMonthWeekNumbers($month_number) {
                
        $first_month_day = date('Y-m-01', strtotime($month_number));
        if (date('w', strtotime($first_month_day)) == 0) {
            $first_month_day = date('Y-m-d', strtotime("{$first_month_day} + 1 day"));
        } 

        $last_month_day = date('Y-m-t', strtotime($month_number));
        if (date('w', strtotime($last_month_day)) == 0) {
            $last_month_day = date('Y-m-d', strtotime("{$first_month_day} - 1 day"));
        }

        $month_week_numbers = [];
        for ($i = $first_month_day; $i <= $last_month_day; $i = date('Y-m-d', strtotime("{$i} + 1 day"))) {
            $week_number = UniversalHelpers::getWeekNumberFromDate($i);
            if (! in_array($week_number, $month_week_numbers)) {
                $month_week_numbers[] = UniversalHelpers::getWeekNumberFromDate($i);
            }
        }

        return $month_week_numbers;
    }


}
