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
            $week_color = __('header.blue_week_color');
            $bg_color = '#ace7f2';
            if ($data['is_red_week']) {
                $is_red_week = 1;
                $week_color = __('header.red_week_color');
                $bg_color = '#ffb3b9';
            }
        @endphp
            <h1 class="top-header">{{ str_replace(['?-1', '?-2'], [$data['week_data']['start_date'], $data['week_data']['end_date']], __('header.teacher_dated_reschedule_variants')) }} <span style="background-color: {{ $bg_color }};">{{ str_replace('?', $week_color, __('header.week_color')) }}</span></h1>   
        @else
            <h1 class="top-header">{{ __('header.teacher_regular_reschedule_variants') }}</h1>
        @endif
        <div class="replacement-schedule-header-div">
            <h3>{{ __('header.teacher') }}: {{ $data['teacher_name'] ?? ''}}</h3>
            <div class="schedule-button-group">
                <form method="POST" action="{{ route('teacher-reschedule') }}">
                @csrf
                    <input type="hidden" name="teacher_id" value="{{ $data['teacher_id'] }}">
                    <input type="hidden" name="lesson_id" value="{{ $data['rescheduling_lesson_id'] }}">
                    <input type="week" name="week_number" value="{{ $data['week_data']['week_number'] }}">
                    <button type="submit" class="btn btn-primary">{{ __('form.this_week') }}</button>
                </form>
                <form method="POST" action="{{ route('teacher-reschedule-doc-export') }}">
                @csrf
                    <input type="hidden" name="lessons" value="{{ json_encode($data['periods']) }}">
                    <input type="hidden" name="week_data" value="{{ json_encode($data['week_data']) }}">
                    <input type="hidden" name="teacher_name" value="{{ $data['teacher_name'] }}">
                    <input type="hidden" name="rescheduling_lesson_id" value="{{ $data['rescheduling_lesson_id'] }}">
                    <input type="hidden" name="prev_data" value="{{ json_encode(old()) }}">
                    <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                    <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
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
                        @if(isset($data) && isset($data['class_periods']) && isset($data['periods']))
                            @php
                                $week_day_ids = $data['week_day_ids'];
                                $weekly_period = $data['weekly_periods'];
                                $weekly_period_id = $data['weekly_period_ids'];
                                $weekly_period_color = $data['weekly_period_colors'];
                                $class_period_ids = $data['class_period_ids'];
                                $class_periods = $data['class_periods'];
                                $week_days_limit = $data['week_days_limit'];
                                $class_periods_limit = $data['class_periods_limit'];
                                $lessons = $data['periods'];
                                $other_lesson_participant_name = $data['other_lesson_participant_name'];
                            @endphp
                            @foreach($class_period_ids as $lesson_name => $class_period_id)
                                @if($class_period_id <= $class_periods_limit)
                                    <tr>
                                        @if($class_period_id <= $class_periods_limit)
                                            <td class="align-middle schedule-period">
                                                <div class="schedule-period-name">{{ $class_period_id }}</div>
                                                <div class="schedule-period-time">
                                                    {{ date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['start'])) }} - {{ date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['end'])) }}
                                                </div>
                                            </td>
                                        @endif
                                        @foreach($week_day_ids as $wd_name => $week_day_id)
                                            @if($week_day_id <= $week_days_limit)
                                                @php
                                                    $is_holiday = isset($data['week_dates']) && is_array($data['week_dates'][$week_day_id]) && isset($data['week_dates'][$week_day_id]['is_holiday']);
                                                @endphp
                                                @if (isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']])
                                                    && ! $is_holiday)
                                                    @php
                                                        $lesson = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']];
                                                        if (!is_array($lesson)) {
                                                            $lesson_subject = '';
                                                            $lesson_type = '';
                                                            $lesson_room = '';
                                                            $other_lesson_participant = '';
                                                            $color = 'PaleGreen';
                                                            $title = __('title.reschedule_variant');
                                                        } else {
                                                            $lesson_subject = __('content.'.$lesson['name']);
                                                            $lesson_type = "(".__('dictionary.'.$lesson['type']).")";
                                                            $lesson_room = __('content.room').' '.$lesson['room'];
                                                            $other_lesson_participant = $lesson[$other_lesson_participant_name];
                                                            $title = '';
                                                            $color = '';
                                                            if ($lesson['id'] == $data['rescheduling_lesson_id']) {
                                                                $color = 'Yellow';
                                                                $title = __('title.rescheduling_lesson');
                                                            }
                                                        }
                                                    @endphp
                                                    <td class="schedule-cell" style="background-color: {{ $color }}" title="{{ $title }}">
                                                        <div class="dropdown schedule-actions-div">
                                                            @if(isset($lesson['date']))
                                                                <div class="margin-10px-top font-size14 schedule-date"><span class="schedule-date-text">{{ $lesson['date'] }}</span></div>
                                                            @endif
                                                            <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson_subject }} {{ $lesson_type }}</div>
                                                            <div class="font-size13 text-light-gray schedule-room">{{ $lesson_room }}</div>
                                                            <div class="font-size13 text-light-gray schedule-group">{{ $other_lesson_participant }}</div>
                                                        </div>
                                                    </td>
                                                @elseif(isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']]) || isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']]))
                                                    @php
                                                        $lesson_red = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']] ?? false;
                                                        $lesson_blue = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']] ?? false;
                                                    @endphp
                                                    <td class="schedule-cell">
                                                        @if($lesson_red)
                                                            @php
                                                                if (!is_array($lesson_red)) {
                                                                    $lesson_subject = '';
                                                                    $lesson_type = '';
                                                                    $lesson_room = '';
                                                                    $other_lesson_participant = '';
                                                                    $color = 'PaleGreen';
                                                                    $title = __('title.reschedule_variant');
                                                                } else {
                                                                    $lesson_subject = __('content.'.$lesson_red['name']);
                                                                    $lesson_type = "(".__('dictionary.'.$lesson_red['type']).")";
                                                                    $lesson_room = __('content.room').' '.$lesson_red['room'];
                                                                    $other_lesson_participant = $lesson_red[$other_lesson_participant_name];
                                                                    $title = "";
                                                                    $color = '';
                                                                    if ($lesson_red['id'] == $data['rescheduling_lesson_id']) {
                                                                        $color = 'Yellow';
                                                                        $title =  __('title.rescheduling_lesson');
                                                                    }
                                                                }
                                                            @endphp
                                                            <div class="schedule-cell-top" style="background-color: {{ $color }}" title="{{ $title }}">
                                                                @if(isset($lesson_red['date']))
                                                                    <div class="margin-10px-top font-size14 schedule-date"><span class="schedule-date-text">{{ $lesson_red['date'] }}</span></div>
                                                                @endif
                                                                <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_subject }} {{ $lesson_type }}</div>
                                                                <div class="font-size13 text-light-gray schedule-room-half">{{ $lesson_room }}</div>
                                                                <div class="font-size13 text-light-gray schedule-group-half">{{ $other_lesson_participant }}</div>
                                                            </div>
                                                        @endif
                                                        @if($lesson_blue)
                                                            @php
                                                                if (!is_array($lesson_blue)) {
                                                                    $lesson_subject = '';
                                                                    $lesson_type = '';
                                                                    $lesson_room = '';
                                                                    $other_lesson_participant = '';
                                                                    $color = 'PaleGreen';
                                                                    $title = __('title.reschedule_variant');
                                                                } else {
                                                                    $lesson_subject = __('content.'.$lesson_blue['name']);
                                                                    $lesson_type = "(".__('dictionary.'.$lesson_blue['type']).")";
                                                                    $lesson_room = __('content.room').' '.$lesson_blue['room'];
                                                                    $other_lesson_participant = $lesson_blue[$other_lesson_participant_name];
                                                                    $color = '';
                                                                    $title = "";
                                                                    if ($lesson_blue['id'] == $data['rescheduling_lesson_id']) {
                                                                        $color = 'Yellow';
                                                                        $title = __('title.rescheduling_lesson');
                                                                    }
                                                                }
                                                            @endphp
                                                            <div class="schedule-cell-bottom" style="background-color: {{ $color }}" title="{{ $title }}">
                                                                @if(isset($lesson_blue['date']))
                                                                    <div class="margin-10px-top font-size14 schedule-date"><span class="schedule-date-text">{{ $lesson_blue['date'] }}</span></div>
                                                                @endif
                                                                <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_subject }} {{ $lesson_type }}</div>
                                                                <div class="font-size13 text-light-gray schedule-room-half">{{ $lesson_room }}</div>
                                                                <div class="font-size13 text-light-gray schedule-group-half">{{ $other_lesson_participant }}</div>
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
    </div>
@endsection
