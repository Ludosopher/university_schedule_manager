<?php

namespace App\DocExporters\OneTable;

use App\ClassPeriod;
use App\Setting;

class DocExporterTable extends DocExporter
{
    protected $data = [];
    protected $cell_width = 1400;
        
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function createWriter() {
        
        $table_properties = config('tables.replacement_variants');
        $header_data = json_decode($this->data['header_data'], true);
        $replacement_lessons = json_decode($this->data['replacement_lessons'], true);

        $week_period_string = '';
        if (isset($this->data['week_data']) && isset($this->data['is_red_week'])) {
            $week_data = json_decode($this->data['week_data'], true);
            $week_color = $this->data['is_red_week'] ? __('header.red_week_color') : __('header.blue_week_color');
            $week_period_string = str_replace(['?-1', '?-2', '?-3' ], [$week_data['start_date'], $week_data['end_date'], $week_color], __('header.week_period_string')); 
        }

        $header_rows = [
            [ 'text' => __('header.replacement_variants_export_to_docx').$week_period_string, 'font_style' => ['bold' => true], 'paragraph_style' => ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]],
            [ 'text' => str_replace(['?-1', '?-2', '?-3' ], [__('dictionary.'.$header_data['class_period']), __('dictionary.'.$header_data['week_day']), $this->data['date_or_weekly_period']], __('header.replaceable_lesson_export_to_docx')), 'font_style' => null, 'paragraph_style' => ['spaceBefore' => 0, 'spaceAfter' => 0]],
            [ 'text' => __('header.of_teacher_export_to_docx').$header_data['teacher'], 'font_style' => null, 'paragraph_style' => ['spaceBefore' => 0, 'spaceAfter' => 0]],
            [ 'text' => __('header.of_group_export_to_docx').$header_data['group'], 'font_style' => null, 'paragraph_style' => ['spaceBefore' => 0, 'spaceAfter' => 100]]
        ];
        
        $ordinaryCellStyle = array('valign' => 'center');
        $ordinaryFontStyle = array('size' => 8);
        $ordinaryParagraphStyle = array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0);

        $table_header_cells = [];
        foreach ($table_properties as $property) {
            if (isset($this->data['is_red_week']) && __('table_header.'.$property['header']) == __('table_header.weekly_period')) {
                continue;
            }
            $table_header_cells[] = [
                'width' => $this->cell_width, 'text' => __('table_header.'.$property['header']),
            ];
        }

        $cell_data = [];
        $row_data = [];
        $table_content = [];
        foreach($replacement_lessons as $lesson) {
            foreach($table_properties as $property) {
                $field = $property['field'];
                if (isset($this->data['is_red_week']) && $field == 'weekly_period_id') {
                    continue;
                } elseif ($field == 'week_day_id' && isset($lesson['date'])) {
                    $content = __('dictionary.'.$lesson['name']).' ('.__('dictionary.'.$lesson['type']).')';
                } elseif (is_array($lesson[$field])) {
                    $content = \Lang::has('dictionary.'.$lesson[$field]['name']) ? __('dictionary.'.$lesson[$field]['name']) : $lesson[$field]['name'];
                } else {
                    $content = \Lang::has('dictionary.'.$lesson[$field]) ? __('dictionary.'.$lesson[$field]) : $lesson[$field]; 
                }
                $cell_data['add_cell'] = ['value' => $this->cell_width, 'cell_style' => $ordinaryCellStyle];
                $cell_data['add_text'] = [
                    ['text' => $content, 'font_style' => $ordinaryFontStyle, 'paragraph_style' => $ordinaryParagraphStyle],
                ];

                if (count($cell_data)) {$row_data[] = $cell_data;}
                $cell_data = [];
            }
            $table_content[] = [
                'height' => 700,
                'data' => $row_data
            ];
            $row_data = [];
        }

        return $this->getWriter([
            'header_rows' => $header_rows,
            'table_header_cells' => $table_header_cells,
            'table_content' => $table_content
        ]);
    }

}