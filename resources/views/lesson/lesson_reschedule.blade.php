@extends('layouts.app')
@section('content')
    <div class="container">
        @if($errors->any())
            <div class="alertFail">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ __('user_validation.invalid_input_data') }}
            </div>
        @endif
    <div>
        <div>
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
                <h1 class="top-header">{{ str_replace(['?-1', '?-2'], [$data['week_data']['start_date'], $data['week_data']['end_date']], __('header.dated_reschedule_variants')) }}<span style="background-color: {{ $bg_color }};">{{ str_replace('?', $week_color, __('header.week_color')) }}</span></h1>   
            @else
                <h1 class="top-header">{{ __('header.regular_reschedule_variants') }}</h1>
            @endif
            <h4>{{ __('header.rescheduling_lesson') }}: {{ __('dictionary.'.$data['lesson_name']) }} - {{ __('dictionary.'.$data['lesson_week_day']) }} - {{ __('dictionary.'.$data['lesson_weekly_period']) }} - {{ __('dictionary.'.$data['lesson_class_period']) }} {{ __('header.class_period') }}</h4>
            <h4>{{ __('header.teacher') }}: {{ $data['teacher_name'] ?? ''}}</h4>
        </div>
        <div class="replacement-schedule-header-div">
            <h4>{{ __('header.group(s)') }}: {{ $data['groups_name'] ?? ''}}</h4>
            <div class="schedule-button-group">
                <form method="POST" action="{{ route('lesson-rescheduling') }}" class="top-right-button">
                @csrf
                    <input type="hidden" name="teacher_id" value="{{ $data['teacher_id'] }}">
                    <input type="hidden" name="lesson_id" value="{{ $data['lesson_id'] }}">
                    @php
                        $week_number = isset($data['week_data']['week_number']) ? $data['week_data']['week_number'] : (isset($data['week_number']) ? $data['week_number'] : '');
                    @endphp
                    <input type="week" name="week_number" value="{{ $week_number }}" min="{{ $data['current_study_period_border_weeks']['start'] }}" max="{{ $data['current_study_period_border_weeks']['end'] }}">
                    <button type="submit" class="btn btn-primary">{{ __('form.this_week') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="timetable-img text-center">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr class="bg-light-gray">
                        <th class="text-uppercase">{{ __('header.period') }}</th>
                        @php
                            $week_days = config('enum.week_days');
                        @endphp
                        @if(isset($data['week_dates']))
                            @foreach($data['week_dates'] as $week_day_id => $date)
                                @if(is_array($date) && isset($date['is_holiday']))
                                    <th class="text-uppercase holiday-header" title="{{ __('title.holiday') }}">{{ __('week_day.'.$week_days[$week_day_id]) }} ({{ date('d.m.y', strtotime($date['date'])) }})</th>
                                @else
                                    <th class="text-uppercase">{{ __('week_day.'.$week_days[$week_day_id]) }} ({{ date('d.m.y', strtotime($date)) }})</th>
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
                    @if(isset($data) && isset($data['class_periods']) && isset($data['free_periods']))
                        @php
                            $week_day_ids = $data['week_day_ids'];
                            $weekly_period = $data['weekly_period'];
                            $weekly_period_id = $data['weekly_period_id'];
                            $weekly_period_color = $data['weekly_period_color'];
                            $class_period_ids = $data['class_period_ids'];
                            $week_days_limit = $data['week_days_limit'];
                            $class_periods_limit = $data['class_periods_limit'];
                            $free_weekly_period_color = $data['free_weekly_period_colors'];
                            $class_periods = $data['class_periods'];
                            $free_periods = $data['free_periods'];
                        @endphp
                        @foreach($class_period_ids as $lesson_name => $class_period_id)
                            @if($class_period_id <= $class_periods_limit)
                                <tr>
                                    <td class="align-middle schedule-period">
                                        <div class="schedule-period-name">{{ $class_period_id }}</div>
                                        <div class="schedule-period-time">
                                            {{ date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['start'])) }} - {{ date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['end'])) }}
                                        </div>
                                    </td>
                                    @foreach($week_day_ids as $wd_name => $week_day_id)
                                        @php
                                            $is_holiday = isset($data['week_dates']) && is_array($data['week_dates'][$week_day_id]) && isset($data['week_dates'][$week_day_id]['is_holiday']);
                                        @endphp
                                        @if($week_day_id <= $week_days_limit)
                                            @if (isset($free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']])
                                                && ! $is_holiday)
                                                @php $free_period = $free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']]; @endphp
                                                <td class="schedule-cell reschedule-cell" style="background-color: {{ $free_weekly_period_color[$weekly_period_id['every_week']] }};"></td>
                                            @elseif(isset($free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']]) || isset($free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']]))
                                                @php
                                                    $free_period_red = $free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']] ?? false;
                                                    $free_period_blue = $free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']] ?? false;
                                                @endphp
                                                <td class="schedule-cell reschedule-cell">
                                                    @if($free_period_red)
                                                        <div class="schedule-cell-top" style="background-color: {{ $free_weekly_period_color[$weekly_period_id['red_week']] }}"></div>
                                                    @endif
                                                    @if($free_period_blue)
                                                        <div class="schedule-cell-bottom" style="background-color: {{ $free_weekly_period_color[$weekly_period_id['blue_week']] }}"></div>
                                                    @endif
                                                </td>
                                            @else
                                                <td class="schedule-cell reschedule-cell"></td>
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
    <div class="replacement-schedule-header-div">
            <h5>{{ __('header.view_in_schedule') }}:</h5>
            <div class="schedule-button-group">
                <form method="POST" action="{{ route('teacher-reschedule') }}" target="_blank">
                @csrf
                    <input type="hidden" name="lesson_id" value="{{ $data['lesson_id'] }}">
                    <input type="hidden" name="teacher_id" value="{{ $data['teacher_id'] }}">
                    <input type="hidden" name="week_number" value="{{ $data['week_data']['week_number'] }}">
                    <input type="hidden" name="prev_data" value="{{ json_encode(old()) }}">
                    <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                    <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                    <button type="submit" class="btn btn-light schedule-dropdown">{{ __("form.teacher's") }}</button>
                </form>
                @if (isset($data['groups_ids_names']) && is_array($data['groups_ids_names']))
                    @foreach ($data['groups_ids_names'] as $group)
                        <form method="POST" action="{{ route('group-reschedule') }}" target="_blank">
                        @csrf
                            <input type="hidden" name="lesson_id" value="{{ $data['lesson_id'] }}">
                            <input type="hidden" name="teacher_id" value="{{ $data['teacher_id'] }}">
                            <input type="hidden" name="group_id" value="{{ $group['id']  }}">
                            <input type="hidden" name="week_number" value="{{ $data['week_data']['week_number'] }}">
                            <input type="hidden" name="prev_data" value="{{ json_encode(old()) }}">
                            <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                            <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                            <button type="submit" class="btn btn-light schedule-dropdown">{{ $group['name'] }}</button>
                        </form>
                    @endforeach
                @endif
            </div>
        </div>
@endsection
