@extends('layouts.app')
@section('content')
    <div class="container">
    <h1>Расписание занятий</h1>
    <h3>Группа: {{ $data['instance_name'] ?? ''}}</h3>
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
                        <th class="text-uppercase">Суббота</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($data) && isset($data['class_periods']) && isset($data['lessons']))
                        @php
                            $week_day = config('enum.week_day_ids');
                            $weekly_period = config('enum.weekly_periods');
                            $weekly_period_id = config('enum.weekly_period_ids');
                            $weekly_period_color = config('enum.weekly_period_colors');
                            $class_period = config('enum.class_period_ids');
                            $class_periods = $data['class_periods'];
                            $lessons = $data['lessons'];
                        @endphp

                        @foreach($class_period as $lesson_name => $value)
                            <tr>
                                <td class="align-middle schedule-period">
                                    <div class="schedule-period-name">Первая</div>
                                    <div class="schedule-period-time">
                                        {{ date('H:i', strtotime($class_periods[$class_period[$lesson_name]]['start'])) }} - {{ date('H:i', strtotime($class_periods[$class_period[$lesson_name]]['end'])) }}
                                    </div>
                                </td>
                                
                                @foreach($week_day as $wd_name => $value)
                                    @if(isset($lessons[$class_period[$lesson_name]][$week_day[$wd_name]][$weekly_period_id['every_week']]))
                                    @php $lesson = $lessons[$class_period[$lesson_name]][$week_day[$wd_name]][$weekly_period_id['every_week']]; @endphp
                                    <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                        <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                        <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                        <div class="font-size13 text-light-gray schedule-group">{{ $lesson['group'] }}</div>    
                                    </td>
                                    @elseif(isset($lessons[$class_period[$lesson_name]][$week_day[$wd_name]][$weekly_period_id['red_week']]) || isset($lessons[$class_period[$lesson_name]][$week_day[$wd_name]][$weekly_period_id['blue_week']]))
                                        @php 
                                            $lesson_red = $lessons[$class_period[$lesson_name]][$week_day[$wd_name]][$weekly_period_id['red_week']] ?? false;
                                            $lesson_blue = $lessons[$class_period[$lesson_name]][$week_day[$wd_name]][$weekly_period_id['blue_week']] ?? false; 
                                        @endphp
                                        <td class="schedule-cell">
                                            @if($lesson_red)
                                                <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                                    <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                                    <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                                    <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['group'] }}</div>
                                                </div>
                                            @endif
                                            @if($lesson_blue)
                                                <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                                    <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                                    <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                                    <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['group'] }}</div>
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