@extends('layouts.app')
@section('content')
    <div class="container">
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
                        <tr>
                            <td class="align-middle"><div>Первая</div><div>{{ date('H:i', strtotime($class_periods[$class_period['first']]->start)) }} - {{ date('H:i', strtotime($class_periods[$class_period['first']]->end)) }}</div></td>
                            @if(isset($lessons[$class_period['first']][$week_day['monday']]))
                                @php $lesson = $lessons[$class_period['first']][$week_day['monday']]; @endphp
                                <td style="background-color: {{ $weekly_period_color[$lesson['weekly_period']] }}">
                                    <div class="margin-10px-top font-size14">{{ $lesson['name'] }}</div>
                                    <div class="font-size13 text-light-gray">{{ $lesson['group'] }}</div>    
                                </td>
                            @else
                                <td></td>
                            @endif
                            <td>
                                <span class="bg-green padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Yoga</span>
                                <div class="margin-10px-top font-size14">9:00-10:00</div>
                                <div class="font-size13 text-light-gray">Marta Healy</div>
                            </td>

                            <td>
                                <span class="bg-yellow padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Music</span>
                                <div class="margin-10px-top font-size14">9:00-10:00</div>
                                <div class="font-size13 text-light-gray">Ivana Wong</div>
                            </td>
                            <td>
                                <span class="bg-sky padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Dance</span>
                                <div class="margin-10px-top font-size14">9:00-10:00</div>
                                <div class="font-size13 text-light-gray">Ivana Wong</div>
                            </td>
                            <td>
                                <span class="bg-purple padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Art</span>
                                <div class="margin-10px-top font-size14">9:00-10:00</div>
                                <div class="font-size13 text-light-gray">Kate Alley</div>
                            </td>
                            <td>
                                <span class="bg-pink padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">English</span>
                                <div class="margin-10px-top font-size14">9:00-10:00</div>
                                <div class="font-size13 text-light-gray">James Smith</div>
                            </td>
                        </tr>

                        <tr>
                            <td class="align-middle"><div>Вторая</div><div>{{ date('H:i', strtotime($class_periods[$class_period['second']]->start)) }} - {{ date('H:i', strtotime($class_periods[$class_period['second']]->end)) }}</div></td>
                            <td>
                                <span class="bg-yellow padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Music</span>
                                <div class="margin-10px-top font-size14">10:00-11:00</div>
                                <div class="font-size13 text-light-gray">Ivana Wong</div>
                            </td>
                            <td class="bg-light-gray">

                            </td>
                            <td>
                                <span class="bg-purple padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Art</span>
                                <div class="margin-10px-top font-size14">10:00-11:00</div>
                                <div class="font-size13 text-light-gray">Kate Alley</div>
                            </td>
                            <td>
                                <span class="bg-green padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Yoga</span>
                                <div class="margin-10px-top font-size14">10:00-11:00</div>
                                <div class="font-size13 text-light-gray">Marta Healy</div>
                            </td>
                            <td>
                                <span class="bg-pink padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">English</span>
                                <div class="margin-10px-top font-size14">10:00-11:00</div>
                                <div class="font-size13 text-light-gray">James Smith</div>
                            </td>
                            <td class="bg-light-gray">

                            </td>
                        </tr>

                        <tr>
                            <td class="align-middle"><div>Третья</div><div>{{ date('H:i', strtotime($class_periods[$class_period['third']]->start)) }} - {{ date('H:i', strtotime($class_periods[$class_period['third']]->end)) }}</div></td>
                            <td>
                                <span class="bg-lightred padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Break</span>
                                <div class="margin-10px-top font-size14">11:00-12:00</div>
                            </td>
                            <td>
                                <span class="bg-lightred padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Break</span>
                                <div class="margin-10px-top font-size14">11:00-12:00</div>
                            </td>
                            <td>
                                <span class="bg-lightred padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Break</span>
                                <div class="margin-10px-top font-size14">11:00-12:00</div>
                            </td>
                            <td>
                                <span class="bg-lightred padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Break</span>
                                <div class="margin-10px-top font-size14">11:00-12:00</div>
                            </td>
                            <td>
                                <span class="bg-lightred padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Break</span>
                                <div class="margin-10px-top font-size14">11:00-12:00</div>
                            </td>
                            <td>
                                <span class="bg-lightred padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Break</span>
                                <div class="margin-10px-top font-size14">11:00-12:00</div>
                            </td>
                        </tr>

                        <tr>
                            <td class="align-middle"><div>Четвёртая</div><div>{{ date('H:i', strtotime($class_periods[$class_period['fourth']]->start)) }} - {{ date('H:i', strtotime($class_periods[$class_period['fourth']]->end)) }}</div></td>
                                    <td class="bg-light-gray">

                            </td>
                            <td>
                                <span class="bg-purple padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Art</span>
                                <div class="margin-10px-top font-size14">12:00-1:00</div>
                                <div class="font-size13 text-light-gray">Kate Alley</div>
                            </td>
                            <td>
                                <span class="bg-sky padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Dance</span>
                                <div class="margin-10px-top font-size14">12:00-1:00</div>
                                <div class="font-size13 text-light-gray">Ivana Wong</div>
                            </td>
                            <td>
                                <span class="bg-yellow padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Music</span>
                                <div class="margin-10px-top font-size14">12:00-1:00</div>
                                <div class="font-size13 text-light-gray">Ivana Wong</div>
                            </td>
                            <td class="bg-light-gray">

                            </td>
                            <td>
                                <span class="bg-green padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Yoga</span>
                                <div class="margin-10px-top font-size14">12:00-1:00</div>
                                <div class="font-size13 text-light-gray">Marta Healy</div>
                            </td>
                        </tr>

                        <tr>
                            <td class="align-middle"><div>Пятая</div><div>{{ date('H:i', strtotime($class_periods[$class_period['fifth']]->start)) }} - {{ date('H:i', strtotime($class_periods[$class_period['fifth']]->end)) }}</div></td>
                            <td>
                                <span class="bg-pink padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">English</span>
                                <div class="margin-10px-top font-size14">1:00-2:00</div>
                                <div class="font-size13 text-light-gray">James Smith</div>
                            </td>
                            <td>
                                <span class="bg-yellow padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Music</span>
                                <div class="margin-10px-top font-size14">1:00-2:00</div>
                                <div class="font-size13 text-light-gray">Ivana Wong</div>
                            </td>
                            <td class="bg-light-gray">

                            </td>
                            <td>
                                <span class="bg-pink padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">English</span>
                                <div class="margin-10px-top font-size14">1:00-2:00</div>
                                <div class="font-size13 text-light-gray">James Smith</div>
                            </td>
                            <td>
                                <span class="bg-green padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Yoga</span>
                                <div class="margin-10px-top font-size14">1:00-2:00</div>
                                <div class="font-size13 text-light-gray">Marta Healy</div>
                            </td>
                            <td>
                                <span class="bg-yellow padding-5px-tb padding-15px-lr border-radius-5 margin-10px-bottom text-white font-size16  xs-font-size13">Music</span>
                                <div class="margin-10px-top font-size14">1:00-2:00</div>
                                <div class="font-size13 text-light-gray">Ivana Wong</div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>            
    </div> 
@endsection    