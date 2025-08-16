<?php

namespace App\DocExporters\ManyTables;

use App\ClassPeriod;
use App\Setting;

class DocExporterMonthSchedule
{
    public $data;
    protected $leftHeaderCellWidth = 1300;
    protected $cellWidth = 1600;
    protected $rowHeight = 900;

    public function __construct($data) {
        $this->data = $data;
    }

    public function createWriter() {
        
        $weeks = json_decode($this->data['weeks'], true);
        $week_day_ids = config('enum.week_day_ids');
        $weekly_period_id = config('enum.weekly_period_ids');
        $class_period_ids = config('enum.class_period_ids');
        $class_periods = ClassPeriod::get();
        $class_periods = array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()));
        $other_partic = $this->data['other_participant'];
        $week_days = config('enum.week_days');
        $settings = Setting::pluck('value', 'name');
        $class_periods_limit = $settings['full_time_class_periods_limit'] ?? config('site.class_periods_limits')['full_time'];
        $week_days_limit = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
        //------------------------------------------------------
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
       
        $section = $phpWord->addSection(array(
            // 'orientation' => 'landscape',
            'marginLeft'   => 600,
            'marginRight'  => 600,
            'marginTop'    => 600,
            'marginBottom' => 600,
        ));

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
        $section->addText(__('header.schedule_on_month_export_to_docx').$this->data['month_name'], ['bold' => true], array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
        $section->addText("{$participant_header}: {$participant}");
        
        $styleTable = array('borderSize' => 6, 'borderColor' => '999999');
                
        $headerCellStyle = array('valign' => 'center');
        $headerFontStyle = array('size' => 8);
        $headerParagraphStyle = array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0);

        $everyWeekCellStyle = array('valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER);
        $everyWeekParagraphStyle = array('spaceBefore' => 0, 'spaceAfter' => 0, 'align' => 'center');
        
        $phpWord->addTableStyle('Schedule', $styleTable);
        
        foreach ($weeks as $week) {
            $lessons = $week['lessons'];
            $week_dates = $week['week_dates'];
            $week_color_name = $week['is_red_week'] ? __('title.red_week') : __('title.blue_week');
            $table = $section->addTable('Schedule');
            $table->addRow(null, array('tblHeader' => true));
            $table->addCell($this->leftHeaderCellWidth, $headerCellStyle)->addText($week_color_name, $headerFontStyle, $headerParagraphStyle);
            
            foreach ($week_dates as $week_day_id => $date) {
                if ($week_day_id <= $week_days_limit) {
                    if (is_array($date) && isset($date['is_holiday'])) {
                        $date = date('d.m.y', strtotime($date['date']));
                        $is_holiday_header = ' /'.__('title.holiday').'/';
                    } else {
                        $date = date('d.m.y', strtotime($date));
                        $is_holiday_header = '';
                    }
                    $table->addCell($this->cellWidth, $headerCellStyle)->addText(__('week_day.'.$week_days[$week_day_id]).' ('.$date.') '.$is_holiday_header, $headerFontStyle, $headerParagraphStyle);
                }
            }
            
            foreach ($class_period_ids as $lesson_name => $class_period_id) {
                if ($class_period_id <= $class_periods_limit) {
                    $section->addText('');
                    $table->addRow($this->rowHeight);
                    $left_header_cell = $table->addCell($this->leftHeaderCellWidth, $headerCellStyle);
                    $left_header_cell->addText($class_period_id, $headerFontStyle, $headerParagraphStyle);
                    $left_header_cell->addText(date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['start'])).' - '.date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['end'])), $headerFontStyle, $headerParagraphStyle);
                    
                    foreach ($week_day_ids as $wd_name => $week_day_id) {
                        $is_holiday = is_array($week_dates[$week_day_id]) && isset($week_dates[$week_day_id]['is_holiday']);
                        if ($week_day_id <= $week_days_limit) {
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
                                $sell = $table->addCell($this->cellWidth, $currentEveryWeekCellStyle);
                                $sell->addText("{$lesson_n} {$lesson_type}", $everyWeekFontStyle, $everyWeekParagraphStyle);
                                $sell->addText("{$lesson_room}", $everyWeekFontStyle, $everyWeekParagraphStyle);
                                $sell->addText($lesson_other_participant, $everyWeekFontStyle, $everyWeekParagraphStyle);
                                
                            } else {
                                $table->addCell($this->cellWidth, $everyWeekCellStyle);
                            }
                        }    
                    }
                }    
            }
        }
        
        return \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    }

    
}