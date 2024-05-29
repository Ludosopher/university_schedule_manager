@extends('layouts.app')
@section('content')
    <div class="container">
        @includeIf('parts.notices.errors_various')
        @includeIf('parts.headers.reschedule')
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
        @includeIf('parts.headers.reschedule_bottom')
    </div>
@endsection
