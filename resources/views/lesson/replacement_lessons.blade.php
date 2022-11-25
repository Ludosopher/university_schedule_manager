@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="getAllContainer">
            <div class="getAllLeft">
                <h4>Найти</h4>
                <form method="POST" action="{{ route('lesson-replacement') }}">
                @csrf
                    <input type="hidden" name="prev_replace_rules" value="{{ json_encode($data['prev_replace_rules']) }}">
                    @if(isset($data['filter_form_fields']))
                        @foreach($data['filter_form_fields'] as $field)
                            @if($field['type'] == 'between')
                                @php $field_name = $field['name']; @endphp
                                <h6>{{ $field['header'] }}</h6>
                                <div class="birthYear">
                                    <div>
                                        <label for="{{$field_name}}_from" class="form-label">От</label>
                                        <input name="{{$field_name}}_from" type="number" min="{{ $field['min_value'] }}" max="{{ $field['max_value'] }}" step="{{ $field['step'] }}" class="form-control form-control-sm" id="{{$field_name}}_from" value="{{ old($field_name.'_from') !== null ? old($field_name.'_from') : '' }}">
                                        @if ($errors !== null && $errors->has($field_name.'_from'))
                                            @foreach($errors->get($field_name.'_from') as $error)
                                                <div class="validationErrorText">{{ $error }}</div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div>
                                        <label for="{{$field_name}}_to" class="form-label">До</label>
                                        <input name="{{$field_name}}_to" type="number" min="{{ $field['min_value'] }}" max="{{ $field['max_value'] }}" step="{{ $field['step'] }}" class="form-control form-control-sm" id="{{$field_name}}_to" value="{{ old($field_name.'_to') !== null ? old($field_name.'_to') : '' }}">
                                        @if ($errors !== null && $errors->has($field_name.'_to'))
                                            @foreach($errors->get($field_name.'_to') as $error)
                                                <div class="validationErrorText">{{ $error }}</div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>    
                            @endif
                            @if($field['type'] == 'objects-select')
                                @php $field_name = $field['name'].'_id'; @endphp
                                <div class="mb-3">
                                    @if(isset($field['multiple_options']) && is_array($field['multiple_options']) && $field['multiple_options']['is_multiple'])
                                        <label class="form-label">{{ $field['header'] }}<span style="color: red;">*</span></label>
                                        <select multiple size="{{ $field['multiple_options']['size'] }}" name="{{ $field_name }}[]" class="form-select" aria-label="Default select example">    
                                    @else
                                        <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
                                        <select name="{{ $field_name }}" class="form-select" aria-label="Default select example">
                                    @endif
                                        @foreach($data[$field['plural_name']] as $value)
                                            @if(old($field_name) !== null
                                                && (old($field_name) == (is_object($value) ? $value->id : $value['id'])
                                                   || (is_array(old($field_name)) && in_array((is_object($value) ? $value->id : $value['id']), old($field_name)))))
                                                    <option selected value="{{ is_object($value) ? $value->id : $value['id'] }}">{{ is_object($value) ? $value->name : $value['name'] }}</option>
                                            @elseif(isset($data['updating_instance']) && $data['updating_instance']->$field_name == $value->id)
                                                <option selected value="{{ is_object($value) ? $value->id : $value['id'] }}">{{ is_object($value) ? $value->name : $value['name'] }}</option>
                                            @elseif(isset($data['updating_instance']) 
                                                    && is_array($data['updating_instance']->$field_name) 
                                                    && in_array($value->id, $data['updating_instance']->$field_name))
                                                <option selected value="{{ is_object($value) ? $value->id : $value['id'] }}">{{ is_object($value) ? $value->name : $value['name'] }}</option>    
                                            @else
                                                <option value="{{ is_object($value) ? $value->id : $value['id'] }}">{{ is_object($value) ? $value->name : $value['name'] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @if ($errors !== null && $errors->has($field_name))
                                        @foreach($errors->get($field_name) as $error)
                                            <div class="validationErrorText">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>    
                            @endif

                            @if($field['type'] == 'input')
                                @php $field_name = $field['name']; @endphp
                                <div class="mb-3">
                                    <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
                                    <input name="{{ $field_name }}" type="{{ $field['input_type'] }}" class="form-control form-control-sm filter-input" id="{{ $field_name }}" value="{{ old($field_name) !== null ? old($field_name) : '' }}">
                                    @if ($errors !== null && $errors->has($field_name))
                                        @foreach($errors->get($field_name) as $error)
                                            <div class="validationErrorText">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>    
                            @endif    
                        @endforeach    
                    @endif
                    <input type="week" name="week_number" value="{{ $data['week_data']['week_number'] }}" style="margin-bottom: 20px;">
                    <p for="{{ $field_name }}" class="form-explanation"><span style="color: red;">*</span> Для выбора нескольких полей нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора.</p>
                    <button type="submit" class="btn btn-primary form-button">Показать</button>
                </form>
            </div>
            <div class="getAllRight">
                @if(isset($data['week_data']['start_date']) && isset($data['week_data']['end_date']))
                    <h1>Варианты замены занятия с {{ $data['week_data']['start_date'] }} по {{ $data['week_data']['end_date'] }}</h1>   
                @else
                    <h1>Регулярные варианты замены занятия</h1>
                @endif
                <h5>Заменяемое занятие: {{ $data['header_data']['class_period'] }} пара, {{ $data['header_data']['week_day'] }}, {{ $data['header_data']['weekly_period'] }}</h5>
                <h5>Преподавателя: {{ $data['header_data']['teacher'] }}</h5>
                <div class="replacement-schedule-header-div">
                    <h5>Группы: {{ $data['header_data']['group'] }}</h5>
                    <div class="schedule-button-group">
                        <form method="POST" action="{{ route('lesson-replacement-doc-export') }}">
                        @csrf
                            <input type="hidden" name="replacement_lessons" value="{{ json_encode($data['replacement_lessons']) }}">
                            <input type="hidden" name="header_data" value="{{ json_encode($data['header_data']) }}">
                            <button type="submit" class="btn btn-primary top-right-button">В Word</button>
                        </form>
                    </div>
                </div>
                <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            @foreach($data['table_properties'] as $property)
                                <th class="th-sm text-center align-top">{{ $property['header'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['replacement_lessons'] as $lesson)
                            <tr>
                                @foreach($data['table_properties'] as $property)
                                    @php $field = $property['field'] @endphp
                                    @if($field == 'profession_level_name')
                                        <td class="regular-cell"><a href="{{ route('teacher-schedule', ['schedule_teacher_id' => $lesson['teacher_id']]) }}">{{ is_array($lesson[$field]) ? $lesson[$field]['name'] : $lesson[$field] }}</a></td>
                                    @elseif($field == 'week_day_id')
                                        @php
                                            $lesson_date = "";
                                            if (isset($lesson['date'])) {
                                                $lesson_date = ' ('.$lesson['date'].')';
                                            }
                                        @endphp
                                        <td class="regular-cell">{{ $lesson[$field]['name'] }}{{ $lesson_date }}</td>
                                    @else
                                        <td class="regular-cell">{{ is_array($lesson[$field]) ? $lesson[$field]['name'] : $lesson[$field] }}</td>    
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="replacement-schedule-header-div">
                    <h3>В расписании преподавателя</h3>
                    <form method="POST" action="{{ route('lesson-replacement-schedule-doc-export') }}">
                    @csrf
                        <input type="hidden" name="lessons" value="{{ json_encode($data['in_schedule']) }}">
                        <input type="hidden" name="header_data" value="{{ json_encode($data['header_data']) }}">
                        <input type="hidden" name="week_data" value="{{ json_encode($data['week_data']) }}">
                        <input type="hidden" name="replaceable_lesson_id" value="{{ $data['prev_replace_rules']['lesson_id'] }}">
                        <button type="submit" class="btn btn-primary replacement-doc-export-button">В Word</button>
                    </form>
                </div>
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
                                    @if(isset($data['week_data']['week_number']))
                                        <th class="text-uppercase">Суббота</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data) && isset($data['class_periods']) && isset($data['in_schedule']))
                                    @php
                                        $week_day_ids = config('enum.week_day_ids');
                                        $weekly_period = config('enum.weekly_periods');
                                        $weekly_period_id = config('enum.weekly_period_ids');
                                        $weekly_period_color = config('enum.weekly_period_colors');
                                        $class_period_ids = config('enum.class_period_ids');
                                        $class_periods = array_combine(range(1, count($data['class_periods'])), array_values($data['class_periods']->toArray()));
                                        $lessons = $data['in_schedule'];
                                        $week_days_limits = config('site.week_days_limits');
                                        $class_periods_limits = config('site.class_periods_limits');
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
                                                @if($week_day_id <= $week_days_limit)
                                                    @if(isset($lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']]))
                                                    @php 
                                                        $lesson = $lessons[$class_period_ids[$lesson_name]][$week_day_ids[$wd_name]][$weekly_period_id['every_week']];
                                                        $other_lesson_participant = 'group';
                                                        $color = '';
                                                        $title = '';
                                                        if (isset($lesson['for_replacement']) && $lesson['for_replacement']) {
                                                            $color = 'PaleGreen';
                                                            $title = 'Вариант замены';
                                                            $other_lesson_participant = 'teacher';
                                                        } elseif ($lesson['id'] == $data['prev_replace_rules']['lesson_id']) {
                                                            $color = 'Yellow';
                                                            $title = 'Заменяемое занятие';
                                                        } 
                                                    @endphp
                                                    <td class="schedule-cell" style="background-color: {{ $color }}" title="{{ $title }}">
                                                        <div class="dropdown schedule-actions-div">
                                                            @if(isset($lesson['date']))
                                                                <div class="margin-10px-top font-size14 schedule-date"><span class="schedule-date-text">{{ $lesson['date'] }}</span></div>
                                                            @endif
                                                            <div class="margin-10px-top font-size14 schedule-subject">{{ $lesson['name'] }} ({{ $lesson['type'] }})</div>
                                                            <div class="font-size13 text-light-gray schedule-room">ауд. {{ $lesson['room'] }}</div>
                                                            <div class="font-size13 text-light-gray schedule-group">{{ $lesson[$other_lesson_participant] }}</div>
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
                                                                    if (isset($lesson_red['for_replacement']) && $lesson_red['for_replacement']) {
                                                                        $color = 'PaleGreen';
                                                                        $title = 'Вариант замены';
                                                                        $other_lesson_participant = 'teacher';
                                                                    } elseif ($lesson_red['id'] == $data['prev_replace_rules']['lesson_id']) {
                                                                        $color = 'Yellow';
                                                                        $title = 'Заменяемое занятие';
                                                                    }
                                                                @endphp
                                                                <div class="schedule-cell-top" style="background-color: {{ $color }}" title="{{ $title }}">
                                                                    @if(isset($lesson_red['date']))
                                                                        <div class="margin-10px-top font-size14 schedule-date"><span class="schedule-date-text">{{ $lesson_red['date'] }}</span></div>
                                                                    @endif
                                                                    <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_red['name'] }} ({{ $lesson_red['type'] }})</div>
                                                                    <div class="font-size13 text-light-gray schedule-room-half">ауд. {{ $lesson_red['room'] }}</div>
                                                                    <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_red[$other_lesson_participant] }}</div>
                                                                </div>
                                                            @endif
                                                            @if($lesson_blue)
                                                                @php 
                                                                    $other_lesson_participant = 'group';
                                                                    $color = '';
                                                                    $title = '';
                                                                    if (isset($lesson_blue['for_replacement']) && $lesson_blue['for_replacement']) {
                                                                        $color = 'PaleGreen';
                                                                        $title = 'Вариант замены';
                                                                        $other_lesson_participant = 'teacher';
                                                                    } elseif ($lesson_blue['id'] == $data['prev_replace_rules']['lesson_id']) {
                                                                        $color = 'Yellow';
                                                                        $title = 'Заменяемое занятие';
                                                                    } 
                                                                @endphp
                                                                <div class="schedule-cell-bottom" style="background-color: {{ $color }}" title="{{ $title }}">
                                                                    @if(isset($lesson_blue['date']))
                                                                        <div class="margin-10px-top font-size14 schedule-date"><span class="schedule-date-text">{{ $lesson_blue['date'] }}</span></div>
                                                                    @endif
                                                                    <div class="margin-10px-top font-size14 schedule-subject-half">{{ $lesson_blue['name'] }} ({{ $lesson_blue['type'] }})</div>
                                                                    <div class="font-size13 text-light-gray schedule-room-half">ауд. {{ $lesson_blue['room'] }}</div>
                                                                    <div class="font-size13 text-light-gray schedule-group-half">{{ $lesson_blue[$other_lesson_participant] }}</div>
                                                                </div>
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
            </div>
        </div>
    </div> 
@endsection    