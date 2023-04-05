@extends('layouts.app')
@section('content')
    <div class="container">
        @if (\Session::has('response'))
            @if(\Session::get('response')['success'])
                <div class="alertAccess">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    {{ \Session::get('response')['message'] }}
                </div>
            @else
                <div class="alertFail">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    {{ \Session::get('response')['message'] }}
                </div>
            @endif
        @endif
        <div class="getAllContainer">
            <div class="getAllLeft">
                <h4>{{ __('header.find') }}</h4>
                <form method="POST" action="{{ route('replacement_requests') }}">
                @csrf
                    @if(isset($data['filter_form_fields']))
                        @foreach($data['filter_form_fields'] as $field)
                            @if($field['type'] == 'between')
                                @php $field_name = $field['name']; @endphp
                                <h6>{{ __('replacement_request_form.'.$field['name']) }}</h6>
                                <div class="birthYear">
                                    <div class="integer-input-div">
                                        <label for="{{$field_name}}_from" class="form-label">{{ __('form.from') }}</label>
                                        <input name="{{$field_name}}_from" type="{{ $field['input_type'] }}" min="{{ $field['min_value'] }}" max="{{ $field['max_value'] }}" step="{{ $field['step'] }}" class="form-control form-control-sm integer-input" id="{{$field_name}}_from" value="{{ old($field_name.'_from') !== null && count(request()->all()) ? old($field_name.'_from') : '' }}">
                                        @if ($errors !== null && $errors->has($field_name.'_from'))
                                            @foreach($errors->get($field_name.'_from') as $error)
                                                <div class="validationErrorText">{{ $error }}</div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="integer-input-div">
                                        <label for="{{$field_name}}_to" class="form-label">{{ __('form.to') }}</label>
                                        <input name="{{$field_name}}_to" type="{{ $field['input_type'] }}" min="{{ $field['min_value'] }}" max="{{ $field['max_value'] }}" step="{{ $field['step'] }}" class="form-control form-control-sm integer-input" id="{{$field_name}}_to" value="{{ old($field_name.'_to') !== null && count(request()->all()) ? old($field_name.'_to') : '' }}">
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
                                        <label class="form-label">{{ __('replacement_request_form.'.$field['name']) }}<span class="settings-green-star">*</span></label>
                                        <select multiple size="{{ $field['multiple_options']['size'] }}" name="{{ $field_name }}[]" class="form-select" aria-label="Default select example">
                                    @else
                                        <label for="{{ $field_name }}" class="form-label">{{ __('replacement_request_form.'.$field['name']) }}</label>
                                        <select name="{{ $field_name }}" class="form-select" aria-label="Default select example">
                                    @endif
                                        @foreach($data[$field['plural_name']] as $value)
                                            @php
                                                $localized_value = $field['is_localized'] ? __('dictionary.'.$value->name) : $value->name;
                                            @endphp
                                            @if(old($field_name) !== null
                                                && count(request()->all())
                                                && (old($field_name) == $value->id
                                                   || (is_array(old($field_name)) && in_array($value->id, old($field_name)))))
                                                    <option selected value="{{ $value->id }}">{{ $localized_value }}</option>
                                            @elseif(isset($data['updating_instance']) && $data['updating_instance']->$field_name == $value->id)
                                                <option selected value="{{ $value->id }}">{{ $localized_value }}</option>
                                            @elseif(isset($data['updating_instance'])
                                                    && is_array($data['updating_instance']->$field_name)
                                                    && in_array($value->id, $data['updating_instance']->$field_name))
                                                <option selected value="{{ $value->id }}">{{ $localized_value }}</option>
                                            @else
                                                <option value="{{ $value->id }}">{{ $localized_value }}</option>
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
                            @if($field['type'] == 'switch')
                                @php $field_name = $field['name']; @endphp
                                <div class="rp-switch"></div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        @if(old($field_name) !== null && count(request()->all()))
                                            <input class="form-check-input" name="{{ $field_name }}" type="checkbox" id="{{ $field_name }}" value="{{ true }}" checked>
                                        @else
                                            <input class="form-check-input" name="{{ $field_name }}" type="checkbox" id="{{ $field_name }}" value="{{ true }}">
                                        @endif
                                        <label class="form-check-label" for="{{ $field_name }}">{{ __('replacement_request_form.'.$field['name']) }}</label>
                                    </div>
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
                                    <label for="{{ $field_name }}" class="form-label">{{ __('replacement_request_form.'.$field['name']) }}</label>
                                    <input name="{{ $field_name }}" type="{{ $field['input_type'] }}" class="form-control form-control-sm filter-input" id="{{ $field_name }}" value="{{ old($field_name) !== null && count(request()->all()) ? old($field_name) : '' }}">
                                    @if ($errors !== null && $errors->has($field_name))
                                        @foreach($errors->get($field_name) as $error)
                                            <div class="validationErrorText">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @endif
                    <p class="form-explanation"><span class="settings-green-star">*</span>{{ __('form.multiple_fields_select') }}</p>
                    <button type="submit" class="btn btn-primary form-button">{{ __('form.show') }}</button>
                </form>
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
