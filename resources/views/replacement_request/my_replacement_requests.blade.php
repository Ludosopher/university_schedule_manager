@extends('layouts.personal')
@section('personal_content')
    <div class="container">
        @php $replacement_request_status_ids = config('enum.replacement_request_status_ids'); @endphp
        <h2>{{ __('header.my_replacement_requests') }}</h2>
        <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="th-sm text-center align-top mrp-table-header" rowspan="2">{{ __('table_header.actions') }}</th>
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
                                    <div class="sorting-header"><div class="header-name"></div><div>@sortablelink($property['sort_name'], __('table_header.'.$property['header']), [], ['title' => "{{ __('title.sort') }}", 'class' => 'sort-button'])</div></div>
                                </th>
                            @elseif(is_array($property['field']))
                                @php
                                    $full_field = implode('.', $property['field']);
                                @endphp
                                <th class="th-sm text-center align-top">
                                    <div class="sorting-header"><div class="header-name"></div><div> @sortablelink($full_field, __('table_header.'.$property['header']), [], ['title' => "{{ __('title.sort') }}", 'class' => 'sort-button'])</div></div>
                                </th>
                            @else
                                <th class="th-sm text-center align-top">
                                    <div class="sorting-header"><div class="header-name"></div><div> @sortablelink($property['field'], __('table_header.'.$property['header']), [], ['title' => "{{ __('title.sort') }}", 'class' => 'sort-button'])</div></div>
                                </th>
                            @endif
                        @else
                            <th class="th-sm text-center align-top">{{ __('table_header.'.$property['header']) }}</th>
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
                                <div class="mrp-icon-group">
                                    <form method="POST" action="{{ route('replacement-request-chat') }}" title="{{ __('title.log_in_chat') }}" target="_blank">
                                    @csrf
                                        <input type="hidden" name="replacement_request_id" value="{{ $instance->id }}">
                                        <input type="hidden" name="replacement_request_name" value="{{ $instance->name }}">
                                        <input type="hidden" name="author_id" value="{{ $data['user_id'] }}">
                                        <input type="hidden" name="author_name" value="{{ $data['user_name'] }}">
                                        <button type="submit" class="schedule-replace-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                                <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @if ($instance->status_id == $replacement_request_status_ids['in_drafting'])
                                        <form method="POST" action="{{ route('replacement-request-send') }}" title="{{ __('title.send') }}" target="_blank">
                                        @csrf
                                            <input type="hidden" name="updating_id" value="{{ $instance->id }}">
                                            <input type="hidden" name="is_sent" value="1">
                                            <input type="hidden" name="replaceable_lesson_id" value="{{ $instance->replaceable_lesson_id }}">
                                            <input type="hidden" name="replaceable_date" value="{{ $instance->replaceable_date }}">
                                            <input type="hidden" name="replacing_lesson_id" value="{{ $instance->replacing_lesson_id }}">
                                            <input type="hidden" name="replacing_date" value="{{ $instance->replacing_date }}">
                                            <input type="hidden" name="is_regular" value="{{ $instance->is_regular }}">
                                            <input type="hidden" name="replacing_teacher_id" value="{{ $instance->replacing_lesson->teacher_id }}">
                                            <button type="submit" class="schedule-replace-link">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                                                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    @if ($instance->status_id == $replacement_request_status_ids['in_consent_waiting']
                                        || $instance->status_id == $replacement_request_status_ids['in_permission_waiting']
                                        || $instance->status_id == $replacement_request_status_ids['permitted'])
                                        <a class="" href="{{ route('replacement-request-update', ['updating_id' => $instance->id, 'is_cancelled' => 1]) }}" title="{{ __('title.cancel') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-slash-circle-fill" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.646-2.646a.5.5 0 0 0-.708-.708l-6 6a.5.5 0 0 0 .708.708l6-6z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if ($instance->status_id == $replacement_request_status_ids['in_drafting']
                                        || $instance->status_id == $replacement_request_status_ids['cancelled']
                                        || $instance->status_id == $replacement_request_status_ids['declined']
                                        || $instance->status_id == $replacement_request_status_ids['completed']
                                        || $instance->status_id == $replacement_request_status_ids['not_permitted'])    
                                        <a class="" href="{{ route('replacement-request-delete', ['deleting_id' => $instance->id]) }}" title="{{ __('title.delete') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
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
                                    <td class="regular-cell"><a href="{{ route('teacher-schedule', ['schedule_teacher_id' => $instance->id]) }}" title="{{ __('title.teacher_schedule') }}">{{ \Lang::has('dictionary.'.$instance->$field) ? __('dictionary.'.$instance->$field) : $instance->$field }}</a></td>
                                @else
                                    <td class="regular-cell">{{ \Lang::has('dictionary.'.$instance->$field) ? __('dictionary.'.$instance->$field) : $instance->$field }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <h2>{{ __('header.to_me_replacement_requests') }}</h2>
        <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th class="th-sm text-center align-top mrp-table-header" rowspan="2">{{ __('table_header.actions') }}</th>
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
                                    <div class="sorting-header"><div class="header-name"></div><div>@sortablelink($property['sort_name'], __('table_header.'.$property['header']), [], ['title' => "{{ __('title.sort') }}", 'class' => 'sort-button'])</div></div>
                                </th>
                            @elseif(is_array($property['field']))
                                @php
                                    $full_field = implode('.', $property['field']);
                                @endphp
                                <th class="th-sm text-center align-top">
                                    <div class="sorting-header"><div class="header-name"></div><div> @sortablelink($full_field, __('table_header.'.$property['header']), [], ['title' => "{{ __('title.sort') }}", 'class' => 'sort-button'])</div></div>
                                </th>
                            @else
                                <th class="th-sm text-center align-top">
                                    <div class="sorting-header"><div class="header-name"></div><div> @sortablelink($property['field'], __('table_header.'.$property['header']), [], ['title' => "{{ __('title.sort') }}", 'class' => 'sort-button'])</div></div>
                                </th>
                            @endif
                        @else
                            <th class="th-sm text-center align-top">{{ __('table_header.'.$property['header']) }}</th>
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
                                <div class="mrp-icon-group">
                                    <form method="POST" action="{{ route('replacement-request-chat') }}" title="{{ __('title.log_in_chat') }}" target="_blank">
                                    @csrf
                                        <input type="hidden" name="replacement_request_id" value="{{ $instance->id }}">
                                        <input type="hidden" name="replacement_request_name" value="{{ $instance->name }}">
                                        <input type="hidden" name="author_id" value="{{ $data['user_id'] }}">
                                        <input type="hidden" name="author_name" value="{{ $data['user_name'] }}">
                                        <button type="submit" class="schedule-replace-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                                <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @if ($instance->status_id == $replacement_request_status_ids['in_consent_waiting'])
                                        <a class="" href="{{ route('replacement-request-update', ['updating_id' => $instance->id, 'is_agreed' => 1]) }}" title="{{ __('title.to_agree') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square" viewBox="0 0 16 16">
                                                <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                                <path d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.235.235 0 0 1 .02-.022z"/>
                                            </svg>
                                        </a>
                                    @endif    
                                    @if ($instance->status_id == $replacement_request_status_ids['in_consent_waiting']
                                            || $instance->status_id == $replacement_request_status_ids['in_permission_waiting']
                                            || $instance->status_id == $replacement_request_status_ids['permitted'])
                                        <a class="" href="{{ route('replacement-request-update', ['updating_id' => $instance->id, 'is_declined' => 1]) }}" title="{{ __('title.reject') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-circle" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
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
                                    <td class="regular-cell"><a href="{{ route('teacher-schedule', ['schedule_teacher_id' => $instance->id]) }}" title="{{ __('title.teacher_schedule') }}">{{ \Lang::has('dictionary.'.$instance->$field) ? __('dictionary.'.$instance->$field) : $instance->$field }}</a></td>
                                @else
                                    <td class="regular-cell">{{ \Lang::has('dictionary.'.$instance->$field) ? __('dictionary.'.$instance->$field) : $instance->$field }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
@endsection
