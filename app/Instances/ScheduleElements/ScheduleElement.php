<?php

namespace App\Instances\ScheduleElements;

use App\Instances\Instance;
use App\ClassPeriod;
use App\Group;
use App\Helpers\DateHelpers;
use App\Lesson;
use App\Setting;
use App\WeekDay;
use Illuminate\Database\Eloquent\Builder;

class ScheduleElement extends Instance
{
    protected $config;
    
    public function getSchedule($incoming_data) {

        $settings = Setting::pluck('value', 'name');
        $data['week_day_ids'] = config('enum.week_day_ids');
        $data['weekly_periods'] = config('enum.weekly_periods');
        $data['weekly_period_ids'] = config('enum.weekly_period_ids');
        $data['weekly_period_colors'] = config('enum.weekly_period_colors');
        $data['class_period_ids'] = config('enum.class_period_ids');
        $model_name = $this->config['model_name'];
        $instance_name = $this->config['instance_name'];
        $schedule_instance_id = $incoming_data["schedule_{$this->config['instance_name']}_id"];
        $data['schedule_instance_id'] = $schedule_instance_id;
        $instance_name_field = $this->config['instance_name_field'];
        $profession_level_name_field = $this->config['profession_level_name_field'];
        $other_lesson_participant = $this->config['other_lesson_participant'];
        $other_lesson_participant_name = $this->config['other_lesson_participant_name'];
        
        $week_number = null;
        if (isset($incoming_data['week_number'])) {
            $week_number = $incoming_data['week_number'];
            $data['is_red_week'] = DateHelpers::weekColorIsRed($week_number);
            $data['week_dates'] = DateHelpers::weekDates($week_number);
        }
        
        $week_border_dates = DateHelpers::weekStartEndDates($week_number);
        if ($week_border_dates) {
            $data['week_data'] = [
                'week_number' => $week_number,
                'start_date' => $week_border_dates['start_date'],
                'end_date' => $week_border_dates['end_date'],
            ];
            $data['week_days_limit'] = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
            $data['class_periods_limit'] = $settings['distance_class_periods_limit'] ?? config('site.class_periods_limits')['distance'];
        } else {
            $data['week_data'] = [
                'week_number' => $week_number,
                'start_date' => null,
                'end_date' => null,
            ];
            $data['week_days_limit'] = $settings['full_time_week_days_limit'] ?? config('site.week_days_limits')['full_time'];
            $data['class_periods_limit'] = $settings['full_time_class_periods_limit'] ?? config('site.class_periods_limits')['full_time'];
        }

        $weekly_period_ids = config('enum.weekly_period_ids');
        $class_periods = ClassPeriod::get();
        $data['class_periods'] = array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()));

        $instance = $model_name::where('id', $schedule_instance_id)->first();
        if ($instance) {
            $data['instance_name'] = $profession_level_name_field !== null ? $instance->$profession_level_name_field : $instance->$instance_name_field;
        }

        if ($instance_name == 'group') {
            $lessons = Lesson::with(['week_day', 'weekly_period', 'class_period', 'lesson_room', 'groups'])->whereHas('groups', function (Builder $query) use ($schedule_instance_id) {
                $query->where('id', $schedule_instance_id);
            })->get();
        } else {
            $lessons = Lesson::with(['lesson_type', $instance_name, 'week_day', 'weekly_period', 'class_period', 'lesson_room'])
                             ->where("{$instance_name}_id", $schedule_instance_id)
                             ->get();
        }

        $data['lessons'] = [];
        foreach ($lessons as $lesson) {

            if (! DateHelpers::testLessonDate($week_number, $lesson)) {
                continue;
            };
            
            $week_schedule_lesson = DateHelpers::getWeeklyScheduleLesson($week_number, $lesson);
            if (isset($week_schedule_lesson)) {
                if ($week_schedule_lesson) {
                    $lesson = $week_schedule_lesson;
                } else {
                    continue;
                }
            }

            if (isset($data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$lesson->weekly_period_id])
                || isset($data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$weekly_period_ids['every_week']]))
            {
                $data['duplicated_lesson'] = [
                    $instance_name => $instance->$instance_name_field,
                    'class_period' => $lesson->class_period->name,
                    'week_day' => $lesson->week_day->name,
                    'weekly_period' => $lesson->weekly_period->name
                ];
                return $data;
            } else {

                if (is_array($other_lesson_participant_name)) {
                    $value = $lesson;
                    foreach ($other_lesson_participant_name as $part) {
                        $value = $value->$part;
                        if (!is_object($value)) {
                            break;
                        }
                    }
                } else {
                    $value = $lesson->$other_lesson_participant_name;
                }

                $data['lessons'][$lesson->class_period_id][$lesson->week_day_id][$lesson->weekly_period_id] = [
                    'id' => $lesson->id,
                    'week_day_id' => $lesson->week_day_id,
                    'weekly_period_id' => $lesson->weekly_period_id,
                    'real_weekly_period_id' => $lesson->real_weekly_period_id ?? null,
                    'class_period_id' => $lesson->class_period_id,
                    'teacher_id' => $lesson->teacher_id,
                    'type' => $lesson->lesson_type->short_notation,
                    'name' => $lesson->name,
                    'room' => $lesson->lesson_room->number,
                    'date' => isset($lesson->date) ? date('d.m.y', strtotime($lesson->date)) : null,
                    $other_lesson_participant => $value
                ];
            }
        }

        return $data;
    }

    public function getMonthSchedule($incoming_data) {

        $model_name = $this->config['model_name'];
        $instance_name = $this->config['instance_name'];
        $schedule_instance_id = $incoming_data["schedule_{$this->config['instance_name']}_id"];
        $data['schedule_instance_id'] = $schedule_instance_id;
        $instance_name_field = $this->config['instance_name_field'];
        $profession_level_name_field = $this->config['profession_level_name_field'];
        $other_lesson_participant = $this->config['other_lesson_participant'];
        $other_lesson_participant_name = $this->config['other_lesson_participant_name'];
        $settings = Setting::pluck('value', 'name');
        $data['class_periods_limit'] = $settings['distance_class_periods_limit'] ?? config('site.class_periods_limits')['distance'];
        $data['week_days_limit'] = $settings['distance_week_days_limit'] ?? config('site.week_days_limits')['distance'];
        $data['week_day_ids'] = config('enum.week_day_ids');
        $data['weekly_periods'] = config('enum.weekly_periods');
        $data['weekly_period_ids'] = config('enum.weekly_period_ids');
        $data['weekly_period_colors'] = config('enum.weekly_period_colors');
        $data['class_period_ids'] = config('enum.class_period_ids');
        $data['month_number'] = $incoming_data['month_number'];
        
        $weekly_period_ids = config('enum.weekly_period_ids');
        $class_periods = ClassPeriod::get();
        $data['class_periods'] = array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()));
    
        $instance = $model_name::where('id', $schedule_instance_id)->first();
        if ($instance) {
            $data['instance_name'] = $profession_level_name_field !== null ? $instance->$profession_level_name_field : $instance->$instance_name_field;
        }
    
        if ($instance_name == 'group') {
            $lessons = Lesson::with(['week_day', 'weekly_period', 'class_period', 'lesson_room', 'groups'])->whereHas('groups', function (Builder $query) use ($schedule_instance_id) {
                $query->where('id', $schedule_instance_id);
            })->get();
        } else {
            $lessons = Lesson::with(['lesson_type', $instance_name, 'week_day', 'weekly_period', 'class_period', 'lesson_room'])
                             ->where("{$instance_name}_id", $schedule_instance_id)
                             ->get();
        }
        
        $month_week_numbers = DateHelpers::getMonthWeekNumbers($incoming_data['month_number']);
        
        $month_value = date('n', strtotime($incoming_data['month_number']));
        $months_genitive = config('enum.months');

        $data['month_name'] = __('header.'.$months_genitive[$month_value]).' '.date('Y', strtotime($incoming_data['month_number'])).' '.__('header.of_year');

        foreach ($month_week_numbers as $week_number) {
            
            $data['weeks'][$week_number]['is_red_week'] = DateHelpers::weekColorIsRed($week_number);
            $data['weeks'][$week_number]['week_dates'] = DateHelpers::weekDates($week_number);
                                    
            $week_border_dates = DateHelpers::weekStartEndDates($week_number);
            $data['weeks'][$week_number]['week_data'] = [
                'week_number' => $week_number,
                'start_date' => $week_border_dates['start_date'],
                'end_date' => $week_border_dates['end_date'],
            ];
            
            $data['weeks'][$week_number]['lessons'] = [];
            $iterated_lessons = $lessons->toArray();
           
            foreach ($iterated_lessons as $key => $lesson) {

                if (! DateHelpers::testLessonDate($week_number, $lessons[$key])) {
                    continue;
                };
                
                $week_schedule_lesson = DateHelpers::getMonthWeeklyScheduleLesson($week_number, $lesson);
                if (isset($week_schedule_lesson)) {
                    if ($week_schedule_lesson) {
                        $lesson = $week_schedule_lesson;
                    } else {
                        continue;
                    }
                }
    
                if (isset($data['weeks'][$week_number]['lessons'][$lesson['class_period_id']][$lesson['week_day_id']][$lesson['weekly_period_id']])
                    || isset($data['weeks'][$week_number]['lessons'][$lesson['class_period_id']][$lesson['week_day_id']][$weekly_period_ids['every_week']]))
                {
                    $data['duplicated_lesson'] = [
                        $instance_name => $instance->$instance_name_field,
                        'class_period' => $lessons[$key]->class_period->name,
                        'week_day' => $lessons[$key]->week_day->name,
                        'weekly_period' => $lessons[$key]->weekly_period->name
                    ];
                    return $data;
                } else {
    
                    if (is_array($other_lesson_participant_name)) {
                        $value = $lessons[$key];
                        foreach ($other_lesson_participant_name as $part) {
                            $value = $value->$part;
                            if (!is_object($value)) {
                                break;
                            }
                        }
                    } else {
                        $value = $lessons[$key]->$other_lesson_participant_name;
                    }
    
                    $data['weeks'][$week_number]['lessons'][$lesson['class_period_id']][$lesson['week_day_id']][$lesson['weekly_period_id']] = [
                        'id' => $lesson['id'],
                        'week_day_id' => $lesson['week_day_id'],
                        'weekly_period_id' => $lesson['weekly_period_id'],
                        'class_period_id' => $lesson['class_period_id'],
                        'teacher_id' => $lesson['teacher_id'],
                        'type' => $lessons[$key]->lesson_type->short_notation,
                        'name' => $lesson['name'],
                        'room' => $lessons[$key]->lesson_room->number,
                        'date' => isset($lesson['date']) ? date('d.m.y', strtotime($lesson['date'])) : null,
                        $other_lesson_participant => $value
                    ];
                }
            }
        }

        return $data;
    }

    public function getModelRechedulingData($incoming_data, $reschedule_data) {

        $class_periods = ClassPeriod::get();
        $week_days = WeekDay::get();
        $rescheduling_lesson = Lesson::where('id', $incoming_data['lesson_id'])->first();
        $incoming_data["schedule_{$this->config['instance_name']}_id"] = $incoming_data["{$this->config['instance_name']}_id"];

        $schedule_data = $this->getSchedule($incoming_data, $this->config);
        if (isset($schedule_data['duplicated_lesson'])) {
            return $schedule_data;
        }
        $schedule_lessons = $schedule_data['lessons'] ?? [];

        $data = [
            'rescheduling_lesson_id' => $rescheduling_lesson->id,
            'class_periods' => $class_periods,
            'other_lesson_participant_name' => $this->config['other_lesson_participant'],
            'teacher_name' => $rescheduling_lesson->teacher->profession_level_name,
            'teacher_id' => $rescheduling_lesson->teacher->id,
            'group_id' => $incoming_data['group_id'] ?? null,
            'week_day_ids' => $schedule_data['week_day_ids'],
            'weekly_periods' => $schedule_data['weekly_periods'],
            'weekly_period_ids' => $schedule_data['weekly_period_ids'],
            'weekly_period_colors' => $schedule_data['weekly_period_colors'],
            'class_period_ids' => $schedule_data['class_period_ids'],
            'week_days_limit' => $schedule_data['week_days_limit'],
            'class_periods_limit' => $schedule_data['class_periods_limit'],
        ];

        $data['week_dates'] = $reschedule_data['week_dates'];
        $data['is_red_week'] = $reschedule_data['is_red_week'];
        $data['week_data'] = $reschedule_data['week_data'];

        if (isset($incoming_data['group_id'])) {
            $data['group_name'] = Group::find($incoming_data['group_id'])->name;
        }

        foreach ($class_periods as $class_period) {
            foreach ($week_days as $week_day) {
                if (isset($schedule_lessons[$class_period->id][$week_day->id])) {
                    $this_lessons = $schedule_lessons[$class_period->id][$week_day->id];
                    foreach ($this_lessons as $weekly_period_id => $this_lesson) {
                        $data['periods'][$class_period->id][$week_day->id][$weekly_period_id] = $this_lesson;
                    }
                }
                if (isset($reschedule_data['free_periods'][$class_period->id][$week_day->id])) {
                    $this_periods = $reschedule_data['free_periods'][$class_period->id][$week_day->id];
                    foreach ($this_periods as $weekly_period_id => $this_period) {
                        $data['periods'][$class_period->id][$week_day->id][$weekly_period_id] = $this_period;
                    }
                }
            }
        }

        return $data;
    }

    public function scheduleExport($data) {
        
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

    public function monthScheduleExport($data) {
        
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

    public function replacementExport($data) {
        
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