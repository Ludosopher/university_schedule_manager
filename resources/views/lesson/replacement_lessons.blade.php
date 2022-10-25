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
                                    <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
                                    <select name="{{ $field_name }}" class="form-select filter-select" aria-label="Default select example">
                                                <option selected value=""></option>
                                        @foreach($data[$field['plural_name']] as $value)
                                            @if(old($field_name) !== null && old($field_name) == (is_object($value) ? $value->id : $value['id']))
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
                    <button type="submit" class="btn btn-primary form-button">Показать</button>
                </form>
            </div>
            <div class="getAllRight">              
                <h1>Варианты замены</h1>
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
                                    @else
                                        <td class="regular-cell">{{ is_array($lesson[$field]) ? $lesson[$field]['name'] : $lesson[$field] }}</td>    
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            @foreach($data['table_properties'] as $property)
                                <th class="th-sm text-center align-top">{{ $property['header'] }}</th>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div> 
@endsection    