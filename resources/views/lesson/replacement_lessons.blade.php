@extends('layouts.app')
@section('content')
    @php
        $min_replacement_period = config('site.min_replacement_period');
        $class_period_ids = config('enum.class_period_ids');
        $class_periods = $data['normalize_class_periods'];    
    @endphp
    <div class="container">
        @includeIf('parts.notices.errors_replacement_lessons')
        <div class="getAllContainer" class="top-header">
            <div class="getAllLeft">
                @includeIf('parts.forms.find_replacement_lessons')
            </div>
            <div class="getAllRight">
                @includeIf('parts.headers.replacement')
                <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            @foreach($data['table_properties'] as $property)
                                <th class="th-sm text-center align-top">{{ __('table_header.'.$property['header']) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['replacement_lessons'] as $key => $lesson)
                            @php
                                $wd_id = $lesson['week_day_id']['id'];
                                $is_holiday = isset($data['week_dates']) && is_array($data['week_dates'][$wd_id]) && isset($data['week_dates'][$wd_id]['is_holiday']);
                            @endphp
                            @if (
                                    ! $is_holiday
                                    &&
                                    (
                                        (isset($lesson['replacing_hours_diff']) && $lesson['replacing_hours_diff'] > $min_replacement_period)
                                        ||
                                        (! isset($lesson['replacing_hours_diff']))
                                    ) 
                                )
                                <tr>
                                    @foreach($data['table_properties'] as $property)
                                        @php 
                                            $field = $property['field'];
                                            $localized_value = is_array($lesson[$field]) ? (\Lang::has('dictionary.'.$lesson[$field]['name']) ? __('dictionary.'.$lesson[$field]['name']) 
                                                                                                                                              : $lesson[$field]['name']) 
                                                                                         : (\Lang::has('dictionary.'.$lesson[$field]) ? __('dictionary.'.$lesson[$field]) 
                                                                                                                                      : $lesson[$field]);
                                        @endphp
                                        @if($field == 'profession_level_name')
                                            <td class="regular-cell"><a href="{{ route('teacher-schedule', ['schedule_teacher_id' => $lesson['teacher_id']]) }}">{{ $localized_value }}</a></td>
                                        @elseif($field == 'week_day_id')
                                            @php
                                                $lesson_date = "";
                                                if (isset($lesson['date'])) {
                                                    $lesson_date = ' ('.$lesson['date'].')';
                                                }
                                            @endphp
                                            <td class="regular-cell">{{ $localized_value }}{{ $lesson_date }}</td>
                                        @else
                                            <td class="regular-cell">{{ $localized_value }}</td>    
                                        @endif
                                    @endforeach
                                </tr>    
                            @endif
                        @endforeach
                    </tbody>
                </table>
                @includeIf('parts.headers.replacement_bottom')
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
                                @if(isset($data) && isset($data['class_periods']) && isset($data['in_schedule']))
                                    @php
                                        $week_day_ids = $data['week_day_ids'];
                                        $weekly_period = $data['weekly_periods'];
                                        $weekly_period_id = $data['weekly_period_ids'];
                                        $weekly_period_color = $data['weekly_period_colors'];
                                        $class_period_ids = $data['class_period_ids'];
                                        $class_periods = $data['class_periods'];
                                        $week_days_limit = $data['week_days_limit'];
                                        $class_periods_limit = $data['class_periods_limit'];
                                        $lessons = $data['in_schedule'];
                                    @endphp
                                    @foreach($class_period_ids as $lesson_name => $class_period_id)
                                        @if($class_period_id <= $class_periods_limit)
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
                                                        @if($week_day_id <= $week_days_limit)
                                                            @php
                                                                $is_holiday = isset($data['week_dates']) && is_array($data['week_dates'][$week_day_id]) && isset($data['week_dates'][$week_day_id]['is_holiday']);
                                                            @endphp
                                                            @if (isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']])
                                                                && ! $is_holiday)
                                                                @php 
                                                                    $lesson = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']];
                                                                    $other_lesson_participant = 'group';
                                                                    $is_replacement_link = false;
                                                                    $color = '';
                                                                    $title = '';
                                                                    if (isset($lesson['for_replacement']) && $lesson['for_replacement']) 
                                                                    {
                                                                        $color = 'RGB(200, 255, 200)';
                                                                        $title = __('title.overdue_replacement_variant');
                                                                        $other_lesson_participant = 'teacher';
                                                                        if (
                                                                            (isset($lesson['replacing_hours_diff']) && $lesson['replacing_hours_diff'] > $min_replacement_period)
                                                                            ||
                                                                            (! isset($lesson['replacing_hours_diff'])) 
                                                                        )
                                                                        {
                                                                            $color = 'PaleGreen';
                                                                            $title = __('title.replacement_variant');
                                                                            if (in_array($data['prev_replace_rules']['teacher_id'], $data['user_teachers_ids'])) {
                                                                                $is_replacement_link = true;
                                                                            }
                                                                        }   
                                                                    } elseif ($lesson['id'] == $data['prev_replace_rules']['lesson_id'] 
                                                                            && (
                                                                                ! isset($lessons['week_dates'][$week_day_id]) 
                                                                                || ! isset($data['prev_replace_rules']['date'])
                                                                                || (isset($lessons['week_dates'][$week_day_id]) && isset($data['prev_replace_rules']['date']) 
                                                                                    && date('Y-m-d', strtotime($lessons['week_dates'][$week_day_id])) == date('Y-m-d', strtotime($data['prev_replace_rules']['date']))
                                                                                    )
                                                                                )
                                                                            ) 
                                                                    {
                                                                        $color = 'Yellow';
                                                                        $title = __('title.replaceable_lesson');
                                                                        if (isset($data['replaceable_hours_diff']) && $data['replaceable_hours_diff'] <= $min_replacement_period)
                                                                        {
                                                                            $color = 'RGB(255, 255, 200)';
                                                                            $title = __('title.overdue_replaceable_lesson');
                                                                        }
                                                                        
                                                                    } 
                                                                @endphp
                                                                <td class="schedule-cell" style="background-color: {{ $color }}" title="{{ $title }}">
                                                                    <div class="dropdown schedule-actions-div">
                                                                        @if(isset($lesson['date']))
                                                                            <div class="margin-10px-top font-size14 schedule-date"><span class="schedule-date-text">{{ $lesson['date'] }}</span></div>
                                                                        @endif
                                                                        @if ($is_replacement_link)
                                                                            <form method="POST" action="{{ route('replacement-request-add') }}" title="{{ __('title.ask_for_replacement') }}" target="_blank">
                                                                            @csrf
                                                                                <input type="hidden" name="replaceable_lesson_id" value="{{ $data['prev_replace_rules']['lesson_id'] }}">
                                                                                <input type="hidden" name="replaceable_date" value="{{ $data['replaceable_date_time'] }}">
                                                                                <input type="hidden" name="replacing_lesson_id" value="{{ $lesson['id'] }}">
                                                                                <input type="hidden" name="replacing_date" value="{{ $lesson['replacing_date_time'] }}">
                                                                                <input type="hidden" name="is_regular" value="{{ ! count($lessons['week_dates']) ? 1 : 0 }}">
                                                                                <input type="hidden" name="initiator_id" value="{{ $data['initiator_id'] }}">
                                                                                <input type="hidden" name="prev_replace_rules" value="{{ json_encode($data['prev_replace_rules']) }}">
                                                                                <input type="hidden" name="week_data" value="{{ json_encode($data['week_data']) }}">
                                                                                <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                                                                                <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                                                <button type="submit" class="schedule-replace-link">
                                                                                    <div class="margin-10px-top font-size14 schedule-subject">{{ __('content.'.$lesson['name']) }} ({{ __('dictionary.'.$lesson['type']) }})</div>
                                                                                    <div class="font-size13 text-light-gray schedule-room">{{ __('content.room') }} {{ $lesson['room'] }}</div>
                                                                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson[$other_lesson_participant] }}</div>
                                                                                </button>
                                                                            </form>    
                                                                        @else
                                                                            <div class="margin-10px-top font-size14 schedule-subject">{{ __('content.'.$lesson['name']) }} ({{ __('dictionary.'.$lesson['type']) }})</div>
                                                                            <div class="font-size13 text-light-gray schedule-room">{{ __('content.room') }} {{ $lesson['room'] }}</div>
                                                                            <div class="font-size13 text-light-gray schedule-group">{{ $lesson[$other_lesson_participant] }}</div>
                                                                        @endif
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
                                                                            $other_lesson_participant = 'group';
                                                                            $color = '';
                                                                            $title = '';
                                                                            $is_red_replacement_link = false;
                                                                            if (isset($lesson_red['for_replacement']) && $lesson_red['for_replacement']
                                                                                && (
                                                                                    (isset($data['replaceable_hours_diff']) && $data['replaceable_hours_diff'] > $min_replacement_period)
                                                                                    || 
                                                                                    (! isset($data['replaceable_hours_diff']))
                                                                                )
                                                                            ) 
                                                                            {
                                                                                $color = 'PaleGreen';
                                                                                $title = __('title.replacement_variant');
                                                                                $other_lesson_participant = 'teacher';
                                                                                if (in_array($data['prev_replace_rules']['teacher_id'], $data['user_teachers_ids'])) {
                                                                                    $is_red_replacement_link = true;
                                                                                }
                                                                            } elseif ($lesson_red['id'] == $data['prev_replace_rules']['lesson_id']) {
                                                                                $color = 'Yellow';
                                                                                $title = __('title.replaceable_lesson');
                                                                            }
                                                                        @endphp
                                                                        <div class="schedule-cell-top" style="background-color: {{ $color }}" title="{{ $title }}">
                                                                            @if(isset($lesson_red['date']))
                                                                                <div class="margin-10px-top font-size14 schedule-date"><span class="schedule-date-text">{{ $lesson_red['date'] }}</span></div>
                                                                            @endif
                                                                            @if ($is_red_replacement_link)
                                                                                <form method="POST" action="{{ route('replacement-request-add') }}" title="{{ __('title.ask_for_replacement') }}" target="_blank">
                                                                                @csrf
                                                                                    <input type="hidden" name="replaceable_lesson_id" value="{{ $data['prev_replace_rules']['lesson_id'] }}">
                                                                                    <input type="hidden" name="replaceable_date" value="{{ $data['replaceable_date_time'] }}">
                                                                                    <input type="hidden" name="replacing_lesson_id" value="{{ $lesson_red['id'] }}">
                                                                                    <input type="hidden" name="replacing_date" value="{{ $lesson_red['replacing_date_time'] }}">
                                                                                    <input type="hidden" name="is_regular" value="{{ ! count($lessons['week_dates']) ? 1 : 0 }}">
                                                                                    <input type="hidden" name="initiator_id" value="{{ $data['initiator_id'] }}">
                                                                                    <input type="hidden" name="prev_replace_rules" value="{{ json_encode($data['prev_replace_rules']) }}">
                                                                                    <input type="hidden" name="week_data" value="{{ json_encode($data['week_data']) }}">
                                                                                    <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                                                                                    <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                                                    <button type="submit" class="schedule-replace-link">
                                                                                        <div class="margin-10px-top font-size14 schedule-subject-half">{{ __('content.'.$lesson_red['name']) }} ({{ __('dictionary.'.$lesson_red['type']) }})</div>
                                                                                        <div class="font-size13 text-light-gray schedule-room-half">{{ __('content.room') }} {{ $lesson_red['room'] }}</div>
                                                                                        <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red[$other_lesson_participant] }}</div>
                                                                                    </button>
                                                                                </form>    
                                                                            @else
                                                                                <div class="margin-10px-top font-size14 schedule-subject-half">{{ __('content.'.$lesson_red['name']) }} ({{ __('dictionary.'.$lesson_red['type']) }})</div>
                                                                                <div class="font-size13 text-light-gray schedule-room-half">{{ __('content.room') }} {{ $lesson_red['room'] }}</div>
                                                                                <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red[$other_lesson_participant] }}</div>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                    @if($lesson_blue)
                                                                        @php 
                                                                            $other_lesson_participant = 'group';
                                                                            $color = '';
                                                                            $title = '';
                                                                            $is_blue_replacement_link = false;
                                                                            if (isset($lesson_blue['for_replacement']) && $lesson_blue['for_replacement']
                                                                                && (
                                                                                    (isset($data['replaceable_hours_diff']) && $data['replaceable_hours_diff'] > $min_replacement_period)
                                                                                    || 
                                                                                    (! isset($data['replaceable_hours_diff']))
                                                                                )
                                                                            ) 
                                                                            {
                                                                                $color = 'PaleGreen';
                                                                                $title = __('title.replacement_variant');
                                                                                $other_lesson_participant = 'teacher';
                                                                                if (in_array($data['prev_replace_rules']['teacher_id'], $data['user_teachers_ids'])) {
                                                                                    $is_blue_replacement_link = true;
                                                                                }
                                                                            } elseif ($lesson_blue['id'] == $data['prev_replace_rules']['lesson_id']) {
                                                                                $color = 'Yellow';
                                                                                $title =  __('title.replaceable_lesson');
                                                                            } 
                                                                        @endphp
                                                                        <div class="schedule-cell-bottom" style="background-color: {{ $color }}" title="{{ $title }}">
                                                                            @if(isset($lesson_blue['date']))
                                                                                <div class="margin-10px-top font-size14 schedule-date"><span class="schedule-date-text">{{ $lesson_blue['date'] }}</span></div>
                                                                            @endif
                                                                            @if ($is_blue_replacement_link)
                                                                                <form method="POST" action="{{ route('replacement-request-add') }}" title="{{ __('title.ask_for_replacement') }}" target="_blank">
                                                                                @csrf
                                                                                    <input type="hidden" name="replaceable_lesson_id" value="{{ $data['prev_replace_rules']['lesson_id'] }}">
                                                                                    <input type="hidden" name="replaceable_date" value="{{ $data['replaceable_date_time'] }}">
                                                                                    <input type="hidden" name="replacing_lesson_id" value="{{ $lesson_blue['id'] }}">
                                                                                    <input type="hidden" name="replacing_date" value="{{ $lesson_blue['replacing_date_time'] }}">
                                                                                    <input type="hidden" name="is_regular" value="{{ ! count($lessons['week_dates']) ? 1 : 0 }}">
                                                                                    <input type="hidden" name="initiator_id" value="{{ $data['initiator_id'] }}">
                                                                                    <input type="hidden" name="prev_replace_rules" value="{{ json_encode($data['prev_replace_rules']) }}">
                                                                                    <input type="hidden" name="week_data" value="{{ json_encode($data['week_data']) }}">
                                                                                    <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                                                                                    <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                                                                                    <button type="submit" class="schedule-replace-link">
                                                                                        <div class="margin-10px-top font-size14 schedule-subject-half">{{ __('content.'.$lesson_blue['name']) }} ({{ __('dictionary.'.$lesson_blue['type']) }})</div>
                                                                                        <div class="font-size13 text-light-gray schedule-room-half">{{ __('content.room') }} {{ $lesson_blue['room'] }}</div>
                                                                                        <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue[$other_lesson_participant] }}</div>
                                                                                    </button>
                                                                                </form>    
                                                                            @else
                                                                                <div class="margin-10px-top font-size14 schedule-subject-half">{{ __('content.'.$lesson_blue['name']) }} ({{ __('dictionary.'.$lesson_blue['type']) }})</div>
                                                                                <div class="font-size13 text-light-gray schedule-room-half">{{ __('content.room') }} {{ $lesson_blue['room'] }}</div>
                                                                                <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue[$other_lesson_participant] }}</div>
                                                                            @endif
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
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>            
                </div>
            </div>
        </div>
    </div>
@endsection    