@extends('layouts.app')
@section('content')
    <div class="container">
        @if (isset($data['new_instance_name']))
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                Данные группы {{ $data['new_instance_name'] }} добавлены.
            </div>
        @endif
        <div class="external-form-container">
            <div class="internal-form-container">
                @if(isset($data['updating_instance']))
                <h2>Обновление данных группы</h2> 
                @else
                    <h2>Добавление новой группы</h2>
                @endif
                
                <form method="POST" action="{{ route('group-add') }}">
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
                                    <label for="{{ $field_name }}" class="form-label">{{ $field['header'] }}</label>
                                    <select name="{{ $field_name }}" class="form-select" aria-label="Default select example">
                                        @foreach($data[$field['plural_name']] as $value)
                                            @if(old($field_name) !== null && old($field_name) == $value->id)
                                                <option selected value="{{ $value->id }}">{{ $value->name }}</option>
                                            @elseif(isset($data['updating_instance']) && $data['updating_instance']->$field_name == $value->id)
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

{{-- <div class="mb-3">
                        <label for="gender" class="form-label">Пол</label>
                        <select name="gender" class="form-select" aria-label="Default select example">
                            @foreach($data['genders'] as $gender)
                                @if(isset($data['old_data']) && $data['old_data']['gender'] == $gender)
                                    <option selected value="{{ $gender}}">{{ ucfirst($gender) }}</option>
                                @elseif(isset($data['updating_instance']) && $data['updating_instance']->gender == $gender)
                                    <option selected value="{{ $gender}}">{{ ucfirst($gender) }}</option>
                                @else
                                    <option value="{{ $gender }}">{{ ucfirst($gender) }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if (isset($data['errors']) && $data['errors']->has('gender'))
                            @foreach($data['errors']->get('gender') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Фамилия</label>
                        <input name="last_name" type="text" class="form-control form-control-sm" id="last_name" value="{{isset($data['old_data']) ? $data['old_data']['last_name'] : (isset($data['updating_instance']) ? $data['updating_instance']->last_name : '') }}">
                        @if (isset($data['errors']) && $data['errors']->has('last_name'))
                            @foreach($data['errors']->get('last_name') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">Имя</label>
                        <input name="first_name" type="text" class="form-control form-control-sm" id="first_name" aria-describedby="emailHelp" value="{{isset($data['old_data']) ? $data['old_data']['first_name'] : (isset($data['updating_instance']) ? $data['updating_instance']->first_name : '') }}">
                        @if (isset($data['errors']) && $data['errors']->has('first_name'))
                            @foreach($data['errors']->get('first_name') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="patronymic" class="form-label">Отчество</label>
                        <input name="patronymic" type="text" class="form-control form-control-sm" id="patronymic" value="{{isset($data['old_data']) ? $data['old_data']['patronymic'] : (isset($data['updating_instance']) ? $data['updating_instance']->patronymic : '') }}">
                        @if (isset($data['errors']) && $data['errors']->has('patronymic'))
                            @foreach($data['errors']->get('patronymic') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="birth_year" class="form-label">Год рождения</label>
                        <input name="birth_year" type="number" min="1900" max="2099" step="1" class="form-control form-control-sm" id="birth_year" value="{{isset($data['old_data']) ? $data['old_data']['birth_year'] : (isset($data['updating_instance']) ? $data['updating_instance']->birth_year : '') }}">
                        @if (isset($data['errors']) && $data['errors']->has('birth_year'))
                            @foreach($data['errors']->get('birth_year') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input name="phone" type="text" class="form-control form-control-sm" id="phone" value="{{isset($data['old_data']) ? $data['old_data']['phone'] : (isset($data['updating_instance']) ? $data['updating_instance']->phone : '') }}">
                        @if (isset($data['errors']) && $data['errors']->has('phone'))
                            @foreach($data['errors']->get('phone') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Электронная почта</label>
                        <input name="email" type="email" class="form-control form-control-sm" id="email" value="{{isset($data['old_data']) ? $data['old_data']['email'] : (isset($data['updating_instance']) ? $data['updating_instance']->email : '') }}">
                        @if (isset($data['errors']) && $data['errors']->has('email'))
                            @foreach($data['errors']->get('email') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif 
                    </div>
                    <div class="mb-3">
                        <label for="faculty_id" class="form-label">Факультет</label>
                        <select name="faculty_id" class="form-select" aria-label="Default select example">
                            @foreach($data['faculties'] as $faculty)
                                @if(isset($data['old_data']) && $data['old_data']['faculty_id'] == $faculty->id)
                                    <option selected value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @elseif(isset($data['updating_instance']) && $data['updating_instance']->faculty_id == $faculty->id)
                                    <option selected value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @else
                                    <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if (isset($data['errors']) && $data['errors']->has('faculty_id'))
                            @foreach($data['errors']->get('faculty_id') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="department_id" class="form-label">Кафедра</label>
                        <select name="department_id" class="form-select" aria-label="Default select example">
                            @foreach($data['departments'] as $department)
                                @if(isset($data['old_data']) && $data['old_data']['department_id'] == $department->id)
                                    <option selected value="{{ $department->id }}">{{ $department->name }}</option>
                                @elseif(isset($data['updating_instance']) && $data['updating_instance']->department_id == $department->id)
                                    <option selected value="{{ $department->id }}">{{ $department->name }}</option>
                                @else
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if (isset($data['errors']) && $data['errors']->has('department_id'))
                            @foreach($data['errors']->get('department_id') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="professional_level_id" class="form-label">Профессиональный уровень</label>
                        <select name="professional_level_id" class="form-select" aria-label="Default select example">
                            @foreach($data['professional_levels'] as $professional_level)
                                @if(isset($data['old_data']) && $data['old_data']['professional_level_id'] == $professional_level->id)
                                    <option selected value="{{ $professional_level->id }}">{{ $professional_level->name }}</option>
                                @elseif(isset($data['updating_instance']) && $data['updating_instance']->professional_level_id == $professional_level->id)
                                    <option selected value="{{ $professional_level->id }}">{{ $professional_level->name }}</option>
                                @else
                                    <option value="{{ $professional_level->id }}">{{ $professional_level->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if (isset($data['errors']) && $data['errors']->has('professional_level_id'))
                            @foreach($data['errors']->get('professional_level_id') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="position_id" class="form-label">Должность</label>
                        <select name="position_id" class="form-select" aria-label="Default select example">
                            @foreach($data['positions'] as $position)
                                @if(isset($data['old_data']) && $data['old_data']['position_id'] == $position->id)
                                    <option selected value="{{ $position->id }}">{{ $position->name }}</option>
                                @elseif(isset($data['updating_instance']) && $data['updating_instance']->position_id == $position->id)
                                    <option selected value="{{ $position->id }}">{{ $position->name }}</option>
                                @else
                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if (isset($data['errors']) && $data['errors']->has('position_id'))
                            @foreach($data['errors']->get('position_id') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div> --}}
    