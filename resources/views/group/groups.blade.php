@extends('layouts.app')
@section('content')
    <div class="container">
        @if (isset($data['deleted_instance_name']))
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                Данные группы {{ $data['deleted_instance_name'] }} удалены.
            </div>
        @endif
        @if (isset($data['deleting_instance_not_found']))
            <div class="alertFail">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                Такая группа не найдена.
            </div>
        @endif
        @if (isset($data['updated_instance_name']))
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                Данные группы {{ $data['updated_instance_name'] }} обновлены.
            </div>
        @endif
        <div class="getAllContainer">
            <div class="getAllLeft">
                <h4>Найти</h4>
                <form method="POST" action="{{ route('groups') }}">
                @csrf
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
                                    <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
                                    <select name="{{ $field_name }}" class="form-select facultyId" aria-label="Default select example">
                                                <option selected value=""></option>
                                        @foreach($data[$field['plural_name']] as $value)
                                            @if(old($field_name) !== null && old($field_name) == $value->id)
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
                            @if($field['type'] == 'input')
                                @php $field_name = $field['name']; @endphp
                                <div class="mb-3">
                                    <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
                                    <input style="width: 90%;" name="{{ $field_name }}" type="{{ $field['input_type'] }}" class="form-control form-control-sm" id="{{ $field_name }}" value="{{ old($field_name) !== null ? old($field_name) : '' }}">
                                    @if ($errors !== null && $errors->has($field_name))
                                        @foreach($errors->get($field_name) as $error)
                                            <div class="validationErrorText">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>    
                            @endif    
                        @endforeach    
                    @endif
                    <button type="submit" class="btn btn-primary form-button">Показать</button>
                </form>
            </div>
            <div class="getAllRight">              
                <h1>Группы</h1>
                <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="th-sm text-center align-top"></th>
                            @foreach($data['table_properties'] as $property)
                                @if($property['sorting'])
                                    @if(is_array($property['field']))
                                        @php
                                            $full_field = implode('.', $property['field']);
                                        @endphp
                                        <th class="th-sm text-center align-top">@sortablelink($full_field, $property['header'])</th>
                                    @else
                                        <th class="th-sm text-center align-top">@sortablelink($property['field'], $property['header'])</th>   
                                    @endif
                                @else
                                    <th class="th-sm text-center align-top">{{ $property['header'] }}</th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['instances'] as $instance)
                            <tr>
                                <td>
                                    <a class="" href="{{ route('group-update', ['updating_id' => $instance->id]) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                        </svg>
                                    </a>
                                    <a class="" href="{{ route('group-delete', ['deleting_id' => $instance->id]) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                        </svg>
                                    </a>
                                </td>
                                @foreach($data['table_properties'] as $property)
                                    @php $field = $property['field'] @endphp
                                    @if(is_array($field) && count($field) > 1)
                                        @php
                                            $value = $instance;
                                            foreach ($field as $part) {
                                                $value = $value->$part;
                                                if (!is_object($value)) {
                                                    break;
                                                }
                                            }
                                        @endphp
                                        <td>{{ $value }}</td>  
                                    @else
                                        <td>{{ $instance->$field }}</td>    
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="th-sm text-center align-top"></th>
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