@extends('layouts.app')
@section('content')
    <div class="container">
        @if (isset($data['new_instance_name']))
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                Данные пары {{ $data['new_instance_name'] }} добавлены.
            </div>
        @endif
        <div class="external-form-container">
            <div class="internal-form-container">
                @if(isset($data['updating_instance']))
                <h2>Обновление данных пары</h2> 
                @else
                    <h2>Добавление новой пары</h2>
                @endif
                
                <form method="POST" action="{{ route('lesson-add') }}">
                @csrf
                    @if(isset($data['updating_instance']))
                        <input type="hidden" name="updating_id" value="{{ $data['updating_instance']->id }}">    
                    @endif
                    @if(isset($data['add_form_fields']))
                        @foreach($data['add_form_fields'] as $field)
                            @if($field['type'] == 'enum-select')
                                @php $field_name = $field['name']; @endphp
                                <div class="mb-3">
                                    <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
                                    <select name="{{ $field_name }}" class="form-select" aria-label="Default select example">
                                        @foreach($data[$field['plural_name']] as $value)
                                            @if(old($field_name) !== null && old($field_name) == $value)
                                                <option selected value="{{ $value }}">{{ ucfirst($value) }}</option>
                                            @elseif(isset($data['updating_instance']) && $data['updating_instance']->$field_name == $value)
                                                <option selected value="{{ $value }}">{{ ucfirst($value) }}</option>
                                            @else
                                                <option value="{{ $value }}">{{ ucfirst($value) }}</option>
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
                            @if($field['type'] == 'objects-select')
                                @php $field_name = $field['name'].'_id'; @endphp
                                <div class="mb-3">
                                    @if(isset($field['multiple_options']) && is_array($field['multiple_options']) && $field['multiple_options']['is_multiple'])
                                        <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
                                        <p class="form-explanation">{{ $field['multiple_options']['explanation'] }}</p>
                                        <select multiple size="{{ $field['multiple_options']['size'] }}" name="{{ $field_name }}[]" class="form-select" aria-label="Default select example">    
                                    @else
                                        <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
                                        <select name="{{ $field_name }}" class="form-select" aria-label="Default select example">
                                    @endif
                                        @foreach($data[$field['plural_name']] as $value)
                                            @if(old($field_name) !== null && old($field_name) == $value->id)
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
                            @if($field['type'] == 'input')
                                @php $field_name = $field['name']; @endphp
                                <div class="mb-3">
                                    <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
                                    <input name="{{ $field_name }}" type="{{ $field['input_type'] }}" class="form-control form-control-sm" id="{{ $field_name }}" value="{{old($field_name) !== null ? old($field_name) : (isset($data['updating_instance']) ? $data['updating_instance']->$field_name : '') }}">
                                    @if ($errors !== null && $errors->has($field_name))
                                        @foreach($errors->get($field_name) as $error)
                                            <div class="validationErrorText">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>    
                            @endif
                        @endforeach
                    @endif
                    
                    <button type="submit" class="btn btn-primary">{{isset($data['updating_instance']) ? 'Обновить' : 'Добавить'}}</button>
                </form>
            </div>
        </div>
        
    </div>    
@endsection  


    