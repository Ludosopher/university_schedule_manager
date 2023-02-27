<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Заголовок страницы</title>

<style>
    .top-header {
        font-size: 17px;
        margin-top: 1.5rem;
    }

    .replacement-schedule-header-div {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }

    .schedule-period {
        width: 10%;
    }

    .schedule-period-name {
        font-size: 17px;
        font-weight: bold;
    }

    .schedule-period-time {
        font-size: 12px;
    }

    .schedule-cell {
        text-align: center;
        position: relative !important;
        padding: 0rem !important;
        width: 15% !important;
        height: 100px;
    }

    .schedule-actions-div {
        padding: 0px 5px 0px 5px;
    }

    .schedule-subject {
        font-size: 15px;
        line-height: 100%;
        padding-top: 4px;
    }

    .schedule-room {
        font-size: 13px;
        line-height: 150%;
    }

    .schedule-group {
        font-size: 13px;
        line-height: 150%;
    }

    .schedule-cell-top {
        text-align: center;
        margin: 0px !important;
        padding-top: 0px;
        height: 50%;
        width: 100%;
        position: absolute !important;
        top: 0 !important;
        border-bottom: 1px solid #dbdbdb;
    }

    .schedule-subject-half {
        font-size: 14px;
        font-weight: bold;
        line-height: 80%;
        margin-top: 2px;
    }

    .schedule-room-half {
        font-size: 11px;
        font-weight: bold;
        letter-spacing: 1.5px;
        line-height: 110%;
    }

    .schedule-group-half {
        font-size: 11px;
        font-weight: bold;
        letter-spacing: 1.5px;
        line-height: 110%;
    }

    .schedule-cell-bottom {
        text-align: center;
        margin: 0px !important;
        padding-top: 0px;
        height: 50%;
        width: 100%;
        position: absolute !important;
        bottom: 0 !important;
        border-top: 1px solid #dbdbdb;
    }
</style>

</head>
  <body>
    <p>Здравствуйте, уважаемый {{ $data['addressee_name'] }}!</p>
    <p>К Вам обращается {{ $data['requester_name'] }}</p>
    <p>Прошу поменяться со мной занятиями в группе {{ $data['group'] }}, то есть,</p>
    <p>{{ $data['replaceable_lesson_description'] }};</p>
    <p>{{ $data['replacing_lesson_description'] }}</p>
    <p>Для ответа перейдите в свой 
       <a class="" href="{{ route('my_replacement_requests') }}">
            личный кабинет                                 
       </a> 
       в "Менеджере расписания".
    </p>

    @foreach($data['schedule_data'] as $data)
        @if(isset($data['week_data']) && isset($data['is_red_week']))
        @php
            $is_red_week = 0;
            $week_color = "синяя";
            $bg_color = '#ace7f2';
            if ($data['is_red_week']) {
                $is_red_week = 1;
                $week_color = "красная";
                $bg_color = '#ffb3b9';
            }
        @endphp
        <h4 class="top-header">Расписание занятий преподавателя {{ $data['instance_name'] ?? ''}} с {{ $data['week_data']['start_date'] }} по {{ $data['week_data']['end_date'] }} <span style="background-color: {{ $bg_color }};">( {{ $week_color }} неделя )</span></h4>   
        @else
            <h4 class="top-header">Регулярное расписание занятий преподавателя {{ $data['instance_name'] ?? ''}}</h4>
        @endif
        <table style="border-collapse: collapse; border: 1px solid grey; width: 80%">
            <thead>
                <tr>
                    <th style="border: 1px solid grey; text-align: center;">Пара</th>
                    @if(isset($data['week_dates']))
                        @php
                            $week_days_ru = config('enum.week_days_ru');
                        @endphp
                        @foreach($data['week_dates'] as $week_day_id => $date)
                            @if(is_array($date) && isset($date['is_holiday']))
                                <th class="text-uppercase" style="color: red;" title="Праздничный день">{{ $week_days_ru[$week_day_id] }} ({{ date('d.m.y', strtotime($date['date'])) }})</th>
                            @else
                                <th class="text-uppercase">{{ $week_days_ru[$week_day_id] }} ({{ date('d.m.y', strtotime($date)) }})</th>
                            @endif
                        @endforeach
                    @else
                        <th style="border: 1px solid grey; text-align: center;">Понедельник</th>
                        <th style="border: 1px solid grey; text-align: center;">Вторник</th>
                        <th style="border: 1px solid grey; text-align: center;">Среда</th>
                        <th style="border: 1px solid grey; text-align: center;">Четверг</th>
                        <th style="border: 1px solid grey; text-align: center;">Пятница</th>
                        <th style="border: 1px solid grey; text-align: center;">Суббота</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if(isset($data) && isset($data['class_periods']) && isset($data['lessons']))
                    @php
                        $week_day_ids = config('enum.week_day_ids');
                        $weekly_period = config('enum.weekly_periods');
                        $weekly_period_id = config('enum.weekly_period_ids');
                        $weekly_period_color = config('enum.weekly_period_colors');
                        $class_period_ids = config('enum.class_period_ids');
                        $class_periods = $data['class_periods'];
                        $lessons = $data['lessons'];
                    @endphp
                    @foreach($class_period_ids as $lesson_name => $class_period_id)
                        <tr>
                            @php
                                $class_period_start_time = date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['start']));
                                $class_period_end_time = date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['end'])); 
                            @endphp
                            <td class="schedule-period" style="border: 1px solid grey; text-align: center;">
                                <div class="schedule-period-name">{{ $class_period_id }}</div>
                                <div class="schedule-period-time">
                                    {{ $class_period_start_time }} - {{ $class_period_end_time }}
                                </div>
                            </td>

                            @foreach($week_day_ids as $wd_name => $week_day_id)
                                @php
                                    $is_holiday = isset($data['week_dates']) && is_array($data['week_dates'][$week_day_id]) && isset($data['week_dates'][$week_day_id]['is_holiday']);
                                @endphp
                                @if (isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']])
                                     && ! $is_holiday)
                                    @php 
                                        $lesson = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']];
                                        $other_lesson_participant_name = 'group';
                                        if (isset($lesson['is_replaceable'])) {
                                            $cell_bg_color = '#FFFF00';
                                            $title = 'Заменяемое занятие другого преподавателя';
                                            $other_lesson_participant_name = 'teacher_name';
                                        } elseif (isset($lesson['is_replacing'])) {
                                            $cell_bg_color = '#98FB98';
                                            $title = 'Ваше занятие на замену';
                                        } elseif (isset($lesson['date'])) {
                                            $cell_bg_color = '#D3D3D3';
                                            $title = 'Единоразовое занятие';
                                        } else {
                                            $cell_bg_color = $weekly_period_color[$weekly_period_id['every_week']];
                                            $title = '';
                                        }
                                    @endphp
                                    <td class="schedule-cell" style="background-color: {{ $cell_bg_color }}; border: 1px solid grey;" title="{{ $title }}">
                                        <div class="schedule-actions-div">
                                            <div class="schedule-subject">{{ $lesson['name'] }} ({{ $lesson['type'] }})</div>
                                            <div class="schedule-room">ауд. {{ $lesson['room'] }}</div>
                                            <div class="schedule-group">{{ $lesson[$other_lesson_participant_name] }}</div>
                                        </div>
                                    </td>
                                @elseif(isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']]) || isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']]))
                                    @php
                                        $lesson_red = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']] ?? false;
                                        $lesson_blue = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']] ?? false;
                                        $other_lesson_red_participant_name = 'group';
                                        if (isset($lesson_red['is_replaceable'])) {
                                            $cell_red_bg_color = '#FFFF00';
                                            $title_red = 'Заменяемое занятие другого преподавателя';
                                            $other_lesson_red_participant_name = 'teacher_name';
                                        } elseif (isset($lesson_red['is_replacing'])) {
                                            $cell_red_bg_color = '#98FB98';
                                            $title_red = 'Ваше занятие на замену';
                                        } else {
                                            $cell_red_bg_color = $weekly_period_color[$weekly_period_id['red_week']];
                                            $title_red = '';
                                        }
                                        $other_lesson_blue_participant_name = 'group';
                                        if (isset($lesson_blue['is_replaceable'])) {
                                            $cell_blue_bg_color = '#FFFF00';
                                            $title_blue = 'Заменяемое занятие другого преподавателя';
                                            $other_lesson_blue_participant_name = 'teacher_name';
                                        } elseif (isset($lesson_blue['is_replacing'])) {
                                            $cell_blue_bg_color = '#98FB98';
                                            $title_blue = 'Ваше занятие на замену';
                                        } else {
                                            $cell_blue_bg_color = $weekly_period_color[$weekly_period_id['blue_week']];
                                            $title_blue = '';
                                        }
                                    @endphp
                                    <td class="schedule-cell" style="border: 1px solid grey;">
                                        @if($lesson_red)
                                            <div class="schedule-cell-top" style="background-color: {{ $cell_red_bg_color }}" title="{{ $title_red }}">
                                                <div class="schedule-subject-half">{{ $lesson_red['name'] }} ({{ $lesson_red['type'] }})</div>
                                                <div class="schedule-room-half">ауд. {{ $lesson_red['room'] }}</div>
                                                <div class="schedule-group-half">{{ $lesson_red[$other_lesson_red_participant_name] }}</div>
                                            </div>
                                        @endif
                                        @if($lesson_blue)
                                            <div class="schedule-cell-bottom" style="background-color: {{ $cell_blue_bg_color }}" title="{{ $title_blue }}">
                                                <div class="schedule-subject-half">{{ $lesson_blue['name'] }} ({{ $lesson_blue['type'] }})</div>
                                                <div class="schedule-room-half">ауд. {{ $lesson_blue['room'] }}</div>
                                                <div class="schedule-group-half">{{ $lesson_blue[$other_lesson_blue_participant_name] }}</div>
                                            </div>
                                        @endif
                                    </td>
                                @else
                                    <td class="schedule-cell" style="border: 1px solid grey;"></td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    @endforeach
  </body>
</html>