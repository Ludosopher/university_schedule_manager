@extends('layouts.app')
@section('content')
    <div class="container">
    @if($errors->any())
        <div class="alertFail">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ __('user_validation.invalid_input_data') }}
        </div>
    @endif
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
        <h1 class="top-header">Расписание занятий преподавателя с {{ $data['week_data']['start_date'] }} по {{ $data['week_data']['end_date'] }} <span style="background-color: {{ $bg_color }};">( {{ $week_color }} неделя )</span></h1>   
    @else
        <h1 class="top-header">Регулярное расписание занятий преподавателя</h1>
    @endif
    <div class="replacement-schedule-header-div">
        <h3>Преподаватель: {{ $data['instance_name'] ?? ''}}</h3>
        <div class="schedule-button-group">
            <form method="POST" class="month-schedule-form" action="{{ route('teacher-month-schedule') }}" target="_blank">
            @csrf
                <input type="month" name="month_number" value="">
                <input type="hidden" name="schedule_teacher_id" value="{{ $data['schedule_instance_id'] }}">
                <button type="submit" class="btn btn-success month-schedule-button">За этот месяц</button>
            </form>
            <form method="POST" action="{{ route('teacher-schedule', ['schedule_teacher_id' => $data['schedule_instance_id']]) }}" target="_blank">
            @csrf
                <input type="week" name="week_number" value="{{ $data['week_data']['week_number'] }}">
                <button type="submit" class="btn btn-primary">За эту неделю</button>
            </form>
            <form method="POST" action="{{ route('teacher-schedule-doc-export') }}">
            @csrf
                <input type="hidden" name="lessons" value="{{ isset($data['lessons']) ? json_encode($data['lessons']) : '' }}">
                <input type="hidden" name="teacher_name" value="{{ $data['instance_name'] }}">
                <input type="hidden" name="week_data" value="{{ isset($data['week_data']) ? json_encode($data['week_data']) : '' }}">
                <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                <button type="submit" class="btn btn-primary top-right-button">В MS Word</button>
            </form>
        </div>
    </div>
    <div class="timetable-img text-center">
        <div class="table-responsive">
            <table class="table table-bordered text-center schedule-table">
                <thead>
                    <tr class="bg-light-gray">
                        <th class="text-uppercase">Пара</th>
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
                            <th class="text-uppercase">Понедельник</th>
                            <th class="text-uppercase">Вторник</th>
                            <th class="text-uppercase">Среда</th>
                            <th class="text-uppercase">Четверг</th>
                            <th class="text-uppercase">Пятница</th>
                            <th class="text-uppercase">Суббота</th>
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
                                <td class="align-middle schedule-period">
                                    <div class="schedule-period-name">{{ $class_period_id }}</div>
                                    <div class="schedule-period-time">
                                        {{ $class_period_start_time }} - {{ $class_period_end_time }}
                                    </div>
                                </td>

                                @foreach($week_day_ids as $wd_name => $week_day_id)
                                    @php
                                        $is_holiday = isset($data['week_dates']) && is_array($data['week_dates'][$week_day_id]) && isset($data['week_dates'][$week_day_id]['is_holiday']);
                                    @endphp
                                    @if(isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']])
                                        && ! $is_holiday)
                                        @php 
                                            $lesson = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']];
                                            $cell_bg_color = isset($lesson['date']) ? '#D3D3D3' : $weekly_period_color[$weekly_period_id['every_week']];
                                            $title = isset($lesson['date']) ? 'Единоразовое занятие' : 'Регулярное занятие'; 
                                        @endphp
                                        <td class="schedule-cell" style="background-color: {{ $cell_bg_color }}" title="{{ $title }}">
                                            <div class="dropdown schedule-actions-div">
                                                <a class="dropdown-toggle schedule-actions-button" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }} ({{ $lesson['type'] }})</div>
                                                    <div class="font-size13 text-light-gray schedule-room">ауд. {{ $lesson['room'] }}</div>
                                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['group'] }}</div>
                                                </a>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <li>
                                                        <form method="POST" action="{{ route('lesson-replacement') }}" target="_blank">
                                                        @csrf
                                                            <input type="hidden" name="replace_rules[lesson_id]" value="{{ $lesson['id'] }}">
                                                            <input type="hidden" name="replace_rules[teacher_id]" value="{{ $lesson['teacher_id'] }}">
                                                            <input type="hidden" name="replace_rules[class_period_id]" value="{{ $lesson['class_period_id'] }}">
                                                            @php
                                                                $w_p_id = $lesson['real_weekly_period_id'] ?? $lesson['weekly_period_id'];
                                                            @endphp
                                                            <input type="hidden" name="replace_rules[weekly_period_id]" value="{{ $w_p_id }}">
                                                            <input type="hidden" name="replace_rules[week_day_id]" value="{{ $lesson['week_day_id'] }}">
                                                            @php
                                                                $lesson_date = '';
                                                                if (isset($data['week_dates'])) {
                                                                    $lesson_date = date('Y-m-d '.$class_period_start_time, strtotime(str_replace('"', '', json_encode($data['week_dates'][$week_day_id]))));
                                                                }
                                                            @endphp
                                                            <input type="hidden" name="replace_rules[date]" value="{{ $lesson_date }}">
                                                            <input type="hidden" name="week_data" value="{{ isset($data['week_data']) ? json_encode($data['week_data']) : '' }}">
                                                            <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                                                            <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                            <button type="submit" class="btn btn-light schedule-dropdown">Варианты замены</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('lesson-rescheduling') }}" target="_blank">
                                                        @csrf
                                                            <input type="hidden" name="lesson_id" value="{{ $lesson['id'] }}">
                                                            <input type="hidden" name="teacher_id" value="{{ $lesson['teacher_id'] }}">
                                                            <input type="hidden" name="week_data" value="{{ isset($data['week_data']) ? json_encode($data['week_data']) : '' }}">
                                                            <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                                                            <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                            <button type="submit" class="btn btn-light schedule-dropdown">Варианты переноса</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    @elseif(isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']]) || isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']]))
                                        @php
                                            $lesson_red = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']] ?? false;
                                            $lesson_blue = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']] ?? false;
                                        @endphp
                                        <td class="schedule-cell">
                                            @if($lesson_red)
                                                <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                                    <a class="dropdown-toggle schedule-actions-button" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }} ({{ $lesson_red['type'] }})</div>
                                                        <div class="font-size13 text-light-gray schedule-room-half">ауд. {{ $lesson_red['room'] }}</div>
                                                        <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['group'] }}</div>
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <li>
                                                            <form method="POST" action="{{ route('lesson-replacement') }}" target="_blank">
                                                            @csrf
                                                                <input type="hidden" name="replace_rules[lesson_id]" value="{{ $lesson_red['id'] }}">
                                                                <input type="hidden" name="replace_rules[teacher_id]" value="{{ $lesson_red['teacher_id'] }}">
                                                                <input type="hidden" name="replace_rules[class_period_id]" value="{{ $lesson_red['class_period_id'] }}">
                                                                <input type="hidden" name="replace_rules[weekly_period_id]" value="{{ $lesson_red['weekly_period_id'] }}">
                                                                <input type="hidden" name="replace_rules[week_day_id]" value="{{ $lesson_red['week_day_id'] }}">
                                                                <input type="hidden" name="week_data" value="{{ isset($data['week_data']) ? json_encode($data['week_data']) : '' }}">
                                                                <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                                                                <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                                <button type="submit" class="btn btn-light schedule-dropdown">Варианты замены</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="{{ route('lesson-rescheduling') }}" target="_blank">
                                                            @csrf
                                                                <input type="hidden" name="lesson_id" value="{{ $lesson_red['id'] }}">
                                                                <input type="hidden" name="teacher_id" value="{{ $lesson_red['teacher_id'] }}">
                                                                <input type="hidden" name="week_data" value="{{ isset($data['week_data']) ? json_encode($data['week_data']) : '' }}">
                                                                <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                                                                <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                                <button type="submit" class="btn btn-light schedule-dropdown">Варианты переноса</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                            @if($lesson_blue)
                                                <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                                    <a class="dropdown-toggle schedule-actions-button" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }} ({{ $lesson_blue['type'] }})</div>
                                                        <div class="font-size13 text-light-gray schedule-room-half">ауд. {{ $lesson_blue['room'] }}</div>
                                                        <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['group'] }}</div>
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <li>
                                                            <form method="POST" action="{{ route('lesson-replacement') }}" target="_blank">
                                                            @csrf
                                                                <input type="hidden" name="replace_rules[lesson_id]" value="{{ $lesson_blue['id'] }}">
                                                                <input type="hidden" name="replace_rules[teacher_id]" value="{{ $lesson_blue['teacher_id'] }}">
                                                                <input type="hidden" name="replace_rules[class_period_id]" value="{{ $lesson_blue['class_period_id'] }}">
                                                                <input type="hidden" name="replace_rules[weekly_period_id]" value="{{ $lesson_blue['weekly_period_id'] }}">
                                                                <input type="hidden" name="replace_rules[week_day_id]" value="{{ $lesson_blue['week_day_id'] }}">
                                                                <input type="hidden" name="week_data" value="{{ isset($data['week_data']) ? json_encode($data['week_data']) : '' }}">
                                                                <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                                                                <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                                <button type="submit" class="btn btn-light schedule-dropdown">Варианты замены</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="{{ route('lesson-rescheduling') }}" target="_blank">
                                                            @csrf
                                                                <input type="hidden" name="lesson_id" value="{{ $lesson_blue['id'] }}">
                                                                <input type="hidden" name="teacher_id" value="{{ $lesson_blue['teacher_id'] }}">
                                                                <input type="hidden" name="week_data" value="{{ isset($data['week_data']) ? json_encode($data['week_data']) : '' }}">
                                                                <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                                                                <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                                <button type="submit" class="btn btn-light schedule-dropdown">Варианты переноса</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                    @else
                                        <td class="schedule-cell"></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
