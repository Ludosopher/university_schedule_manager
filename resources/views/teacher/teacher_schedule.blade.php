@extends('layouts.app')
@section('content')
    <div class="container">
    @if($errors->any())
        @foreach($errors->all() as $error)
        <div class="alertFail">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ $error }}
        </div>    
        @endforeach
    @endif
    <h1>Расписание занятий</h1>
    <h3>Преподаватель: {{ $data['instance_name'] ?? ''}}</h3>
    <div class="timetable-img text-center">
        <div class="table-responsive">
            <table class="table table-bordered text-center schedule-table">
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
                                <td class="align-middle schedule-period">
                                    <div class="schedule-period-name">{{ $class_period_id }}</div>
                                    <div class="schedule-period-time">
                                        {{ date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['start'])) }} - {{ date('H:i', strtotime($class_periods[$class_period_ids[$lesson_name]]['end'])) }}
                                    </div>
                                </td>
                                
                                @foreach($week_day_ids as $wd_name => $week_day_id)
                                    @if(isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']]))
                                    @php $lesson = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']]; @endphp
                                    <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                        <div class="dropdown schedule-actions-div">
                                            <a class="dropdown-toggle schedule-actions-button" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                                <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                                <div class="font-size13 text-light-gray schedule-group">{{ $lesson['group'] }}</div>
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <li><a class="dropdown-item" target="_blank" href="{{ route('lesson-replacement', ['replace_rules' => ['lesson_id' => $lesson['id'], 'group_id' => $lesson['group_id'], 'teacher_id' => $lesson['teacher_id'], 'class_period_id' => $lesson['class_period_id'], 'weekly_period_id' => $lesson['weekly_period_id'], 'week_day_id' => $lesson['week_day_id']]]) }}">Варианты замены</a></li>
                                                <li><a class="dropdown-item" target="_blank" href="{{ route('lesson-rescheduling', ['lesson_id' => $lesson['id'], 'group_id' => $lesson['group_id'], 'teacher_id' => $lesson['teacher_id']]) }}">Варианты переноса</a></li>
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
                                                        <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                                        <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                                        <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['group'] }}</div>
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <li><a class="dropdown-item" target="_blank" href="{{ route('lesson-replacement', ['replace_rules' => ['lesson_id' => $lesson_red['id'], 'group_id' => $lesson_red['group_id'], 'teacher_id' => $lesson_red['teacher_id'], 'class_period_id' => $lesson_red['class_period_id'], 'weekly_period_id' => $lesson_red['weekly_period_id'], 'week_day_id' => $lesson_red['week_day_id']]]) }}">Варианты замены</a></li>
                                                        <li><a class="dropdown-item" target="_blank" href="{{ route('lesson-rescheduling', ['lesson_id' => $lesson_red['id'], 'group_id' => $lesson_red['group_id'], 'teacher_id' => $lesson_red['teacher_id']]) }}">Варианты переноса</a></li>
                                                    </ul>
                                                </div>
                                            @endif
                                            @if($lesson_blue)
                                                <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                                    <a class="dropdown-toggle schedule-actions-button" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                                        <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                                        <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['group'] }}</div>
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <li><a class="dropdown-item" target="_blank" href="{{ route('lesson-replacement', ['replace_rules' => ['lesson_id' => $lesson_blue['id'], 'group_id' => $lesson_blue['group_id'], 'teacher_id' => $lesson_blue['teacher_id'], 'class_period_id' => $lesson_blue['class_period_id'], 'weekly_period_id' => $lesson_blue['weekly_period_id'], 'week_day_id' => $lesson_blue['week_day_id']]]) }}">Варианты замены</a></li>
                                                        <li><a class="dropdown-item" target="_blank" href="{{ route('lesson-rescheduling', ['lesson_id' => $lesson_blue['id'], 'group_id' => $lesson_blue['group_id'], 'teacher_id' => $lesson_blue['teacher_id']]) }}">Варианты переноса</a></li>
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