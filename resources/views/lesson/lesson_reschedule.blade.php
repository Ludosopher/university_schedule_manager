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
            {{-- @if(isset($data['week_data']['start_date']) && isset($data['week_data']['end_date']))
                <h1>Варианты переноса занятия в период с {{ $data['week_data']['start_date'] }} по {{ $data['week_data']['end_date'] }}</h1>   
            @else
                <h1>Регулярные варианты переноса занятия</h1>
            @endif --}}
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
                <h1 class="top-header">Варианты переноса занятия в период с {{ $data['week_data']['start_date'] }} по {{ $data['week_data']['end_date'] }} <span style="background-color: {{ $bg_color }};">( {{ $week_color }} неделя )</span></h1>   
            @else
                <h1 class="top-header">Регулярные варианты переноса занятия</h1>
            @endif
            <h4>Переносимое занятие: {{ $data['lesson_name'] }} - {{ $data['lesson_week_day'] }} - {{ $data['lesson_weekly_period'] }} - {{ $data['lesson_class_period'] }} пара</h4>
            <h4>Преподаватель: {{ $data['teacher_name'] ?? ''}}</h4>
        </div>
        <div class="replacement-schedule-header-div">
            <h4>Группа(ы): {{ $data['groups_name'] ?? ''}}</h4>
            <div class="schedule-button-group">
                <form method="POST" action="{{ route('lesson-rescheduling') }}" class="top-right-button">
                @csrf
                    <input type="hidden" name="teacher_id" value="{{ $data['teacher_id'] }}">
                    <input type="hidden" name="lesson_id" value="{{ $data['lesson_id'] }}">
                    @php
                        $week_number = isset($data['week_data']['week_number']) ? $data['week_data']['week_number'] : (isset($data['week_number']) ? $data['week_number'] : '');
                    @endphp
                    <input type="week" name="week_number" value="{{ $week_number }}">
                    <button type="submit" class="btn btn-primary">За эту неделю</button>
                </form>
            </div>
        </div>
    </div>
    <div class="timetable-img text-center">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr class="bg-light-gray">
                        <th class="text-uppercase">Пара</th>
                        @if(isset($data['week_dates']))
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
                            {{-- <th class="text-uppercase">Суббота</th> --}}
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if(isset($data) && isset($data['class_periods']) && isset($data['free_periods']))
                        @php
                            $week_day_ids = config('enum.week_day_ids');
                            $weekly_period = config('enum.weekly_periods');
                            $weekly_period_id = config('enum.weekly_period_ids');
                            $free_weekly_period_color = config('enum.free_weekly_period_colors');
                            $class_period_ids = config('enum.class_period_ids');
                            $week_days_limits = config('site.week_days_limits');
                            $class_periods_limits = config('site.class_periods_limits');
                            $class_periods = $data['class_periods'];
                            $free_periods = $data['free_periods'];
                            if (isset($data['week_data']['week_number'])) {
                                $week_days_limit = $week_days_limits['distance'];
                                $class_periods_limit = $class_periods_limits['distance']; 
                            } else {
                                $week_days_limit = $week_days_limits['full_time'];
                                $class_periods_limit = $class_periods_limits['full_time'];
                            }
                        @endphp
                        @foreach($class_period_ids as $lesson_name => $class_period_id)
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
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="replacement-schedule-header-div">
 {{-- @dd($data) --}}
            <h5>Смотреть в расписании:</h5>
            <div class="schedule-button-group">
                {{-- <a class="btn btn-primary reschedule-link" href="{{ route('teacher-reschedule', ['lesson_id' => $data['lesson_id'], 'teacher_id' => $data['teacher_id'], 'week_number' => $data['week_data']['week_number']]) }}" role="button" target="_blank">Преподавателя</a> --}}
                <form method="POST" action="{{ route('teacher-reschedule') }}" target="_blank">
                @csrf
                    <input type="hidden" name="lesson_id" value="{{ $data['lesson_id'] }}">
                    <input type="hidden" name="teacher_id" value="{{ $data['teacher_id'] }}">
                    <input type="hidden" name="week_number" value="{{ $data['week_data']['week_number'] }}">
                    <input type="hidden" name="prev_data" value="{{ json_encode(old()) }}">
                    <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                    <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                    <button type="submit" class="btn btn-light schedule-dropdown">Преподавателя</button>
                </form>
                @if (isset($data['groups_ids_names']) && is_array($data['groups_ids_names']))
                    @foreach ($data['groups_ids_names'] as $group)
                        {{-- <a class="btn btn-primary reschedule-link top-right-button" href="{{ route('group-reschedule', ['lesson_id' => $data['lesson_id'], 'teacher_id' => $data['teacher_id'], 'group_id' => $group['id'], 'week_number' => $data['week_data']['week_number']]) }}" role="button" target="_blank">{{ $group['name'] }}</a> --}}
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
