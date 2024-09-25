<?php

namespace App\DocExporters\OneTable\WeekSchedule;

use App\Helpers\DateHelpers;
use App\Setting;
use Illuminate\Support\Facades\Log as FacadesLog;
use Log;

class DocExporterWeekReschedule extends DocExporterWeekSchedule
{
    protected $data = [];
    protected $first_column_width = 1300;
        
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function createWriter() 
    {
        $settings = Setting::pluck('value', 'name');
        $week_days_limit = $settings['full_time_week_days_limit'] ?? config('site.week_days_limits')['full_time'];
        $study_seasons = config('enum.study_seasons');
                                        
        $week_period_string = '';
        if (isset($this->data['week_data']) && isset($this->data['is_red_week'])) {
            $week_days_limit = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
            $week_data = json_decode($this->data['week_data'], true);
            $week_color = $this->data['is_red_week'] ? __('header.red_week_color') : __('header.blue_week_color');
            if ($week_data['current_study_season'] === $study_seasons['studies']) {
                $week_period_string = str_replace(['?-1', '?-2', '?-3' ], [$week_data['start_date'], $week_data['end_date'], $week_color], __('header.week_period_string')); 
            } 
        }
        if ($this->data['is_reschedule_for'] == 'teacher') {
            $participant_header = __('header.teacher');
            $of_participant_header = __('header.of_teacher');
            $participant = $this->data['teacher_name'];
        } elseif ($this->data['is_reschedule_for'] == 'group') {
            $participant_header = __('header.group');
            $of_participant_header = __('header.of_group');
            $participant = $this->data['group_name'];
        } else {
            $participant_header = '';
            $of_participant_header = ''; 
        }
        
        $header_rows = [
            [ 'text' => __('header.reschedule_variants_export_to_docx').$of_participant_header.$week_period_string, 'font_style' => ['bold' => true], 'paragraph_style' => ['align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0]],
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
        if (!is_array($lesson) && $lesson) {
            $fontStyle = array_merge($fontStyle, ['name' => 'Segoe Script', 'color' => '#000000']);
            $lesson_n = '';
            $lesson_type = '';
            $lesson_room = '';
            $lesson_other_participant = __('title.reschedule_variant');
        } elseif (isset($lesson['id']) && isset($data['rescheduling_lesson_id']) && $lesson['id'] == $data['rescheduling_lesson_id']) {
            $fontStyle = array_merge($fontStyle, ['bold' => true]);
            return ['text' => '('.__('title.rescheduling_lesson').')', 'font_style' => ['size' => 8, 'name' => 'Segoe Script', 'bold' => true], 'paragraph_style' => ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]];
        }

        return '';
    }

}