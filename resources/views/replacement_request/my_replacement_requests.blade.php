@extends('layouts.personal')
@section('personal_content')
    <div class="container">
        @if (\Session::has('new_instance_name'))
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ \Session::get('new_instance_name') }} успешно добавлена.
            </div>
        @endif
        @if (\Session::has('updated_instance_name'))
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ \Session::get('updated_instance_name') }} успешно обновлена.
            </div>
        @endif
        @php $replacement_request_status_ids = config('enum.replacement_request_status_ids'); @endphp
        <h2>Мои просьбы о замене</h2>
        <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="th-sm text-center align-top" rowspan="2"></th>
                    @foreach($data['table_properties'] as $property)
                        @if(in_array($property['header'], ['День недели', 'Пара', 'Аудитория', 'Преподаватель']))
                            @continue
                        @endif
                        @php
                            $rowspan = '';
                            $colspan = '';
                            $header = $property['header'];
                            if (in_array($property['header'], ['Группа(ы)', 'Постоянная замена', 'Статус', 'Инициатор'])) {
                                $rowspan = 2;
                            }
                            if ($property['header'] == 'Дата') {
                                $colspan = 5;
                                if ($property['field'] == 'replaceable_date') {
                                    $header = 'Заменяемое занятие';
                                } else {
                                    $header = 'Заменяющее занятие';
                                }
                                $was_first_lesson = true;
                            }
                        @endphp
                        <th class="th-sm text-center align-top" rowspan="{{ $rowspan }}" colspan="{{ $colspan }}">{{ $header }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach($data['table_properties'] as $property)
                        @if(in_array($property['header'], ['Группа(ы)', 'Постоянная замена', 'Статус', 'Инициатор']))
                            @continue
                        @endif
                        @if($property['sorting'])
                            @if(is_array($property['field']) && isset($property['sort_name']))
                                <th class="th-sm text-center align-top">
                                    <div class="sorting-header"><div class="header-name"></div><div>@sortablelink($property['sort_name'], $property['header'], [], ['title' => 'Сортировать', 'class' => 'sort-button'])</div></div>
                                </th>
                            @elseif(is_array($property['field']))
                                @php
                                    $full_field = implode('.', $property['field']);
                                @endphp
                                <th class="th-sm text-center align-top">
                                    <div class="sorting-header"><div class="header-name"></div><div> @sortablelink($full_field, $property['header'], [], ['title' => 'Сортировать', 'class' => 'sort-button'])</div></div>
                                </th>
                            @else
                                <th class="th-sm text-center align-top">
                                    <div class="sorting-header"><div class="header-name"></div><div> @sortablelink($property['field'], $property['header'], [], ['title' => 'Сортировать', 'class' => 'sort-button'])</div></div>
                                </th>
                            @endif
                        @else
                            <th class="th-sm text-center align-top">{{ $property['header'] }}</th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if(isset($data['my_requests']))
                    @foreach($data['my_requests'] as $instance)
                        @php
                            $replacement_request_status_colors = config('enum.replacement_request_status_colors');
                            $status_color = $replacement_request_status_colors[$instance->status_id];
                        @endphp
                        <tr style="background-color: {{ $status_color }}">
                            <td>
                                @if ($instance->status_id == $replacement_request_status_ids['in drafting'])
                                    <a class="" href="{{ route('replacement-request-update', ['updating_id' => $instance->id, 'is_sent' => 1]) }}" title="Отправить">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                                        </svg>
                                    </a>
                                @elseif ($instance->status_id == $replacement_request_status_ids['in_consent_waiting']
                                         || $instance->status_id == $replacement_request_status_ids['in_permission_waiting']
                                         || $instance->status_id == $replacement_request_status_ids['permitted'])
                                    <a class="" href="{{ route('replacement-request-update', ['updating_id' => $instance->id, 'is_cancelled' => 1]) }}" title="Отменить">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-slash-circle-fill" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.646-2.646a.5.5 0 0 0-.708-.708l-6 6a.5.5 0 0 0 .708.708l6-6z"/>
                                        </svg>
                                    </a>
                                @endif
                            </td>
                            @foreach($data['table_properties'] as $property)
                                @php $field = $property['field'] @endphp
                                @if(is_array($field))
                                    @php
                                        $value = $instance;
                                        foreach ($field as $part) {
                                            $value = $value->$part;
                                            if (!is_object($value)) {
                                                break;
                                            }
                                        }
                                    @endphp
                                    <td class="regular-cell">{{ $value }}</td>
                                @elseif($field == 'full_name')
                                    <td class="regular-cell"><a href="{{ route('teacher-schedule', ['schedule_teacher_id' => $instance->id]) }}" title="Расписание преподавателя">{{ $instance->$field }}</a></td>
                                @else
                                    <td class="regular-cell">{{ $instance->$field }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <h2>Ко мне просьбы о замене</h2>
        <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="th-sm text-center align-top"></th>
                    @foreach($data['table_properties'] as $property)
                        @if(in_array($property['header'], ['День недели', 'Пара', 'Аудитория', 'Преподаватель']))
                            @continue
                        @endif
                        @php
                            $rowspan = '';
                            $colspan = '';
                            $header = $property['header'];
                            if (in_array($property['header'], ['Группа(ы)', 'Постоянная замена', 'Статус', 'Инициатор'])) {
                                $rowspan = 2;
                            }
                            if ($property['header'] == 'Дата') {
                                $colspan = 5;
                                if ($property['field'] == 'replaceable_date') {
                                    $header = 'Заменяемое занятие';
                                } else {
                                    $header = 'Заменяющее занятие';
                                }
                                $was_first_lesson = true;
                            }
                        @endphp
                        <th class="th-sm text-center align-top" rowspan="{{ $rowspan }}" colspan="{{ $colspan }}">{{ $header }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th class="th-sm text-center align-top"></th>
                    @foreach($data['table_properties'] as $property)
                        @if(in_array($property['header'], ['Группа(ы)', 'Постоянная замена', 'Статус', 'Инициатор']))
                            @continue
                        @endif
                        @if($property['sorting'])
                            @if(is_array($property['field']) && isset($property['sort_name']))
                                <th class="th-sm text-center align-top">
                                    <div class="sorting-header"><div class="header-name"></div><div>@sortablelink($property['sort_name'], $property['header'], [], ['title' => 'Сортировать', 'class' => 'sort-button'])</div></div>
                                </th>
                            @elseif(is_array($property['field']))
                                @php
                                    $full_field = implode('.', $property['field']);
                                @endphp
                                <th class="th-sm text-center align-top">
                                    <div class="sorting-header"><div class="header-name"></div><div> @sortablelink($full_field, $property['header'], [], ['title' => 'Сортировать', 'class' => 'sort-button'])</div></div>
                                </th>
                            @else
                                <th class="th-sm text-center align-top">
                                    <div class="sorting-header"><div class="header-name"></div><div> @sortablelink($property['field'], $property['header'], [], ['title' => 'Сортировать', 'class' => 'sort-button'])</div></div>
                                </th>
                            @endif
                        @else
                            <th class="th-sm text-center align-top">{{ $property['header'] }}</th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if(isset($data['to_me_requests']))
                    @foreach($data['to_me_requests'] as $instance)
                        @php
                            $replacement_request_status_colors = config('enum.replacement_request_status_colors');
                            $status_color = $replacement_request_status_colors[$instance->status_id];
                        @endphp
                        <tr style="background-color: {{ $status_color }}">
                            <td>
                                @if ($instance->status_id == $replacement_request_status_ids['in_consent_waiting'])
                                    <a class="" href="{{ route('replacement-request-update', ['updating_id' => $instance->id, 'is_agreed' => 1]) }}" title="Согласиться">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square" viewBox="0 0 16 16">
                                            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                            <path d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.235.235 0 0 1 .02-.022z"/>
                                        </svg>
                                    </a>
                                @endif    
                                @if ($instance->status_id == $replacement_request_status_ids['in_consent_waiting']
                                         || $instance->status_id == $replacement_request_status_ids['in_permission_waiting']
                                         || $instance->status_id == $replacement_request_status_ids['permitted'])
                                    <a class="" href="{{ route('replacement-request-update', ['updating_id' => $instance->id, 'is_declined' => 1]) }}" title="Отклонить">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                            <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                                        </svg>
                                    </a>
                                @endif
                            </td>
                            @foreach($data['table_properties'] as $property)
                                @php $field = $property['field'] @endphp
                                @if(is_array($field))
                                    @php
                                        $value = $instance;
                                        foreach ($field as $part) {
                                            $value = $value->$part;
                                            if (!is_object($value)) {
                                                break;
                                            }
                                        }
                                    @endphp
                                    <td class="regular-cell">{{ $value }}</td>
                                @elseif($field == 'full_name')
                                    <td class="regular-cell"><a href="{{ route('teacher-schedule', ['schedule_teacher_id' => $instance->id]) }}" title="Расписание преподавателя">{{ $instance->$field }}</a></td>
                                @else
                                    <td class="regular-cell">{{ $instance->$field }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
@endsection
