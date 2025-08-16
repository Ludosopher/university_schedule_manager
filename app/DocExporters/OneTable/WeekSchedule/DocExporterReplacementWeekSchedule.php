<?php

namespace App\DocExporters\OneTable\WeekSchedule;

use App\Helpers\DateHelpers;
use App\Setting;
use Log;

class DocExporterReplacementWeekSchedule extends DocExporterWeekSchedule
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
        $week_days_limit = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
                                
        $header_data = json_decode($this->data['header_data'], true);
        $header_rows = [
            [ 'text' => __('header.replacement_variants_export_to_docx'), 'font_style' => ['bold' => true], 'paragraph_style' => ['align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0]],
            [ 'text' => str_replace(['?-1', '?-2', '?-3' ], [__('dictionary.'.$header_data['class_period']), __('dictionary.'.$header_data['week_day']), $this->data['date_or_weekly_period']], __('header.replaceable_lesson_export_to_docx')), 'font_style' => null, 'paragraph_style' => ['spaceBefore' => 0, 'spaceAfter' => 0]],
            [ 'text' => __('header.of_teacher_export_to_docx').$header_data['teacher'], 'font_style' => null, 'paragraph_style' => ['spaceBefore' => 0, 'spaceAfter' => 0]],
            [ 'text' => __('header.of_group_export_to_docx').$header_data['group'], 'font_style' => null, 'paragraph_style' => ['spaceBefore' => 0, 'spaceAfter' => 100]],
        ];

        return $this->getWeekWriter([
            'header_rows' => $header_rows,
            'table_header_cells' => $this->getTableHeaderCells($this->data, $this->first_column_width),
            'week_days_limit' => $week_days_limit
        ]);
    }

    function getCellAdditional($data, $lesson, $week_day_id, &$lesson_other_participant, &$fontStyle, &$lesson_n, &$lesson_type, &$lesson_room)
    {
        $week_dates = isset($data['week_dates']) ? json_decode($data['week_dates'], true) : null;
        if (isset($lesson['for_replacement']) && $lesson['for_replacement']) {
            $fontStyle = array_merge($fontStyle, ['shading' => array('fill' => '#DCDCDC')]);
            $lesson_other_participant = $lesson['teacher'];
        } elseif ((! isset($week_dates) && isset($lesson['id']) && isset($data['replaceable_lesson_id']) && $lesson['id'] == $data['replaceable_lesson_id'])
                    ||
                    (isset($week_dates)
                    //&& isset($data['date_or_weekly_period'])
                    //&& $data['date_or_weekly_period'] === date('d.m.y', strtotime($week_dates[$week_day_id]))
                    && isset($lesson['id']) 
                    && isset($data['replaceable_lesson_id']) 
                    && $lesson['id'] == $data['replaceable_lesson_id']))
        {
            $fontStyle = array_merge($fontStyle, ['bold' => true]);
            return ['text' => '('.__('title.replaceable_lesson').')', 'font_style' => ['size' => 6, 'shading' => array('fill' => '#DCDCDC')], 'paragraph_style' => ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]];
        }
        
        return '';
    }

}