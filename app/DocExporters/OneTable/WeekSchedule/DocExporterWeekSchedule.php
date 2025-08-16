<?php

namespace App\DocExporters\OneTable\WeekSchedule;

use App\ClassPeriod;
use App\DocExporters\OneTable\DocExporter;
use App\Setting;

abstract class DocExporterWeekSchedule extends DocExporter
{
    protected $data = [];
    protected $first_column_width = 1300;
    protected $cell_width = 2000;
        
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    abstract function getCellAdditional($data, $lesson, $week_day_id, &$lesson_other_participant, &$fontStyle, &$lesson_n, &$lesson_type, &$lesson_room);

    protected function getWeekWriter($data)
    {
        $week_day_ids = config('enum.week_day_ids');
        $class_period_ids = config('enum.class_period_ids');
        $weekly_period_id = config('enum.weekly_period_ids');
        $settings = Setting::pluck('value', 'name');
        $class_periods_limit = $settings['full_time_class_periods_limit'] ?? config('site.class_periods_limits')['full_time'];
        $class_periods = ClassPeriod::get();
        $other_partic = $this->data['other_participant'];

        $headerFontStyle = array('size' => 8);
        $headerParagraphStyle = array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0);
        $headerCellStyle = array('valign' => 'center');

        $everyWeekCellStyle = array('valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER);
        $everyWeekParagraphStyle = array('spaceBefore' => 0, 'spaceAfter' => 0, 'align' => 'center');
        
        $halfCellStyle = array('valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER);
        $halfParagraphStyle = array('spaceBefore' => 0, 'spaceAfter' => 0, 'align' => 'center');

        $borderFontStyle = array('color' => '999999', 'size' => 6);
        $borderParagraphStyle = array('spaceBefore' => 0, 'spaceAfter' => 0, 'align' => 'center');

        $table_content = [];
        $lessons = json_decode($this->data['lessons'], true);
        $week_dates = isset($data['week_dates']) ? json_decode($data['week_dates'], true) : null;
        foreach($class_period_ids as $lesson_name => $class_period_id) {
            $row_data[] = [
                'add_cell' => ['value' => $this->first_column_width, 'cell_style' => $headerCellStyle],
                'add_text' => [
                    ['text' => $class_period_id, 'font_style' => $headerFontStyle, 'paragraph_style' => $headerParagraphStyle],
                    ['text' => date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['start'])).' - '.date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['end'])), 'font_style' => $headerFontStyle, 'paragraph_style' => $headerParagraphStyle],
                ]
            ];
            if ($class_period_id <= $class_periods_limit) {
                foreach($week_day_ids as $wd_name => $week_day_id) {
                    $is_holiday = isset($week_dates) && is_array($week_dates[$week_day_id]) && isset($week_dates[$week_day_id]['is_holiday']); 
                    if ($week_day_id <= $data['week_days_limit']) {
                        if (isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']]) && ! $is_holiday) {
                            $lesson = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']];
                            $lesson_n = __('content.'.$lesson['name']); 
                            $lesson_type = '('.__('dictionary.'.$lesson['type']).')';
                            $lesson_room = __('content.room').' '.$lesson['room'];
                            $lesson_other_participant = $lesson[$other_partic];
                            $everyWeekFontStyle = array('size' => 8);
                            $currentEveryWeekCellStyle = $everyWeekCellStyle;
                            if (isset($lesson['date'])) {
                                $currentEveryWeekCellStyle = array_merge($everyWeekCellStyle, ['borderStyle' => 'double', 'borderSize' => 8]);
                            }
                            $additional_row = $this->getCellAdditional($this->data, $lesson, $week_day_id, $lesson_other_participant, $everyWeekFontStyle, $lesson_n, $lesson_type, $lesson_room);
                            $cell_data['add_cell'] = ['value' => $this->cell_width, 'cell_style' => $currentEveryWeekCellStyle];
                            $cell_data['add_text'] = [
                                ['text' => "{$lesson_n} {$lesson_type}", 'font_style' => $everyWeekFontStyle, 'paragraph_style' => $everyWeekParagraphStyle],
                                ['text' => "{$lesson_room}", 'font_style' => $everyWeekFontStyle, 'paragraph_style' => $everyWeekParagraphStyle],
                                ['text' => $lesson_other_participant, 'font_style' => $everyWeekFontStyle, 'paragraph_style' => $everyWeekParagraphStyle]
                            ];
                            if (! empty($additional_row)) {
                                $cell_data['add_text'][] = $additional_row;
                            }
                        } elseif (isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']]) || isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']])) {
                            $lesson_red = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']] ?? false;
                            $lesson_blue = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']] ?? false;
                            if (!$lesson_red) {
                                $redFontStyle = array('size' => 6, 'color' => '#ffffff');
                                $blueFontStyle = array('size' => 6);
                                $red_name = __('content.'.$lesson_blue['name']);
                                $red_date = isset($lesson_blue['date']) ? "//{$lesson_blue['date']}// " : "";
                                $red_type = '('.__('dictionary.'.$lesson_blue['type']).')';
                                $red_room = __('content.room').' '.$lesson_blue['room'];
                                $red_group = $lesson_blue[$other_partic];
                                $blue_name = __('content.'.$lesson_blue['name']);
                                $blue_type = '('.__('dictionary.'.$lesson_blue['type']).')';
                                $blue_date = isset($lesson_blue['date']) ? "//{$lesson_blue['date']}// " : "";
                                $blue_room = __('content.room').' '.$lesson_blue['room'];
                                $blue_group = $lesson_blue[$other_partic];
                            } elseif (!$lesson_blue) {
                                $blueFontStyle = array('size' => 6, 'color' => '#ffffff');
                                $redFontStyle = array('size' => 6);
                                $red_name = __('content.'.$lesson_red['name']);
                                $red_type = '('.__('dictionary.'.$lesson_red['type']).')';
                                $red_date = isset($lesson_red['date']) ? "//{$lesson_red['date']}// " : "";
                                $red_room = __('content.room').' '.$lesson_red['room'];
                                $red_group = $lesson_red[$other_partic];
                                $blue_name = __('content.'.$lesson_red['name']);
                                $blue_type = '('.__('dictionary.'.$lesson_red['type']).')';
                                $blue_date = isset($lesson_red['date']) ? "//{$lesson_red['date']}// " : "";
                                $blue_room = __('content.room').' '.$lesson_red['room'];
                                $blue_group = $lesson_red[$other_partic];
                            } else {
                                $redFontStyle = array('size' => 6, 'color' => '#ffffff');
                                $blueFontStyle = array('size' => 6);
                                $red_name = __('content.'.$lesson_red['name']);
                                $red_type = '('.__('dictionary.'.$lesson_red['type']).')';
                                $red_date = isset($lesson_red['date']) ? "//{$lesson_red['date']}// " : "";
                                $red_room = __('content.room').' '.$lesson_red['room'];
                                $red_group = $lesson_red[$other_partic];
                                $blue_name = __('content.'.$lesson_blue['name']);
                                $blue_type = '('.__('dictionary.'.$lesson_blue['type']).')';
                                $blue_date = isset($lesson_blue['date']) ? "//{$lesson_blue['date']}// " : "";
                                $blue_room = __('content.room').' '.$lesson_blue['room'];
                                $blue_group = $lesson_blue[$other_partic];
                            }
                            $additional_red_row = $this->getCellAdditional($this->data, $lesson_red, $week_day_id, $red_group, $redFontStyle, $red_name, $red_type, $red_room);
                            $additional_blue_row = $this->getCellAdditional($this->data, $lesson_blue, $week_day_id, $blue_group, $blueFontStyle, $blue_name, $blue_type, $blue_room);
                            $cell_data['add_cell'] = ['value' => $this->cell_width, 'cell_style' => $halfCellStyle];
                            $cell_data['add_text'] = [
                                ['text' => "{$red_name} {$red_type}", 'font_style' => $redFontStyle, 'paragraph_style' => $halfParagraphStyle],
                                ['text' => "{$red_date}{$red_room}", 'font_style' => $redFontStyle, 'paragraph_style' => $halfParagraphStyle],
                                ['text' => $red_group, 'font_style' => $redFontStyle, 'paragraph_style' => $halfParagraphStyle],
                            ];
                            if (! empty($additional_red_row)) {
                                $cell_data['add_text'][] = $additional_red_row;
                            }
                            $cell_data['add_text'][] = ['text' => '------------------------------------------', 'font_style' => $borderFontStyle, 'paragraph_style' => $borderParagraphStyle];
                            $cell_data['add_text'][] = ['text' => "{$blue_name} {$blue_type}", 'font_style' => $blueFontStyle, 'paragraph_style' => $halfParagraphStyle];
                            $cell_data['add_text'][] = ['text' => "{$blue_date}{$blue_room}", 'font_style' => $blueFontStyle, 'paragraph_style' => $halfParagraphStyle];
                            $cell_data['add_text'][] = ['text' => $blue_group, 'font_style' => $blueFontStyle, 'paragraph_style' => $halfParagraphStyle];
                            if (! empty($additional_blue_row)) {
                                $cell_data['add_text'][] = $additional_blue_row;
                            }
                        } else {
                            $cell_data['add_cell'] = ['value' => $this->cell_width, 'cell_style' => $halfCellStyle];
                        }
                    }
                    if (count($cell_data)) {$row_data[] = $cell_data;}
                    $cell_data = [];    
                }
            }
            $table_content[] = [
                'height' => 1200,
                'data' => $row_data
            ];
            $row_data = [];
        }
        
        return $this->getWriter([
            'header_rows' => $data['header_rows'],
            'table_header_cells' => $data['table_header_cells'],
            'table_content' => $table_content
        ]);
    }

    protected function getTableHeaderCells($data, $first_column_width) {
        $week_days = config('enum.week_days');
        $settings = Setting::pluck('value', 'name');
        
        $week_days_limit = $settings['full_time_week_days_limit'] ?? config('site.week_days_limits')['full_time'];
        if (isset($data['week_data']) && isset($data['is_red_week'])) {
            $week_days_limit = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
        }
                
        $table_header_cells = [
            ['width' => $first_column_width, 'text' => __('header.period')]
        ];
        if (isset($data['week_dates'])) {
            $week_dates = json_decode($this->data['week_dates'], true);
            foreach ($week_dates as $week_day_id => $date) {
                if ($week_day_id <= $week_days_limit) {
                    if (is_array($date) && isset($date['is_holiday'])) {
                        $date = date('d.m.y', strtotime($date['date']));
                        $is_holiday_header = ' /'.__('title.holiday').'/';
                    } else {
                        $date = date('d.m.y', strtotime($date));
                        $is_holiday_header = '';
                    }
                    $table_header_cells[] = [
                        'text' => __('week_day.'.$week_days[$week_day_id]).' ('.$date.') '.$is_holiday_header,
                    ];
                }
            }
        } else {
            foreach($week_days as $week_day_id => $week_day_name) {
                if ($week_day_id <= $week_days_limit) {
                    $table_header_cells[] = [
                        'text' => __('week_day.'.$week_day_name),
                    ];
                }
            }
        }
        return $table_header_cells;
    }

}