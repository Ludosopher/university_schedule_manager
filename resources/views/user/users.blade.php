@extends('layouts.app')
@section('content')
    <div class="container">
        @includeIf('parts.notices.errors_instance_delete')
        @includeIf('parts.notices.response')
        <div class="getAllContainer">
            <div class="getAllLeft">
                <h4>Найти</h4>
                <form method="POST" action="{{ route('users') }}">
                @csrf
                    @if(isset($data['filter_form_fields']))
                        @foreach($data['filter_form_fields'] as $field)
                            @if($field['type'] == 'between')
                                @php $field_name = $field['name']; @endphp
                                <h6>{{ __('form.'.$field['name']) }}</h6>
                                <div class="birthYear">
                                    <div class="integer-input-div">
                                        <label for="{{$field_name}}_from" class="form-label">От</label>
                                        <input name="{{$field_name}}_from" type="number" min="{{ $field['min_value'] }}" max="{{ $field['max_value'] }}" step="{{ $field['step'] }}" class="form-control form-control-sm integer-input" id="{{$field_name}}_from" value="{{ old($field_name.'_from') !== null && count(request()->all()) ? old($field_name.'_from') : '' }}">
                                        @if ($errors !== null && $errors->has($field_name.'_from'))
                                            @foreach($errors->get($field_name.'_from') as $error)
                                                <div class="validationErrorText">{{ $error }}</div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="integer-input-div">
                                        <label for="{{$field_name}}_to" class="form-label">До</label>
                                        <input name="{{$field_name}}_to" type="number" min="{{ $field['min_value'] }}" max="{{ $field['max_value'] }}" step="{{ $field['step'] }}" class="form-control form-control-sm integer-input" id="{{$field_name}}_to" value="{{ old($field_name.'_to') !== null && count(request()->all()) ? old($field_name.'_to') : '' }}">
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
                                        <label class="form-label">{{ __('form.'.$field['name']) }}<span class="settings-green-star">*</span></label>
                                        <select multiple size="{{ $field['multiple_options']['size'] }}" name="{{ $field_name }}[]" class="form-select" aria-label="Default select example">
                                    @else
                                        <label for="{{ $field_name }}" class="form-label">{{ __('form.'.$field['name']) }}</label>
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
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        @if(old($field_name) !== null && count(request()->all()))
                                            <input class="form-check-input" name="{{ $field_name }}" type="checkbox" id="{{ $field_name }}" value="{{ true }}" checked>
                                        @else
                                            <input class="form-check-input" name="{{ $field_name }}" type="checkbox" id="{{ $field_name }}" value="{{ true }}">
                                        @endif
                                        <label class="form-check-label" for="{{ $field_name }}">{{ __('form.'.$field['name']) }}</label>
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
                                    <label for="{{ $field_name }}" class="form-label">{{ __('form.'.$field['name']) }}</label>
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
                <h1>{{ __('header.users') }}</h1>
                <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="th-sm text-center align-top"></th>
                            @foreach($data['table_properties'] as $property)
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
                            <tr>
                                <td>
                                    <a class="" href="{{ route('user-form', ['updating_id' => $instance->id]) }}" title="Изменить">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                        </svg>
                                    </a>
                                    <a class="" href="{{ route('user-delete', ['deleting_id' => $instance->id]) }}" title="Удалить">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                        </svg>
                                    </a>
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
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="th-sm text-center align-top"></th>
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
