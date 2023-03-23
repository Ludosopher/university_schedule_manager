@extends('layouts.app')
@section('content')
    <div class="container">
    @if($errors->any())
        <div class="alertFail">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ __('user_validation.invalid_input_data') }}
        </div>
    @endif
    <h1 class="top-header">{{ str_replace('?', $data['month_name'], __('header.group_schedule_on')) }}</h1>
    <div class="replacement-schedule-header-div">
        <h3>{{ __('header.group') }}: {{ $data['instance_name'] ?? ''}}</h3>
        <div class="schedule-button-group">
            <form method="POST" action="{{ route('group-month-schedule-doc-export') }}">
            @csrf
                <input type="hidden" name="month_name" value="{{ $data['month_name'] }}">
                <input type="hidden" name="group_name" value="{{ $data['instance_name'] }}">
                <input type="hidden" name="weeks" value="{{ json_encode($data['weeks']) }}">
                <input type="hidden" name="prev_data" value="{{ json_encode(old()) }}">
                <button type="submit" class="btn btn-primary top-right-button">{{ __('form.ms_word') }}</button>
            </form>
        </div>
    </div>
    @php
        $week_day_ids = config('enum.week_day_ids');
        $weekly_period = config('enum.weekly_periods');
        $weekly_period_id = config('enum.weekly_period_ids');
        $weekly_period_color = config('enum.weekly_period_colors');
        $class_period_ids = config('enum.class_period_ids');
        $class_periods = $data['class_periods'];
    @endphp
    @foreach($data['weeks'] as $week_number => $week_content)
        @php
            $is_red_week = 0;
            $bg_color = 'rgb(243,243,255)';
            if ($week_content['is_red_week']) {
                $is_red_week = 1;
                $bg_color = 'rgb(255,243,243)';
            }
        @endphp
        <div class="timetable-img text-center">
            <div class="table-responsive">
                <table class="table table-bordered text-center" style="background-color: {{ $bg_color }}">
                    <thead>
                        <tr class="bg-light-gray">
                            <th class="text-uppercase month-schedule-header-th">{{ __('header.period') }}</th>
                            @php
                                $week_days = config('enum.week_days');
                            @endphp
                            @if(isset($week_content['week_dates']))
                                @foreach($week_content['week_dates'] as $week_day_id => $date)
                                    @if($week_day_id <= $data['week_days_limit'])
                                        @if(is_array($date) && isset($date['is_holiday']))
                                            <th class="text-uppercase" style="color: red;" title="{{ __('title.holiday') }}">{{ __('week_day.'.$week_days[$week_day_id]) }} ({{ date('d.m.y', strtotime($date['date'])) }})</th>
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
                        @if(isset($data['class_periods']) && isset($week_content['lessons']))
                            @php $lessons = $week_content['lessons']; @endphp
                            @foreach($class_period_ids as $lesson_name => $class_period_id)
                                @if($class_period_id <= $class_periods_limit)
                                    <tr>
                                        <td class="align-middle month-schedule-period">
                                            <div class="month-schedule-period-name">{{ $class_period_id }}</div>
                                            <div class="month-schedule-period-time">
                                                {{ date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['start'])) }} - {{ date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['end'])) }}
                                            </div>
                                        </td>

                                        @foreach($week_day_ids as $wd_name => $week_day_id)
                                            @php
                                                $is_holiday = isset($week_content['week_dates']) && is_array($week_content['week_dates'][$week_day_id]) && isset($week_content['week_dates'][$week_day_id]['is_holiday']);
                                            @endphp
                                            @if($week_day_id <= $week_days_limit)
                                                @if (isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']])
                                                    && ! $is_holiday)
                                                    @php 
                                                        $lesson = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']];
                                                        $cell_bg_color = isset($lesson['date']) ? '#D3D3D3' : $weekly_period_color[$weekly_period_id['every_week']];
                                                        $title = isset($lesson['date']) ? __('title.one_time_lesson') : __('title.regular_lesson'); 
                                                    @endphp
                                                    <td class="month-schedule-cell" style="background-color: {{ $cell_bg_color }}" title="{{ $title }}">
                                                        <div class="dropdown month-schedule-actions-div">
                                                            <a class="dropdown-toggle schedule-actions-button" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <div class="margin-10px-top font-size14 month-schedule-subject">{{ __('content.'.$lesson['name']) }} ({{ __('dictionary.'.$lesson['type']) }})</div>
                                                                <div class="font-size13 text-light-gray month-schedule-room">{{ __('content.room') }} {{ $lesson['room'] }}</div>
                                                                <div class="font-size13 text-light-gray month-schedule-group">{{ $lesson['teacher'] }}</div>
                                                            </a>
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                                <li>
                                                                    <form method="POST" action="{{ route('lesson-replacement') }}" target="_blank">
                                                                    @csrf
                                                                        <input type="hidden" name="replace_rules[lesson_id]" value="{{ $lesson['id'] }}">
                                                                        <input type="hidden" name="replace_rules[teacher_id]" value="{{ $lesson['teacher_id'] }}">
                                                                        <input type="hidden" name="replace_rules[class_period_id]" value="{{ $lesson['class_period_id'] }}">
                                                                        <input type="hidden" name="replace_rules[weekly_period_id]" value="{{ $lesson['weekly_period_id'] }}">
                                                                        <input type="hidden" name="replace_rules[week_day_id]" value="{{ $lesson['week_day_id'] }}">
                                                                        <input type="hidden" name="week_data" value="{{ isset($week_content['week_data']) ? json_encode($week_content['week_data']) : '' }}">
                                                                        <input type="hidden" name="week_dates" value="{{ isset($week_content['week_dates']) ? json_encode($week_content['week_dates']) : '' }}">
                                                                        <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                                        <button type="submit" class="btn btn-light schedule-dropdown">{{ __('menu.replacement_variants') }}</button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form method="POST" action="{{ route('lesson-rescheduling') }}" target="_blank">
                                                                    @csrf
                                                                        <input type="hidden" name="lesson_id" value="{{ $lesson['id'] }}">
                                                                        <input type="hidden" name="teacher_id" value="{{ $lesson['teacher_id'] }}">
                                                                        <input type="hidden" name="week_data" value="{{ isset($week_content['week_data']) ? json_encode($week_content['week_data']) : '' }}">
                                                                        <input type="hidden" name="week_dates" value="{{ isset($week_content['week_dates']) ? json_encode($week_content['week_dates']) : '' }}">
                                                                        <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                                        <button type="submit" class="btn btn-light schedule-dropdown">{{ __('menu.reschedule_variants') }}</button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="month-schedule-cell"></td>
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
    @endforeach
    
@endsection
