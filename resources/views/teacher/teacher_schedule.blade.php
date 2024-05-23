@extends('layouts.app')
@section('content')
    <div class="container">
    @if (\Session::has('response'))
        @if(\Session::get('response')['success'])
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ \Session::get('response')['message'] }}
            </div>
        @else
            <div class="alertFail">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ \Session::get('response')['message'] }}
            </div>
        @endif
    @endif
    @if($errors->any())
        <div class="alertFail">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ __('user_validation.invalid_input_data') }}
        </div>
    @endif
    @if(isset($data['week_data']) && isset($data['is_red_week']))
        @php
            $is_red_week = 0;
            $week_color = __('header.blue_week_color');
            $bg_color = '#ace7f2';
            if ($data['is_red_week']) {
                $is_red_week = 1;
                $week_color = __('header.red_week_color');
                $bg_color = '#ffb3b9';
            }
        @endphp
        @if ($data['week_data']['current_study_season'] === $data['study_seasons']['studies'])
            <h1 class="top-header">{{ str_replace(['?-1', '?-2'], [$data['week_data']['start_date'], $data['week_data']['end_date']], __('header.teacher_dated_schedule')) }} <span style="background-color: {{ $bg_color }};">{{ str_replace('?', $week_color, __('header.week_color')) }}</span></h1>   
        @elseif($data['week_data']['current_study_season'] === $data['study_seasons']['session'])
            <h1 class="top-header">{{ __('header.session') }} {{ __('header.'.$data['required_study_period']->season) }} {{ $data['required_study_period']->year }} {{ __('header.of_year') }}</h1>
        @else
            <h1 class="top-header">{{ __('header.vacation') }}</h1>
        @endif        
    @else
        <h1 class="top-header">{{ __('header.teacher_regular_schedule') }} {{ __('header.'.$data['required_study_period']->season) }} {{ $data['required_study_period']->year }} {{ __('header.of_year') }}</h1>
    @endif
    <div class="replacement-schedule-header-div">
        <h3>{{ __('header.teacher') }}: {{ $data['instance_name'] ?? ''}}</h3>
        <div class="schedule-button-group">
            <form class="schedule-form" method="POST" action="{{ route('teacher-schedule', ['schedule_teacher_id' => $data['schedule_instance_id']]) }}" target="_blank">
            @csrf
                <select name="study_period_id" id="study-period-id">
                    <option selected value="">-------- ----</option>
                    @foreach($data['study_periods'] as $study_period)
                        @if ($study_period->id === $data['required_study_period']->id)
                            @if(isset($data['week_data']['week_number']))
                                <option value="{{ $study_period->id }}">{{ __('form.'.$study_period->season) }} {{ $study_period->year }}</option>    
                            @else
                                <option selected value="{{ $study_period->id }}">{{ __('form.'.$study_period->season) }} {{ $study_period->year }}</option>
                            @endif
                        @else
                            <option value="{{ $study_period->id }}">{{ __('form.'.$study_period->season) }} {{ $study_period->year }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">{{ __('form.this_study_period') }}</button>
            </form>
            <form class="schedule-form" method="POST" action="{{ route('teacher-month-schedule') }}" target="_blank">
            @csrf
                <input type="month" name="month_number" value="">
                <input type="hidden" name="schedule_teacher_id" value="{{ $data['schedule_instance_id'] }}">
                <button type="submit" class="btn btn-success month-schedule-button">{{ __('form.this_month') }}</button>
            </form>
            <form class="schedule-form" method="POST" action="{{ route('teacher-schedule', ['schedule_teacher_id' => $data['schedule_instance_id']]) }}" target="_blank">
            @csrf
                <input type="week" name="week_number" value="{{ $data['week_data']['week_number'] }}">
                <button type="submit" class="btn btn-primary">{{ __('form.this_week') }}</button>
            </form>
            <form method="POST" action="{{ route('teacher-schedule-doc-export') }}">
            @csrf
                <input type="hidden" name="lessons" value="{{ isset($data['lessons']) ? json_encode($data['lessons']) : '' }}">
                <input type="hidden" name="teacher_name" value="{{ $data['instance_name'] }}">
                <input type="hidden" name="week_data" value="{{ isset($data['week_data']) ? json_encode($data['week_data']) : '' }}">
                <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                <input type="hidden" name="study_period_id" value="{{ $data['study_period_id'] ?? '' }}">
                <button type="submit" class="btn btn-primary top-right-button">{{ __('form.ms_word') }}</button>
            </form>
        </div>
    </div>
    <div class="timetable-img text-center">
        <div class="table-responsive">
            <table class="table table-bordered text-center schedule-table">
                <thead>
                    <tr class="bg-light-gray">
                        <th class="text-uppercase">{{ __('header.period') }}</th>
                        @php
                            $week_days = config('enum.week_days');
                        @endphp
                        @if(isset($data['week_dates']))
                            @foreach($data['week_dates'] as $week_day_id => $date)
                                @if($week_day_id <= $data['week_days_limit'])
                                    @if(is_array($date) && isset($date['is_holiday']))
                                        <th class="text-uppercase holiday-header" title="{{ __('title.holiday') }}">{{ __('week_day.'.$week_days[$week_day_id]) }} ({{ date('d.m.y', strtotime($date['date'])) }})</th>
                                    @else
                                        <th class="text-uppercase">{{ __('week_day.'.$week_days[$week_day_id]) }} ({{ date('d.m.y', strtotime($date)) }})</th>
                                    @endif
                                @endif
                            @endforeach
                        @else
                            @foreach($week_days as $week_day_id => $week_day_name)
                                @if($week_day_id <= $data['week_days_limit'])
                                    <th class="text-uppercase">{{ __('week_day.'.$week_day_name) }}</th>
                                @endif
                            @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if(isset($data) && isset($data['class_periods']) && isset($data['lessons']))
                        @php
                            $week_day_ids = $data['week_day_ids'];
                            $weekly_period = $data['weekly_periods'];
                            $weekly_period_id = $data['weekly_period_ids'];
                            $weekly_period_color = $data['weekly_period_colors'];
                            $class_period_ids = $data['class_period_ids'];
                            $week_days_limit = $data['week_days_limit'];
                            $class_periods_limit = $data['class_periods_limit'];
                            $class_periods = $data['class_periods'];
                            $lessons = $data['lessons'];
                        @endphp
                        @foreach($class_period_ids as $lesson_name => $class_period_id)
                            @if($class_period_id <= $class_periods_limit)
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
                                        @if($week_day_id <= $week_days_limit)
                                            @if(isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']])
                                                && ! $is_holiday)
                                                @php 
                                                    $lesson = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']];
                                                    $cell_bg_color = isset($lesson['date']) ? '#D3D3D3' : $weekly_period_color[$weekly_period_id['every_week']];
                                                    $title = isset($lesson['date']) ? __('title.one_time_lesson') : __('title.regular_lesson'); 
                                                @endphp
                                                <td class="schedule-cell" style="background-color: {{ $cell_bg_color }}" title="{{ $title }}">
                                                    <div class="dropdown schedule-actions-div">
                                                        <a class="dropdown-toggle schedule-actions-button" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <div class="margin-10px-top font-size14 schedule-subject">{{ __('content.'.$lesson['name']) }} ({{ __('dictionary.'.$lesson['type']) }})</div>
                                                            <div class="font-size13 text-light-gray schedule-room">{{ __('content.room') }} {{ $lesson['room'] }}</div>
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
                                                                    <button type="submit" class="btn btn-light schedule-dropdown">{{ __('form.replacement_variants') }}</button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form method="POST" action="{{ route('lesson-rescheduling') }}" target="_blank">
                                                                @csrf
                                                                    <input type="hidden" name="lesson_id" value="{{ $lesson['id'] }}">
                                                                    <input type="hidden" name="teacher_id" value="{{ $lesson['teacher_id'] }}">
                                                                    <input type="hidden" name="week_data" value="{{ isset($data['week_data']) ? json_encode($data['week_data']) : '' }}">
                                                                    <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                                                                    <input type="hidden" name="week_number" value="{{ $data['week_data']['week_number'] }}">
                                                                    <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                                    <button type="submit" class="btn btn-light schedule-dropdown">{{ __('form.reschedule_variants') }}</button>
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
                                                                <div class="margin-10px-top font-size14 schedule-subject-half">{{ __('content.'.$lesson_red['name']) }} ({{ __('dictionary.'.$lesson_red['type']) }})</div>
                                                                <div class="font-size13 text-light-gray schedule-room-half">{{ __('content.room') }} {{ $lesson_red['room'] }}</div>
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
                                                                        <button type="submit" class="btn btn-light schedule-dropdown">{{ __('form.replacement_variants') }}</button>
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
                                                                        <button type="submit" class="btn btn-light schedule-dropdown">{{ __('form.reschedule_variants') }}</button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    @if($lesson_blue)
                                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                                            <a class="dropdown-toggle schedule-actions-button" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <div class="margin-10px-top font-size14 schedule-subject-half">{{ __('content.'.$lesson_blue['name']) }} ({{ __('dictionary.'.$lesson_blue['type']) }})</div>
                                                                <div class="font-size13 text-light-gray schedule-room-half">{{ __('content.room') }} {{ $lesson_blue['room'] }}</div>
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
                                                                        <button type="submit" class="btn btn-light schedule-dropdown">{{ __('form.replacement_variants') }}</button>
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
                                                                        <button type="submit" class="btn btn-light schedule-dropdown">{{ __('form.reschedule_variants') }}</button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </td>
                                            @else
                                                <td class="schedule-cell"></td>
                                            @endif
                                        @endif
                                    @endforeach
                                </tr>
                            @endif    
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
