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
                        {{-- First lesson --}}
                        <tr>
                            <td class="align-middle schedule-period">
                                <div class="schedule-period-name">Первая</div>
                                <div class="schedule-period-time">
                                    {{ date('H:i', strtotime($class_periods[$class_period['first']]['start'])) }} - {{ date('H:i', strtotime($class_periods[$class_period['first']]['end'])) }}
                                </div>
                            </td>
                            {{-- First lesson. Monday --}}
                            @if(isset($lessons[$class_period['first']][$week_day['monday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['first']][$week_day['monday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['first']][$week_day['monday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['first']][$week_day['monday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['first']][$week_day['monday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['first']][$week_day['monday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- First lesson. Tuesday --}}
                            @if(isset($lessons[$class_period['first']][$week_day['tuesday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['first']][$week_day['tuesday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['first']][$week_day['tuesday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['first']][$week_day['tuesday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['first']][$week_day['tuesday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['first']][$week_day['tuesday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- First lesson. Wednesday --}}
                            @if(isset($lessons[$class_period['first']][$week_day['wednesday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['first']][$week_day['wednesday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['first']][$week_day['wednesday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['first']][$week_day['wednesday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['first']][$week_day['wednesday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['first']][$week_day['wednesday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- First lesson. Thursday --}}
                            @if(isset($lessons[$class_period['first']][$week_day['thursday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['first']][$week_day['thursday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['first']][$week_day['thursday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['first']][$week_day['thursday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['first']][$week_day['thursday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['first']][$week_day['thursday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- First lesson. Friday --}}
                            @if(isset($lessons[$class_period['first']][$week_day['friday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['first']][$week_day['friday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['first']][$week_day['friday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['first']][$week_day['friday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['first']][$week_day['friday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['first']][$week_day['friday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- First lesson. Saturday --}}
                            @if(isset($lessons[$class_period['first']][$week_day['saturday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['first']][$week_day['saturday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['first']][$week_day['saturday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['first']][$week_day['saturday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['first']][$week_day['saturday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['first']][$week_day['saturday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                        </tr>

                        {{-- Second lesson --}}
                        <tr>
                            <td class="align-middle schedule-period">
                                <div class="schedule-period-name">Вторая</div>
                                <div class="schedule-period-time">
                                    {{ date('H:i', strtotime($class_periods[$class_period['second']]['start'])) }} - {{ date('H:i', strtotime($class_periods[$class_period['second']]['end'])) }}
                                </div>
                            </td>
                            {{-- Second lesson. Monday --}}
                            @if(isset($lessons[$class_period['second']][$week_day['monday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['second']][$week_day['monday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['second']][$week_day['monday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['second']][$week_day['monday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['second']][$week_day['monday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['second']][$week_day['monday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Second lesson. Tuesday --}}
                            @if(isset($lessons[$class_period['second']][$week_day['tuesday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['second']][$week_day['tuesday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['second']][$week_day['tuesday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['second']][$week_day['tuesday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['second']][$week_day['tuesday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['second']][$week_day['tuesday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Second lesson. Wednesday --}}
                            @if(isset($lessons[$class_period['second']][$week_day['wednesday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['second']][$week_day['wednesday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['second']][$week_day['wednesday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['second']][$week_day['wednesday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['second']][$week_day['wednesday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['second']][$week_day['wednesday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Second lesson. Thursday --}}
                            @if(isset($lessons[$class_period['second']][$week_day['thursday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['second']][$week_day['thursday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['second']][$week_day['thursday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['second']][$week_day['thursday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['second']][$week_day['thursday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['second']][$week_day['thursday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Second lesson. Friday --}}
                            @if(isset($lessons[$class_period['second']][$week_day['friday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['second']][$week_day['friday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['second']][$week_day['friday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['second']][$week_day['friday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['second']][$week_day['friday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['second']][$week_day['friday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Second lesson. Saturday --}}
                            @if(isset($lessons[$class_period['second']][$week_day['saturday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['second']][$week_day['saturday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['second']][$week_day['saturday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['second']][$week_day['saturday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['second']][$week_day['saturday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['second']][$week_day['saturday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                        </tr>

                        {{-- Third lesson --}}
                        <tr>
                            <td class="align-middle schedule-period">
                                <div class="schedule-period-name">Третья</div>
                                <div class="schedule-period-time">
                                    {{ date('H:i', strtotime($class_periods[$class_period['third']]['start'])) }} - {{ date('H:i', strtotime($class_periods[$class_period['third']]['end'])) }}
                                </div>
                            </td>
                            {{-- Third lesson. Monday --}}
                            @if(isset($lessons[$class_period['third']][$week_day['monday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['third']][$week_day['monday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['third']][$week_day['monday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['third']][$week_day['monday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['third']][$week_day['monday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['third']][$week_day['monday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Third lesson. Tuesday --}}
                            @if(isset($lessons[$class_period['third']][$week_day['tuesday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['third']][$week_day['tuesday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['third']][$week_day['tuesday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['third']][$week_day['tuesday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['third']][$week_day['tuesday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['third']][$week_day['tuesday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Third lesson. Wednesday --}}
                            @if(isset($lessons[$class_period['third']][$week_day['wednesday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['third']][$week_day['wednesday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['third']][$week_day['wednesday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['third']][$week_day['wednesday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['third']][$week_day['wednesday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['third']][$week_day['wednesday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Third lesson. Thursday --}}
                            @if(isset($lessons[$class_period['third']][$week_day['thursday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['third']][$week_day['thursday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['third']][$week_day['thursday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['third']][$week_day['thursday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['third']][$week_day['thursday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['third']][$week_day['thursday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Third lesson. Friday --}}
                            @if(isset($lessons[$class_period['third']][$week_day['friday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['third']][$week_day['friday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['third']][$week_day['friday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['third']][$week_day['friday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['third']][$week_day['friday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['third']][$week_day['friday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Third lesson. Saturday --}}
                            @if(isset($lessons[$class_period['third']][$week_day['saturday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['third']][$week_day['saturday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['third']][$week_day['saturday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['third']][$week_day['saturday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['third']][$week_day['saturday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['third']][$week_day['saturday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                        </tr>

                        {{-- Fourth lesson --}}
                        <tr>
                            <td class="align-middle schedule-period">
                                <div class="schedule-period-name">Четвёртая</div>
                                <div class="schedule-period-time">
                                    {{ date('H:i', strtotime($class_periods[$class_period['fourth']]['start'])) }} - {{ date('H:i', strtotime($class_periods[$class_period['fourth']]['end'])) }}
                                </div>
                            </td>
                            {{-- Fourth lesson. Monday --}}
                            @if(isset($lessons[$class_period['fourth']][$week_day['monday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fourth']][$week_day['monday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fourth']][$week_day['monday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fourth']][$week_day['monday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fourth']][$week_day['monday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fourth']][$week_day['monday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Fourth lesson. Tuesday --}}
                            @if(isset($lessons[$class_period['fourth']][$week_day['tuesday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fourth']][$week_day['tuesday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fourth']][$week_day['tuesday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fourth']][$week_day['tuesday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fourth']][$week_day['tuesday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fourth']][$week_day['tuesday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Fourth lesson. Wednesday --}}
                            @if(isset($lessons[$class_period['fourth']][$week_day['wednesday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fourth']][$week_day['wednesday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fourth']][$week_day['wednesday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fourth']][$week_day['wednesday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fourth']][$week_day['wednesday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fourth']][$week_day['wednesday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Fourth lesson. Thursday --}}
                            @if(isset($lessons[$class_period['fourth']][$week_day['thursday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fourth']][$week_day['thursday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fourth']][$week_day['thursday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fourth']][$week_day['thursday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fourth']][$week_day['thursday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fourth']][$week_day['thursday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Fourth lesson. Friday --}}
                            @if(isset($lessons[$class_period['fourth']][$week_day['friday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fourth']][$week_day['friday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fourth']][$week_day['friday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fourth']][$week_day['friday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fourth']][$week_day['friday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fourth']][$week_day['friday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Fourth lesson. Saturday --}}
                            @if(isset($lessons[$class_period['fourth']][$week_day['saturday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fourth']][$week_day['saturday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fourth']][$week_day['saturday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fourth']][$week_day['saturday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fourth']][$week_day['saturday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fourth']][$week_day['saturday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                        </tr>

                        {{-- Fifth lesson --}}
                        <tr>
                            <td class="align-middle schedule-period">
                                <div class="schedule-period-name">Пятая</div>
                                <div class="schedule-period-time">
                                    {{ date('H:i', strtotime($class_periods[$class_period['fifth']]['start'])) }} - {{ date('H:i', strtotime($class_periods[$class_period['fifth']]['end'])) }}
                                </div>
                            </td>
                            {{-- Fifth lesson. Monday --}}
                            @if(isset($lessons[$class_period['fifth']][$week_day['monday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fifth']][$week_day['monday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fifth']][$week_day['monday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fifth']][$week_day['monday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fifth']][$week_day['monday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fifth']][$week_day['monday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Fifth lesson. Tuesday --}}
                            @if(isset($lessons[$class_period['fifth']][$week_day['tuesday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fifth']][$week_day['tuesday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fifth']][$week_day['tuesday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fifth']][$week_day['tuesday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fifth']][$week_day['tuesday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fifth']][$week_day['tuesday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Fifth lesson. Wednesday --}}
                            @if(isset($lessons[$class_period['fifth']][$week_day['wednesday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fifth']][$week_day['wednesday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fifth']][$week_day['wednesday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fifth']][$week_day['wednesday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fifth']][$week_day['wednesday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fifth']][$week_day['wednesday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Fifth lesson. Thursday --}}
                            @if(isset($lessons[$class_period['fifth']][$week_day['thursday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fifth']][$week_day['thursday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fifth']][$week_day['thursday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fifth']][$week_day['thursday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fifth']][$week_day['thursday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fifth']][$week_day['thursday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Fifth lesson. Friday --}}
                            @if(isset($lessons[$class_period['fifth']][$week_day['friday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fifth']][$week_day['friday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fifth']][$week_day['friday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fifth']][$week_day['friday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fifth']][$week_day['friday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fifth']][$week_day['friday']][$weekly_period_id['blue_week']] ?? false; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                            {{-- Fifth lesson. Saturday --}}
                            @if(isset($lessons[$class_period['fifth']][$week_day['saturday']][$weekly_period_id['every_week']]))
                                @php $lesson = $lessons[$class_period['fifth']][$week_day['saturday']][$weekly_period_id['every_week']]; @endphp
                                <td class="schedule-cell" style="background-color: {{ $weekly_period_color[$weekly_period_id['every_week']] }}">
                                    <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray schedule-type">( {{ $lesson['type'] }} )</div>
                                    <div class="font-size13 text-light-gray schedule-group">{{ $lesson['teacher'] }}</div>    
                                </td>
                            @elseif(isset($lessons[$class_period['fifth']][$week_day['saturday']][$weekly_period_id['red_week']]) || isset($lessons[$class_period['fifth']][$week_day['saturday']][$weekly_period_id['blue_week']]))
                                @php 
                                    $lesson_red = $lessons[$class_period['fifth']][$week_day['saturday']][$weekly_period_id['red_week']] ?? false;
                                    $lesson_blue = $lessons[$class_period['fifth']][$week_day['saturday']][$weekly_period_id['blue_week']]; 
                                @endphp
                                <td class="schedule-cell">
                                    @if($lesson_red)
                                        <div class="schedule-cell-top" style="background-color: {{ $weekly_period_color[$weekly_period_id['red_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_red['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red['teacher'] }}</div>
                                        </div>
                                    @endif
                                    @if($lesson_blue)
                                        <div class="schedule-cell-bottom" style="background-color: {{ $weekly_period_color[$weekly_period_id['blue_week']] }}">
                                            <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }}</div>
                                            <div class="font-size13 text-light-gray schedule-type-half">( {{ $lesson_blue['type'] }} )</div>
                                            <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue['teacher'] }}</div>
                                        </div>    
                                    @endif  
                                </td>
                            @else
                                <td class="schedule-cell"></td>
                            @endif
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>            
    </div> 
@endsection    