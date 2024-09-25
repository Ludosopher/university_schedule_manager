<?php

namespace App\DocExporters\OneTable\WeekSchedule;

use App\ClassPeriod;
use App\Helpers\DateHelpers;
use App\Setting;

class DocExporterOrdinaryWeekSchedule extends DocExporterWeekSchedule
{
    protected $data = [];
    protected $first_column_width = 1300;
        
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function createWriter() 
    {
        $study_periods_data = DateHelpers::getStudyPeriodsData();
        $required_study_period_id = (int)($this->data['study_period_id'] ?? $study_periods_data['current_period_id']);
        $required_study_period = DateHelpers::getRequiredStudyPeriod($study_periods_data['all_periods'], $required_study_period_id);
        $study_seasons = config('enum.study_seasons');
        $settings = Setting::pluck('value', 'name');
        $week_days_limit = $settings['full_time_week_days_limit'] ?? config('site.week_days_limits')['full_time'];

        if (isset($this->data['week_data']) && isset($this->data['is_red_week'])) {
            $week_data = json_decode($this->data['week_data'], true);
            $week_color = $this->data['is_red_week'] ? __('header.red_week_color') : __('header.blue_week_color');
            $week_days_limit = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
            if ($week_data['current_study_season'] === $study_seasons['studies']) {
                $week_period_string = str_replace(['?-1', '?-2', '?-3' ], [$week_data['start_date'], $week_data['end_date'], $week_color], __('header.week_period_string')); 
                $schedule_header = __('header.schedule_export_to_docx').$week_period_string;
            } elseif ($week_data['current_study_season'] === $study_seasons['session']) {
                $schedule_header = __('header.session').' '.__('header.'.$required_study_period->season).' '.$required_study_period->year.' '.__('header.of_year'); 
            } else {
                $schedule_header = __('header.vacation');
            }
        } else {
            $schedule_header = __('header.regular_schedule_export_to_docx').' '.__('header.'.$required_study_period->season).' '.$required_study_period->year.' '. __('header.of_year');
        }

        if (isset($this->data['teacher_name'])) {
            $participant_header = __('header.teacher');
            $participant = $this->data['teacher_name'];
        } elseif (isset($this->data['group_name'])) {
            $participant_header = __('header.group');
            $participant = $this->data['group_name'];
        } else {
            $participant_header = '';
            $participant = '';
        }

        $header_rows = [
            [ 'text' => $schedule_header, 'font_style' => ['bold' => true], 'paragraph_style' => ['align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0]],
            [ 'text' => "{$participant_header}: {$participant}"],
        ];
        
        return $this->getWeekWriter([
            'header_rows' => $header_rows,
            'table_header_cells' => $this->getTableHeaderCells($this->data, $this->first_column_width),
            'week_days_limit' => $week_days_limit
        ]);
    }

    function getCellAdditional($data, $lesson, $week_day_id, &$lesson_other_participant, &$fontStyle, &$lesson_n, &$lesson_type, &$lesson_room)
    {
        return null;
    }

}