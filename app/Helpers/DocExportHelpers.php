<?php

namespace App\Helpers;

use App\ClassPeriod;
use App\Setting;


class DocExportHelpers
{
    public static function scheduleExport($data) {
        
        $lessons = json_decode($data['lessons'], true);
        $week_day_ids = config('enum.week_day_ids');
        $weekly_period_id = config('enum.weekly_period_ids');
        $class_period_ids = config('enum.class_period_ids');
        $class_periods = ClassPeriod::get();
        $class_periods = array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()));
        $other_partic = $data['other_participant'];
        $week_data = isset($data['week_data']) ? json_decode($data['week_data'], true) : null;
        $settings = Setting::pluck('value', 'name');
        $class_periods_limit = $settings['full_time_class_periods_limit'] ?? config('site.class_periods_limits')['full_time'];
        $week_days_limit = $settings['full_time_week_days_limit'] ?? config('site.week_days_limits')['full_time'];
        //------------------------------------------------------
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
       
        $section = $phpWord->addSection(array(
            'orientation' => 'landscape',
            'marginLeft'   => 600,
            'marginRight'  => 600,
            'marginTop'    => 600,
            'marginBottom' => 600,
        ));

        $week_period_string = '';
        if (isset($data['week_data']) && isset($data['is_red_week'])) {
            $week_data = json_decode($data['week_data'], true);
            $week_color = $data['is_red_week'] ? __('header.red_week_color') : __('header.blue_week_color');
            $week_period_string = str_replace(['?-1', '?-2', '?-3' ], [$week_data['start_date'], $week_data['end_date'], $week_color], __('header.week_period_string')); 
            $week_days_limit = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
        }
        
        $section->addTextBreak(1);
        if (isset($data['header_data'])) {
            $header_data = json_decode($data['header_data'], true);
            $section->addText(__('header.replacement_variants_export_to_docx').$week_period_string, ['bold' => true], array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
            $section->addText(str_replace(['?-1', '?-2', '?-3' ], [__('dictionary.'.$header_data['class_period']), __('dictionary.'.$header_data['week_day']), $data['date_or_weekly_period']], __('header.replaceable_lesson_export_to_docx')), null, array('spaceBefore' => 0, 'spaceAfter' => 0));
            $section->addText(__('header.of_teacher_export_to_docx').$header_data['teacher'], null, array('spaceBefore' => 0, 'spaceAfter' => 0));
            $section->addText(__('header.of_group_export_to_docx').$header_data['group'], null, array('spaceBefore' => 0, 'spaceAfter' => 100));
        } else {
            if (isset($data['rescheduling_lesson_id'])) {
                if ($data['is_reschedule_for'] == 'teacher') {
                    $participant_header = __('header.teacher');
                    $of_participant_header = __('header.of_teacher');
                    $participant = $data['teacher_name'];
                } elseif ($data['is_reschedule_for'] == 'group') {
                    $participant_header = __('header.group');
                    $of_participant_header = __('header.of_group');
                    $participant = $data['group_name'];
                } else {
                    $participant_header = '';
                    $of_participant_header = ''; 
                }
                $section->addText(__('header.reschedule_variants_export_to_docx').$of_participant_header.$week_period_string, ['bold' => true], array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
                $section->addText("{$participant_header}: {$participant}");
            } else {
                if (isset($data['teacher_name'])) {
                    $participant_header = __('header.teacher');
                    $participant = $data['teacher_name'];
                } elseif (isset($data['group_name'])) {
                    $participant_header = __('header.group');
                    $participant = $data['group_name'];
                } else {
                    $participant_header = '';
                    $participant = '';
                }
                $section->addText(__('header.schedule_export_to_docx').$week_period_string, ['bold' => true], array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
                $section->addText("{$participant_header}: {$participant}");
            }
        }
        
        $styleTable = array('borderSize' => 6, 'borderColor' => '999999');
                
        $headerCellStyle = array('valign' => 'center');
        $headerFontStyle = array('size' => 8);
        $headerParagraphStyle = array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0);

        $everyWeekCellStyle = array('valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER);
        $everyWeekParagraphStyle = array('spaceBefore' => 0, 'spaceAfter' => 0, 'align' => 'center');
        
        $halfCellStyle = array('valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER);
        $halfParagraphStyle = array('spaceBefore' => 0, 'spaceAfter' => 0, 'align' => 'center');

        $borderFontStyle = array('color' => '999999', 'size' => 6);
        $borderParagraphStyle = array('spaceBefore' => 0, 'spaceAfter' => 0, 'align' => 'center');

        $phpWord->addTableStyle('Schedule', $styleTable);
        
        $table = $section->addTable('Schedule');
        $table->addRow(null, array('tblHeader' => true));
        $table->addCell(1300, $headerCellStyle)->addText(__('header.period'), $headerFontStyle, $headerParagraphStyle);
        $week_days = config('enum.week_days');
        if (isset($data['week_dates'])) {
            $week_dates = json_decode($data['week_dates'], true);
            foreach ($week_dates as $week_day_id => $date) {
                if ($week_day_id <= $week_days_limit) {
                    if (is_array($date) && isset($date['is_holiday'])) {
                        $date = date('d.m.y', strtotime($date['date']));
                        $is_holiday_header = ' /'.__('title.holiday').'/';
                    } else {
                        $date = date('d.m.y', strtotime($date));
                        $is_holiday_header = '';
                    }
                    $table->addCell(2000, $headerCellStyle)->addText(__('week_day.'.$week_days[$week_day_id]).' ('.$date.') '.$is_holiday_header, $headerFontStyle, $headerParagraphStyle);
                }
            }
        } else {
            foreach($week_days as $week_day_id => $week_day_name) {
                if ($week_day_id <= $week_days_limit) {
                    $table->addCell(2000, $headerCellStyle)->addText(__('week_day.'.$week_day_name), $headerFontStyle, $headerParagraphStyle);
                }
            }
        }
        foreach($class_period_ids as $lesson_name => $class_period_id) {
            if ($class_period_id <= $class_periods_limit) {
                $table->addRow(1200);
                $left_header_cell = $table->addCell(1300, $headerCellStyle);
                $left_header_cell->addText($class_period_id, $headerFontStyle, $headerParagraphStyle);
                $left_header_cell->addText(date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['start'])).' - '.date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['end'])), $headerFontStyle, $headerParagraphStyle);
                foreach($week_day_ids as $wd_name => $week_day_id) {
                    $is_holiday = isset($week_dates) && is_array($week_dates[$week_day_id]) && isset($week_dates[$week_day_id]['is_holiday']); 
                    if ($week_day_id <= $week_days_limit) {
                        if (isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']]) && ! $is_holiday) {
                            $lesson = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']];
                            $lesson_n = __('content.'.$lesson['name']); 
                            $lesson_type = '('.__('dictionary.'.$lesson['type']).')';
                            $lesson_room = __('content.room').' '.$lesson['room'];
                            $lesson_other_participant = $lesson[$other_partic];
                            $everyWeekFontStyle = array('size' => 8);
                            $reschedule_massage = false;
                            $replacement_massage = false;
                            if (isset($lesson['for_replacement']) && $lesson['for_replacement']) {
                                $everyWeekFontStyle = array_merge($everyWeekFontStyle, ['shading' => array('fill' => '#DCDCDC')]);
                                $lesson_other_participant = $lesson['teacher'];
                            } elseif ((! isset($data['week_dates']) && isset($lesson['id']) && isset($data['replaceable_lesson_id']) && $lesson['id'] == $data['replaceable_lesson_id'])
                                      ||
                                      (isset($data['week_dates'])
                                       && isset($data['date_or_weekly_period'])
                                       && $data['date_or_weekly_period'] === date('d.m.y', strtotime($week_dates[$week_day_id]))
                                       && isset($lesson['id']) 
                                       && isset($data['replaceable_lesson_id']) 
                                       && $lesson['id'] == $data['replaceable_lesson_id']))
                            {
                                $everyWeekFontStyle = array_merge($everyWeekFontStyle, ['bold' => true]);
                                $replacement_massage = true;
                            }
                            if (!is_array($lesson) && $lesson) {
                                $everyWeekFontStyle = array_merge($everyWeekFontStyle, ['name' => 'Segoe Script']);
                                $lesson = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']];
                                $lesson_n = '';
                                $lesson_type = '';
                                $lesson_room = '';
                                $lesson_other_participant = __('title.reschedule_variant');
                            } elseif (isset($lesson['id']) && isset($data['rescheduling_lesson_id']) && $lesson['id'] == $data['rescheduling_lesson_id']) {
                                $everyWeekFontStyle = array_merge($everyWeekFontStyle, ['bold' => true]);
                                $reschedule_massage = true;
                            }
                            $currentEveryWeekCellStyle = $everyWeekCellStyle;
                            if (isset($lesson['date'])) {
                                $currentEveryWeekCellStyle = array_merge($everyWeekCellStyle, ['borderStyle' => 'double', 'borderSize' => 8]);
                            }
                            $sell = $table->addCell(2000, $currentEveryWeekCellStyle);
                            $sell->addText("{$lesson_n} {$lesson_type}", $everyWeekFontStyle, $everyWeekParagraphStyle);
                            $sell->addText("{$lesson_room}", $everyWeekFontStyle, $everyWeekParagraphStyle);
                            $sell->addText($lesson_other_participant, $everyWeekFontStyle, $everyWeekParagraphStyle);
                            $reschedule_massage ? $sell->addText('('.__('title.rescheduling_lesson').')', ['size' => 8, 'name' => 'Segoe Script', 'bold' => true], ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]) : '';
                            $replacement_massage ? $sell->addText('('.__('title.replaceable_lesson').')', ['size' => 6, 'shading' => array('fill' => '#DCDCDC')], ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]) : '';
                        } elseif (isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']]) || isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']])) {
                            $lesson_red = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']] ?? false;
                            $lesson_blue = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']] ?? false;
                            $red_replacement_massage = false;
                            $blue_replacement_massage = false;
                            $red_reschedule_massage = false;
                            $blue_reschedule_massage = false;
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
                                $redFontStyle = array('size' => 6);
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
                            if (isset($lesson_red['for_replacement']) && $lesson_red['for_replacement']) {
                                $redFontStyle = array_merge($redFontStyle, ['shading' => array('fill' => '#DCDCDC')]);
                                $red_group = $lesson_red['teacher'];
                            } elseif (isset($lesson_red['id']) && isset($data['replaceable_lesson_id']) && $lesson_red['id'] == $data['replaceable_lesson_id']) {
                                $redFontStyle = array_merge($redFontStyle, ['bold' => true]);
                                $red_replacement_massage = true;
                            }
                            if (isset($lesson_blue['for_replacement']) && $lesson_blue['for_replacement']) {
                                $blueFontStyle = array_merge($blueFontStyle, ['shading' => array('fill' => '#DCDCDC')]);
                                $blue_group = $lesson_blue['teacher'];
                            } elseif (isset($lesson_blue['id']) && isset($data['replaceable_lesson_id']) && $lesson_blue['id'] == $data['replaceable_lesson_id']) {
                                $blueFontStyle = array_merge($blueFontStyle, ['bold' => true]);
                                $blue_replacement_massage = true;
                            }

                            if (!is_array($lesson_red) && $lesson_red) {
                                $redFontStyle = array_merge($redFontStyle, ['size' => 8, 'name' => 'Segoe Script']);
                                $red_name = '';
                                $red_type = '';
                                $red_room = '';
                                $red_group = __('title.reschedule_variant');
                            } elseif (isset($lesson_red['id']) && isset($data['rescheduling_lesson_id']) && $lesson_red['id'] == $data['rescheduling_lesson_id']) {
                                $redFontStyle = array_merge($redFontStyle, ['bold' => true]);
                                $red_reschedule_massage = true;
                            }
                            if (!is_array($lesson_blue) && $lesson_blue) {
                                $blueFontStyle = array_merge($blueFontStyle, ['size' => 8, 'name' => 'Segoe Script']);
                                $blue_name = '';
                                $blue_type = '';
                                $blue_room = '';
                                $blue_group = __('title.reschedule_variant');
                            } elseif (isset($lesson_blue['id']) && isset($data['rescheduling_lesson_id']) && $lesson_blue['id'] == $data['rescheduling_lesson_id']) {
                                $blueFontStyle = array_merge($blueFontStyle, ['bold' => true]);
                                $blue_reschedule_massage = true;
                            }
                            $sell = $table->addCell(2000, $halfCellStyle);
                            $sell->addText("{$red_name} {$red_type}", $redFontStyle, $halfParagraphStyle);
                            $sell->addText("{$red_date}{$red_room}", $redFontStyle, $halfParagraphStyle);
                            $sell->addText($red_group, $redFontStyle, $halfParagraphStyle);
                            $red_reschedule_massage ? $sell->addText('('.__('title.rescheduling_lesson').')', ['size' => 6, 'name' => 'Segoe Script', 'bold' => true], ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]) : '';
                            $red_replacement_massage ? $sell->addText('('.__('title.replaceable_lesson').')', ['size' => 6, 'bold' => true, 'shading' => array('fill' => '#DCDCDC')], ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]) : '';
                            
                            $sell->addText('------------------------------------------', $borderFontStyle, $borderParagraphStyle);
                            
                            $sell->addText("{$blue_name} {$blue_type}", $blueFontStyle, $halfParagraphStyle);
                            $sell->addText("{$blue_date}{$blue_room}", $blueFontStyle, $halfParagraphStyle);
                            $sell->addText($blue_group, $blueFontStyle, $halfParagraphStyle);
                            $blue_reschedule_massage ? $sell->addText('('.__('title.rescheduling_lesson').')', ['size' => 6, 'name' => 'Segoe Script', 'bold' => true], ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]) : '';
                            $blue_replacement_massage ? $sell->addText('('.__('title.replaceable_lesson').')', ['size' => 6, 'bold' => true, 'shading' => array('fill' => '#DCDCDC')], ['align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0]) : '';
                        } else {
                            $table->addCell(2000, $everyWeekCellStyle);
                        }
                    }    
                }
            }    
        }

        return \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    }

    public static function monthScheduleExport($data) {
        
        $weeks = json_decode($data['weeks'], true);
        $week_day_ids = config('enum.week_day_ids');
        $weekly_period_id = config('enum.weekly_period_ids');
        $class_period_ids = config('enum.class_period_ids');
        $class_periods = ClassPeriod::get();
        $class_periods = array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()));
        $other_partic = $data['other_participant'];
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

        if (isset($data['teacher_name'])) {
            $participant_header = __('header.teacher');
            $participant = $data['teacher_name'];
        } elseif (isset($data['group_name'])) {
            $participant_header = __('header.group');
            $participant = $data['group_name'];
        } else {
            $participant_header = '';
            $participant = '';
        }
        $section->addText(__('header.schedule_on_month_export_to_docx').$data['month_name'], ['bold' => true], array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
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
            $table->addCell(1300, $headerCellStyle)->addText($week_color_name, $headerFontStyle, $headerParagraphStyle);
            
            foreach ($week_dates as $week_day_id => $date) {
                if ($week_day_id <= $week_days_limit) {
                    if (is_array($date) && isset($date['is_holiday'])) {
                        $date = date('d.m.y', strtotime($date['date']));
                        $is_holiday_header = ' /'.__('title.holiday').'/';
                    } else {
                        $date = date('d.m.y', strtotime($date));
                        $is_holiday_header = '';
                    }
                    $table->addCell(2000, $headerCellStyle)->addText(__('week_day.'.$week_days[$week_day_id]).' ('.$date.') '.$is_holiday_header, $headerFontStyle, $headerParagraphStyle);
                }
            }
            
            foreach ($class_period_ids as $lesson_name => $class_period_id) {
                if ($class_period_id <= $class_periods_limit) {
                    $section->addText('');
                    $table->addRow(1200);
                    $left_header_cell = $table->addCell(1300, $headerCellStyle);
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
                                $sell = $table->addCell(2000, $currentEveryWeekCellStyle);
                                $sell->addText("{$lesson_n} {$lesson_type}", $everyWeekFontStyle, $everyWeekParagraphStyle);
                                $sell->addText("{$lesson_room}", $everyWeekFontStyle, $everyWeekParagraphStyle);
                                $sell->addText($lesson_other_participant, $everyWeekFontStyle, $everyWeekParagraphStyle);
                                
                            } else {
                                $table->addCell(2000, $everyWeekCellStyle);
                            }
                        }    
                    }
                }    
            }
        }
        
        return \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    }

    public static function replacementExport($data) {
        
        $table_properties = config('tables.replacement_variants');
        $header_data = json_decode($data['header_data'], true);
        $replacement_lessons = json_decode($data['replacement_lessons'], true);
        
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
       
        $section = $phpWord->addSection(array(
            'orientation' => 'landscape',
            'marginLeft'   => 600,
            'marginRight'  => 600,
            'marginTop'    => 600,
            'marginBottom' => 600,
        ));
        
        $week_period_string = '';
        if (isset($data['week_data']) && isset($data['is_red_week'])) {
            $week_data = json_decode($data['week_data'], true);
            $week_color = $data['is_red_week'] ? __('header.red_week_color') : __('header.blue_week_color');
            $week_period_string = str_replace(['?-1', '?-2', '?-3' ], [$week_data['start_date'], $week_data['end_date'], $week_color], __('header.week_period_string')); 
        }
        
        $section->addText(__('header.replacement_variants_export_to_docx').$week_period_string, ['bold' => true], array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0));
        $section->addText(str_replace(['?-1', '?-2', '?-3' ], [__('dictionary.'.$header_data['class_period']), __('dictionary.'.$header_data['week_day']), $data['date_or_weekly_period']], __('header.replaceable_lesson_export_to_docx')), null, array('spaceBefore' => 0, 'spaceAfter' => 0));
        $section->addText(__('header.of_teacher_export_to_docx').$header_data['teacher'], null, array('spaceBefore' => 0, 'spaceAfter' => 0));
        $section->addText(__('header.of_group_export_to_docx').$header_data['group'], null, array('spaceBefore' => 0, 'spaceAfter' => 100));
        
        $styleTable = array('borderSize' => 6, 'borderColor' => '999999');
                
        $headerCellStyle = array('valign' => 'center');
        $headerFontStyle = array('bold' => true, 'size' => 8);
        $headerParagraphStyle = array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0);

        $ordinaryCellStyle = array('valign' => 'center');
        $ordinaryFontStyle = array('size' => 8);
        $ordinaryParagraphStyle = array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0);

        $phpWord->addTableStyle('Replacement', $styleTable);
        
        $table = $section->addTable('Replacement');
        $table->addRow(null, array('tblHeader' => true));
        foreach ($table_properties as $property) {
            if (isset($data['is_red_week']) && __('table_header.'.$property['header']) == __('table_header.weekly_period')) {
                continue;
            }
            $table->addCell(2000, $headerCellStyle)->addText(__('table_header.'.$property['header']), $headerFontStyle, $headerParagraphStyle); 
        }
        foreach($replacement_lessons as $lesson) {
            $table->addRow(null);
            
            foreach($table_properties as $property) {
                $field = $property['field'];
                if (isset($data['is_red_week']) && $field == 'weekly_period_id') {
                    continue;
                } elseif ($field == 'week_day_id' && isset($lesson['date'])) {
                    $content = __('dictionary.'.$lesson['name']).' ('.__('dictionary.'.$lesson['type']).')';
                } elseif (is_array($lesson[$field])) {
                    $content = \Lang::has('dictionary.'.$lesson[$field]['name']) ? __('dictionary.'.$lesson[$field]['name']) : $lesson[$field]['name'];
                } else {
                    $content = \Lang::has('dictionary.'.$lesson[$field]) ? __('dictionary.'.$lesson[$field]) : $lesson[$field]; 
                }
                $table->addCell(2000, $ordinaryCellStyle)->addText($content, $ordinaryFontStyle, $ordinaryParagraphStyle);
            }
        }

        return \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    }
    
    
}