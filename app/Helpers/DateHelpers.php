<?php

namespace App\Helpers;

use App\ExternalDataset;
use App\Lesson;
use App\Setting;
use App\StudyPeriod;
use DateTime;


class DateHelpers
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
        $holidays = self::getHolidays();
        $dates =  [
            1 => (new DateTime())->setISODate($year, $week, 1)->format('Y-m-d H:i:s'),
            2 => (new DateTime())->setISODate($year, $week, 2)->format('Y-m-d H:i:s'),
            3 => (new DateTime())->setISODate($year, $week, 3)->format('Y-m-d H:i:s'),
            4 => (new DateTime())->setISODate($year, $week, 4)->format('Y-m-d H:i:s'),
            5 => (new DateTime())->setISODate($year, $week, 5)->format('Y-m-d H:i:s'),
            6 => (new DateTime())->setISODate($year, $week, 6)->format('Y-m-d H:i:s'),
            // 7 => (new DateTime())->setISODate($year, $week, 7)->format('d.m.y') 
        ];

        if ($holidays) {
            foreach ($dates as &$date) {
                foreach ($holidays as $holiday) {
                    if (date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime($holiday))) {
                        $holiday_date = [
                            'is_holiday' => true,
                            'date' => $date
                        ];
                        $date = $holiday_date;
                        break;
                    }
                }
            }
        }
        
        return $dates;
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

    public static function checkOneTimeLessonToWeek($week_number, $date) {
        if (!isset($week_number) && isset($date)) {
            return false;
        }
        if (isset($week_number) && isset($date)) {
            if (date('Y-W', strtotime($date)) != date('Y-W', strtotime($week_number))) {
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

    public static function weekColorIsRed($week_number_str) {
        
        $red_week_is_odd_setting = Setting::where('name', 'red_week_is_odd')->first();
        $red_week_is_odd = $red_week_is_odd_setting ? $red_week_is_odd_setting->value : config('site.red_week_is_odd');
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
        $weekly_period_id = null;
        if (is_object($lesson)) {
            $weekly_period_id = $lesson->weekly_period_id;
        } elseif (is_array($lesson)) {
            $weekly_period_id = $lesson['weekly_period_id'];
        }

        if (($week_is_red && isset($weekly_period_id) && $weekly_period_id != $weekly_period_ids['blue_week'])
            || 
            (! $week_is_red && isset($weekly_period_id) && $weekly_period_id != $weekly_period_ids['red_week'])) 
        {
            if (is_object($lesson)) {
                $lesson->real_weekly_period_id = $weekly_period_id;
                $lesson->weekly_period_id = $weekly_period_ids['every_week'];
            } elseif (is_array($lesson)) {
                $lesson['real_weekly_period_id'] = $weekly_period_id;
                $lesson['weekly_period_id'] = $weekly_period_ids['every_week'];
            }

            return $lesson;
        }
        
        return false;
    }

    public static function getMonthWeekNumbers($month_number) 
    {
        $first_month_day = date('Y-m-01', strtotime($month_number));
        if (date('w', strtotime($first_month_day)) == 0) {
            $first_month_day = date('Y-m-d', strtotime("{$first_month_day} + 1 day"));
        } 

        $last_month_day = date('Y-m-t', strtotime($month_number));
        if (date('w', strtotime($last_month_day)) == 0) {
            $last_month_day = date('Y-m-d', strtotime("{$last_month_day} - 1 day"));
        }

        $month_week_numbers = [];
        for ($i = $first_month_day; $i <= $last_month_day; $i = date('Y-m-d', strtotime("{$i} + 1 day"))) {
            $week_number = DateHelpers::getWeekNumberFromDate($i);
            if (! in_array($week_number, $month_week_numbers)) {
                $month_week_numbers[] = DateHelpers::getWeekNumberFromDate($i);
            }
        }

        return $month_week_numbers;
    }

    public static function getHolidays() {

        $external_dataset_ids = config('enum.external_dataset_ids');
        
        $xmlcalendar_data = ExternalDataset::where('id', $external_dataset_ids['xmlcalendar'])->first();
        if ($xmlcalendar_data) {
            if (date('Y') != date('Y', strtotime($xmlcalendar_data->update_date))) {
                $url = str_replace('{Y}', date('Y'), $xmlcalendar_data->url_pattern);
                $calendar = simplexml_load_file($url);
                if ($calendar) {
                    $calendar = $calendar->days->day;
                    // All holidays for the current year
                    foreach( $calendar as $day ){
                        $d = (array)$day->attributes()->d;
                        $d = $d[0];
                        $d = substr($d, 3, 2).'.'.substr($d, 0, 2).'.'.date('Y');
                        // not counting the short days
                        if( $day->attributes()->t == 1 ) $arHolidays[] = $d;
                    }
                    $xmlcalendar_data->body = json_encode($arHolidays);
                    $xmlcalendar_data->update_date = date('Y-m-d');
                    $xmlcalendar_data->save();
                    
                    return $arHolidays;
                }
            
                Log::channel('production_calendar')->error('The production calendar was not received.');
                return false;
            }

            return json_decode($xmlcalendar_data->body, true);
        }
    }

    public static function getStudyPeriodsData() {
        $current_study_period_id = null;
        $current_date = date('Y-m-d');
        $study_periods = StudyPeriod::orderBy('start')
                                    ->get();
        foreach ($study_periods as $key => &$study_period) {
            if (! isset($study_periods[$key-1]) && $current_date < $study_period->start) {
                $study_period->is_current = true;
                $current_study_period_id = $study_period->id;
                break;
            }
            if ($current_date >= $study_period->start && $current_date <= $study_period->end) {
                $study_period->is_current = true;
                $current_study_period_id = $study_period->id;
                break;
            }
            if (isset($study_periods[$key+1]) && $current_date > $study_period->end && $current_date < $study_periods[$key+1]->start) {
                $study_periods[$key+1]->is_current = true;
                $current_study_period_id = $study_periods[$key+1]->id;
                break;
            }
            if (! isset($study_periods[$key+1]) && $current_date > $study_period->end) {
                $study_period->is_current = true;
                $current_study_period_id = $study_period->id;
                break;
            }
        }

        return [
            'all_periods' => $study_periods,
            'current_period_id' => $current_study_period_id
        ];
    }

    public static function getRequiredStudyPeriod($study_periods, $required_study_period_id) 
    {
        foreach ($study_periods as $study_period) {
            if ($study_period->id === $required_study_period_id) {
                return $study_period;
            }
        }
        return false;
    }

    public static function checkWeekToStudyPeriodSeason(StudyPeriod $study_period, $week_number)
    {
        $study_seasons = config('enum.study_seasons');
        $study_season_name_parts = config('enum.study_season_name_parts');

        foreach ($study_season_name_parts as $study_season => $study_season_name_part) {
            $start = $study_season_name_part.'start';
            $end = $study_season_name_part.'end';
            if (date('Y-W', strtotime($week_number)) >= date('Y-W', strtotime($study_period->$start))
                &&
                date('Y-W', strtotime($week_number)) <= date('Y-W', strtotime($study_period->$end))) 
            {
                return $study_seasons[$study_season];
            }
        }
        
        return $study_seasons['vacation'];
    }

    public static function checkWeekToStudyPeriod(StudyPeriod $study_period, $week_number)
    {
        if (date('Y-W', strtotime($week_number)) >= date('Y-W', strtotime($study_period->start))
            &&
            date('Y-W', strtotime($week_number)) <= date('Y-W', strtotime($study_period->end))) 
        {
            return true;
        }
        return false;
    }

    public static function checkRegularLessonToWeek($study_period_id, $week_number)
    {
        if (isset($study_period_id)) {
            $lesson_study_period = StudyPeriod::find($study_period_id);
            return self::checkWeekToStudyPeriod($lesson_study_period, $week_number);
        }
        return false;
    }

    public static function getCurrentStudyPeriodBorderWeeks()
    {
        $study_periods_data = self::getStudyPeriodsData();
        $current_study_period = StudyPeriod::find($study_periods_data['current_period_id']);
        return [
            'start' => self::getWeekNumberFromDate($current_study_period->start),
            'end' => self::getWeekNumberFromDate($current_study_period->end)
        ];
    }

}
