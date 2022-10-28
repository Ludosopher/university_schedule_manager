@extends('layouts.app')
@section('content')
    <div class="container">
    <h1>Варианты переноса занятия</h1>
    <h3>Переносимое занятие: {{ $data['lesson_name'] }} - {{ $data['lesson_week_day'] }} - {{ $data['lesson_weekly_period'] }} - {{ $data['lesson_class_period'] }} пара</h3>
    <h3>Преподаватель: {{ $data['teacher_name'] ?? ''}}</h3>
    <h3>Группа: {{ $data['group_name'] ?? ''}}</h3>
    <div class="timetable-img text-center">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr class="bg-light-gray">
                        <th class="text-uppercase">Пара
                        </th>
                        <th class="text-uppercase">Понедельник</th>
                        <th class="text-uppercase">Вторник</th>
                        <th class="text-uppercase">Среда</th>
                        <th class="text-uppercase">Четверг</th>
                        <th class="text-uppercase">Пятница</th>
                        {{-- <th class="text-uppercase">Суббота</th> --}}
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
                            $rescheduling_week_days_limit = config('site.rescheduling_week_days_limit');
                            $class_periods = $data['class_periods'];
                            $free_periods = $data['free_periods'];
                        @endphp
                        @foreach($class_period_ids as $lesson_name => $class_period_id)
                            <tr>
                                <td class="align-middle schedule-period">
                                    <div class="schedule-period-name">{{ $class_period_id }}</div>
                                    <div class="schedule-period-time">
                                        {{ date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['start'])) }} - {{ date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['end'])) }}
                                    </div>
                                </td>
                                
                                @foreach($week_day_ids as $wd_name => $week_day_id)
                                    @if($week_day_id <= $rescheduling_week_days_limit)
                                        @if(isset($free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']]))
                                        @php $free_period = $free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']]; @endphp
                                        <td class="schedule-cell" style="background-color: {{ $free_weekly_period_color[$weekly_period_id['every_week']] }};"></td>
                                        @elseif(isset($free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']]) || isset($free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']]))
                                            @php 
                                                $free_period_red = $free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['red_week']] ?? false;
                                                $free_period_blue = $free_periods[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['blue_week']] ?? false; 
                                            @endphp
                                            <td class="schedule-cell">
                                                @if($free_period_red)
                                                    <div class="schedule-cell-top" style="background-color: {{ $free_weekly_period_color[$weekly_period_id['red_week']] }}"></div>
                                                @endif
                                                @if($free_period_blue)
                                                    <div class="schedule-cell-bottom" style="background-color: {{ $free_weekly_period_color[$weekly_period_id['blue_week']] }}"></div>
                                                @endif
                                            </td>
                                        @else
                                            <td class="schedule-cell"></td>
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
@endsection    