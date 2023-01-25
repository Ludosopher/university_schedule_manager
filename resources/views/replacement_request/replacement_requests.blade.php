@extends('layouts.app')
@section('content')
    <div class="container">
        {{-- @if (isset($data['deleted_instance_name']))
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ str_replace('?', $data['deleted_instance_name'], __('teacher.teacher_removed')) }}
            </div>
        @endif
        @if (isset($data['deleting_instance_not_found']))
            <div class="alertFail">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ __('teacher.teacher_not_found') }}
            </div>
        @endif
        @if (isset($data['updated_instance_name']))
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ str_replace('?', $data['updated_instance_name'], __('teacher.teacher_updated')) }}
            </div>
        @endif
        @if($errors->any() && ($errors->has('schedule_teacher_id') || $errors->has('week_number')))
            <div class="alertFail">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ __('user_validation.invalid_input_data') }}
            </div>
        @endif --}}
        @if (\Session::has('updated_instance_name'))
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ \Session::get('updated_instance_name') }} успешно обновлена.
            </div>
        @endif
        <div class="getAllContainer">
            <div class="getAllLeft">
                <h4>Найти</h4>
                <form method="POST" action="{{ route('replacement_requests') }}">
                @csrf
                    @if(isset($data['filter_form_fields']))
                        @foreach($data['filter_form_fields'] as $field)
                            @if($field['type'] == 'between')
                                @php $field_name = $field['name']; @endphp
                                <h6>{{ $field['header'] }}</h6>
                                <div class="birthYear">
                                    <div class="integer-input-div">
                                        <label for="{{$field_name}}_from" class="form-label">От</label>
                                        <input name="{{$field_name}}_from" type="{{ $field['input_type'] }}" min="{{ $field['min_value'] }}" max="{{ $field['max_value'] }}" step="{{ $field['step'] }}" class="form-control form-control-sm integer-input" id="{{$field_name}}_from" value="{{ old($field_name.'_from') !== null && count(request()->all()) ? old($field_name.'_from') : '' }}">
                                        @if ($errors !== null && $errors->has($field_name.'_from'))
                                            @foreach($errors->get($field_name.'_from') as $error)
                                                <div class="validationErrorText">{{ $error }}</div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="integer-input-div">
                                        <label for="{{$field_name}}_to" class="form-label">До</label>
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
                                        <label class="form-label">{{ $field['header'] }}<span style="color: green;">*</span></label>
                                        <select multiple size="{{ $field['multiple_options']['size'] }}" name="{{ $field_name }}[]" class="form-select" aria-label="Default select example">
                                    @else
                                        <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
                                        <select name="{{ $field_name }}" class="form-select" aria-label="Default select example">
                                    @endif
                                        @foreach($data[$field['plural_name']] as $value)
                                            @if(old($field_name) !== null
                                                && count(request()->all())
                                                && (old($field_name) == $value->id
                                                   || (is_array(old($field_name)) && in_array($value->id, old($field_name)))))
                                                    <option selected value="{{ $value->id }}">{{ $value->name }}</option>
                                            @elseif(isset($data['updating_instance']) && $data['updating_instance']->$field_name == $value->id)
                                                <option selected value="{{ $value->id }}">{{ $value->name }}</option>
                                            @elseif(isset($data['updating_instance'])
                                                    && is_array($data['updating_instance']->$field_name)
                                                    && in_array($value->id, $data['updating_instance']->$field_name))
                                                <option selected value="{{ $value->id }}">{{ $value->name }}</option>
                                            @else
                                                <option value="{{ $value->id }}">{{ $value->name }}</option>
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
                                <div style="height: 20px;"></div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        @if(old($field_name) !== null && count(request()->all()))
                                            <input class="form-check-input" name="{{ $field_name }}" type="checkbox" id="{{ $field_name }}" value="{{ true }}" checked>
                                        @else
                                            <input class="form-check-input" name="{{ $field_name }}" type="checkbox" id="{{ $field_name }}" value="{{ true }}">
                                        @endif
                                        <label class="form-check-label" for="{{ $field_name }}">{{ $field['header'] }}</label>
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
                                    <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
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
                    <p class="form-explanation"><span style="color: green;">*</span> Для выбора нескольких полей нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора.</p>
                    <button type="submit" class="btn btn-primary form-button">Показать</button>
                </form>
            </div>
            <div class="getAllRight">
                <h1>Просьбы о замене</h1>
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
                        @foreach($data['instances'] as $instance)
                            @php
                                $replacement_request_status_ids = config('enum.replacement_request_status_ids');
                                $replacement_request_status_colors = config('enum.replacement_request_status_colors');
                                $status_color = $replacement_request_status_colors[$instance->status_id];
                            @endphp
                            @if($instance->status_id !== $replacement_request_status_ids['in drafting'])
                                <tr style="background-color: {{ $status_color }}">
                                    <td>
                                        @if ($instance->status_id == $replacement_request_status_ids['in_permission_waiting']
                                            || $instance->status_id == $replacement_request_status_ids['not_permitted'])
                                            <a class="" href="{{ route('replacement-request-update', ['updating_id' => $instance->id, 'is_permitted' => 1]) }}" title="Разрешить">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up" viewBox="0 0 16 16">
                                                    <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2.144 2.144 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a9.84 9.84 0 0 0-.443.05 9.365 9.365 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111L8.864.046zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a8.908 8.908 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.224 2.224 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.866.866 0 0 1-.121.416c-.165.288-.503.56-1.066.56z"/>
                                                </svg>
                                            </a>
                                        @endif
                                        @if ($instance->status_id == $replacement_request_status_ids['in_permission_waiting']
                                            || $instance->status_id == $replacement_request_status_ids['permitted'])
                                            <a class="" href="{{ route('replacement-request-update', ['updating_id' => $instance->id, 'is_not_permitted' => 1]) }}" title="Не разрешить">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-down" viewBox="0 0 16 16">
                                                    <path d="M8.864 15.674c-.956.24-1.843-.484-1.908-1.42-.072-1.05-.23-2.015-.428-2.59-.125-.36-.479-1.012-1.04-1.638-.557-.624-1.282-1.179-2.131-1.41C2.685 8.432 2 7.85 2 7V3c0-.845.682-1.464 1.448-1.546 1.07-.113 1.564-.415 2.068-.723l.048-.029c.272-.166.578-.349.97-.484C6.931.08 7.395 0 8 0h3.5c.937 0 1.599.478 1.934 1.064.164.287.254.607.254.913 0 .152-.023.312-.077.464.201.262.38.577.488.9.11.33.172.762.004 1.15.069.13.12.268.159.403.077.27.113.567.113.856 0 .289-.036.586-.113.856-.035.12-.08.244-.138.363.394.571.418 1.2.234 1.733-.206.592-.682 1.1-1.2 1.272-.847.283-1.803.276-2.516.211a9.877 9.877 0 0 1-.443-.05 9.364 9.364 0 0 1-.062 4.51c-.138.508-.55.848-1.012.964l-.261.065zM11.5 1H8c-.51 0-.863.068-1.14.163-.281.097-.506.229-.776.393l-.04.025c-.555.338-1.198.73-2.49.868-.333.035-.554.29-.554.55V7c0 .255.226.543.62.65 1.095.3 1.977.997 2.614 1.709.635.71 1.064 1.475 1.238 1.977.243.7.407 1.768.482 2.85.025.362.36.595.667.518l.262-.065c.16-.04.258-.144.288-.255a8.34 8.34 0 0 0-.145-4.726.5.5 0 0 1 .595-.643h.003l.014.004.058.013a8.912 8.912 0 0 0 1.036.157c.663.06 1.457.054 2.11-.163.175-.059.45-.301.57-.651.107-.308.087-.67-.266-1.021L12.793 7l.353-.354c.043-.042.105-.14.154-.315.048-.167.075-.37.075-.581 0-.211-.027-.414-.075-.581-.05-.174-.111-.273-.154-.315l-.353-.354.353-.354c.047-.047.109-.176.005-.488a2.224 2.224 0 0 0-.505-.804l-.353-.354.353-.354c.006-.005.041-.05.041-.17a.866.866 0 0 0-.121-.415C12.4 1.272 12.063 1 11.5 1z"/>
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
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            @if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_moderator))
                                {{-- <th class="th-sm text-center align-top"></th> --}}
                            @endif
                            @foreach($data['table_properties'] as $property)
                                <th class="th-sm text-center align-top">{{ $property['header'] }}</th>
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
