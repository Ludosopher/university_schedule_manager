@extends('layouts.app')
@section('content')
    <div class="container">
        @includeIf('parts.notices.response')
        <div class="getAllContainer">
            <div class="getAllLeft">
                @includeIf('parts.forms.find')
            </div>
            <div class="getAllRight">
                <h1>{{ __('header.replacement_requests') }}</h1>
                <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="th-sm text-center align-top" rowspan="2">Действия</th>
                            @foreach($data['table_properties'] as $property)
                                @if(in_array($property['header'], ['week_day', 'class_period', 'lesson_room', 'teacher']))
                                    @continue
                                @endif
                                @php
                                    $rowspan = '';
                                    $colspan = '';
                                    $header = __('table_header.'.$property['header']);
                                    if (in_array($property['header'], ['group', 'is_regular', 'status', 'initiator'])) {
                                        $rowspan = 2;
                                    }
                                    if ($property['header'] == 'date') {
                                        $colspan = 5;
                                        if ($property['field'] == 'replaceable_date') {
                                            $header = __('table_header.replaceable_lesson');
                                        } else {
                                            $header = __('table_header.replacing_lesson');
                                        }
                                        $was_first_lesson = true;
                                    }
                                @endphp
                                <th class="th-sm text-center align-top" rowspan="{{ $rowspan }}" colspan="{{ $colspan }}">{{ $header }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($data['table_properties'] as $property)
                                @if(in_array($property['header'], ['group', 'is_regular', 'status', 'initiator']))
                                    @continue
                                @endif
                                @if($property['sorting'])
                                    @if(is_array($property['field']) && isset($property['sort_name']))
                                        <th class="th-sm text-center align-top">
                                            <div class="sorting-header"><div class="header-name"></div><div>@sortablelink($property['sort_name'], __('table_header.'.$property['header']), [], ['title' => __('title.sort'), 'class' => 'sort-button'])</div></div>
                                        </th>
                                    @elseif(is_array($property['field']))
                                        @php
                                            $full_field = implode('.', $property['field']);
                                        @endphp
                                        <th class="th-sm text-center align-top">
                                            <div class="sorting-header"><div class="header-name"></div><div> @sortablelink($full_field, __('table_header.'.$property['header']), [], ['title' => __('title.sort'), 'class' => 'sort-button'])</div></div>
                                        </th>
                                    @else
                                        <th class="th-sm text-center align-top">
                                            <div class="sorting-header"><div class="header-name"></div><div> @sortablelink($property['field'], __('table_header.'.$property['header']), [], ['title' => __('title.sort'), 'class' => 'sort-button'])</div></div>
                                        </th>
                                    @endif
                                @else
                                    <th class="th-sm text-center align-top">{{ __('table_header.'.$property['header']) }}</th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['instances'] as $instance)
                            @php
                                $replacement_request_status_ids = config('enum.replacement_request_status_ids');
                                $replacement_request_status_colors = config('enum.replacement_request_status_colors');
                                $status_color = $replacement_request_status_colors[$instance->status_id];
                            @endphp
                            @if($instance->status_id !== $replacement_request_status_ids['in_drafting'])
                                <tr style="background-color: {{ $status_color }}">
                                    <td>
                                        <div class="mrp-icon-group">
                                            @if ($instance->status_id == $replacement_request_status_ids['in_permission_waiting']
                                                || $instance->status_id == $replacement_request_status_ids['not_permitted'])
                                                <a class="" href="{{ route('replacement-request-update', ['updating_id' => $instance->id, 'is_permitted' => 1]) }}" title="Разрешить">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                                        <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
                                                        <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if ($instance->status_id == $replacement_request_status_ids['in_permission_waiting']
                                                || $instance->status_id == $replacement_request_status_ids['permitted'])
                                                <a class="" href="{{ route('replacement-request-update', ['updating_id' => $instance->id, 'is_not_permitted' => 1]) }}" title="Запретить">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
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
                                            <td class="regular-cell">{{ \Lang::has('dictionary.'.$value) ? __('dictionary.'.$value) : $value }}</td>
                                        @elseif($field == 'full_name')
                                            <td class="regular-cell"><a href="{{ route('teacher-schedule', ['schedule_teacher_id' => $instance->id]) }}" title="Расписание преподавателя">{{ \Lang::has('dictionary.'.$instance->$field) ? __('dictionary.'.$instance->$field) : $instance->$field }}</a></td>
                                        @else
                                            <td class="regular-cell">{{ \Lang::has('dictionary.'.$instance->$field) ? __('dictionary.'.$instance->$field) : $instance->$field }}</td>
                                        @endif
                                    @endforeach
                                </tr>    
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            @if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_moderator))
                                <th class="th-sm text-center align-top">{{ __('table_header.actions') }}</th>
                            @endif
                            @foreach($data['table_properties'] as $property)
                                <th class="th-sm text-center align-top">{{ __('table_header.'.$property['header']) }}</th>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
                <div class="nav-pagination">
                    {{ $data['instances']->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
