@extends('layouts.app')
@section('content')
    <div class="container">
        @if (isset($data['deleted_message']))
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ $data['deleted_message'] }}
            </div>
        @endif
        @if (isset($data['not_found_message']))
            <div class="alertFail">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ $data['not_found_message'] }}
            </div>
        @endif
        @if (isset($data['updated_instance_name']))
            <div class="alertAccess">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                Данные преподавателя {{ $data['updated_instance_name'] }} обновлены.
            </div>
        @endif
        <div class="getAllContainer">
            <div class="getAllLeft">
                <h4>Найти</h4>
                <form method="POST" action="{{ route('teachers-filter') }}">
                @csrf
                    @if(isset($data['filter_form_fields']))
                        @foreach($data['filter_form_fields'] as $field)
                            @if($field['type'] == 'between')
                                @php $field_name = $field['name']; @endphp
                                <h6>{{ $field['header'] }}</h6>
                                <div class="birthYear">
                                    <div>
                                        <label for="{{$field_name}}_from" class="form-label">От</label>
                                        <input name="{{$field_name}}_from" type="number" min="{{ $field['min_value'] }}" max="{{ $field['max_value'] }}" step="{{ $field['step'] }}" class="form-control form-control-sm" id="{{$field_name}}_from" value="{{ isset($data['prev_request'][$field_name.'_from']) ? $data['prev_request'][$field_name.'_from'] : '' }}">
                                        @if ($errors !== null && $errors->has($field_name.'_from'))
                                            @foreach($errors->get($field_name.'_from') as $error)
                                                <div class="validationErrorText">{{ $error }}</div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div>
                                        <label for="{{$field_name}}_to" class="form-label">До</label>
                                        <input name="{{$field_name}}_to" type="number" min="{{ $field['min_value'] }}" max="{{ $field['max_value'] }}" step="{{ $field['step'] }}" class="form-control form-control-sm" id="{{$field_name}}_to" value="{{ isset($data['prev_request'][$field_name.'_to']) ? $data['prev_request'][$field_name.'_to'] : '' }}">
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
                                            @if(isset($data['prev_request'][$field_name]) && $data['prev_request'][$field_name] == $value->id)
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
                                    <input style="width: 90%;" name="{{ $field_name }}" type="{{ $field['input_type'] }}" class="form-control form-control-sm" id="{{ $field_name }}" value="{{ isset($data['prev_request'][$field_name]) ? $data['prev_request'][$field_name] : '' }}">
                                    @if ($errors !== null && $errors->has($field_name))
                                        @foreach($errors->get($field_name) as $error)
                                            <div class="validationErrorText">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>    
                            @endif    
                        @endforeach    
                    @endif
                    
                    <button type="submit" class="btn btn-primary">Показать</button>
                </form>
            </div>
            <div class="getAllRight">
                <h1>Преподаватели</h1>
                <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="th-sm text-center align-top"></th>
                            @foreach($data['headers'] as $header)
                                <th class="th-sm text-center align-top">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['instances'] as $instance)
                            <tr>
                                <td>
                                    <a class="" href="{{ route('teacher-update', ['updating_id' => $instance->id]) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                        </svg>
                                    </a>
                                    <a class="" href="{{ route('teacher-delete', ['deleting_id' => $instance->id]) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                        </svg>
                                    </a>
                                </td>
                                @foreach($data['fields'] as $field)
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
                            @foreach($data['headers'] as $header)
                                <th class="th-sm text-center align-top">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
                <nav aria-label="Page navigation example" class="nav-pagination">
                    <ul class="pagination">
                        <li class="page-item">
                            <form id="page-to-left" method="post" action="{{ route('teachers-filter') }}">
                            @csrf
                                <input type="hidden" name="page_number" value="{{ old('page_number') !== null && old('page_number') - 1 > 0 ? old('page_number') - 1 : 1 }}">
                                <input type="hidden" name="prev_request" value="{{ json_encode($data['prev_request']) }}">
                            </form>
                            <a class="page-link" onclick="document.getElementById('page-to-left').submit();" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                        @for($i = 1; $i <= $data['pages_number']; $i++)
                            @if($data['current_page'] == $i)
                                <li class="page-item">
                                    <form id="page-number{{ $i }}" method="post" action="{{ route('teachers-filter') }}">
                                    @csrf
                                        <input type="hidden" name="page_number" value="{{ $i }}">
                                        <input type="hidden" name="prev_request" value="{{ json_encode($data['prev_request']) }}">
                                    </form>
                                    <a class="page-link" style="background: rgb(235,235,235);" onclick="document.getElementById('page-number{{ $i }}').submit();">
                                        {{ $i }} 
                                    </a>
                                </li>    
                            @else
                                <li class="page-item">
                                    <form id="page-number{{ $i }}" method="post" action="{{ route('teachers-filter') }}">
                                    @csrf
                                        <input type="hidden" name="page_number" value="{{ $i }}">
                                        <input type="hidden" name="prev_request" value="{{ json_encode($data['prev_request']) }}">
                                    </form>
                                    <a class="page-link" onclick="document.getElementById('page-number{{ $i }}').submit();">
                                        {{ $i }} 
                                    </a>
                                </li>
                            @endif
                        @endfor
                        <li class="page-item">
                            <form id="page-to-right" method="post" action="{{ route('teachers-filter') }}">
                            @csrf
                                <input type="hidden" name="page_number" value="{{ old('page_number') !== null && old('page_number') + 1 < $data['pages_number'] ? old('page_number') + 1 : $data['pages_number'] }}">
                                <input type="hidden" name="prev_request" value="{{ json_encode($data['prev_request']) }}">
                            </form>
                            <a class="page-link" onclick="document.getElementById('page-to-right').submit();" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div> 
@endsection

{{-- <div class="mb-3">
                        <label for="full_name" class="form-label">Фамилия, Имя или Отчество</label>
                        <input name="full_name" type="text" class="form-control form-control-sm" id="full_name" value="{{ Session::has('prev_request') ? unserialize(session('prev_request'))['full_name'] : '' }}">
                        @if (Session::has('errors') && unserialize(session('errors'))->has('full_name'))
                            @foreach(unserialize(session('errors'))->get('full_name') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <h6>Год рождения</h6>
                        <div class="birthYear">
                            <div>
                                <label for="birth_year_from" class="form-label">От</label>
                                <input name="birth_year_from" type="number" min="1900" max="2099" step="1" class="form-control form-control-sm" id="birth_year_from" value="{{ Session::has('prev_request') ? unserialize(session('prev_request'))['birth_year_from'] : '' }}">
                                @if (Session::has('errors') && unserialize(session('errors'))->has('birth_year_from'))
                                    @foreach(unserialize(session('errors'))->get('birth_year_from') as $error)
                                        <div class="validationErrorText">{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                            <div>
                                <label for="birth_year_to" class="form-label">До</label>
                                <input name="birth_year_to" type="number" min="1900" max="2099" step="1" class="form-control form-control-sm" id="birth_year_to" value="{{ Session::has('prev_request') ? unserialize(session('prev_request'))['birth_year_to'] : '' }}">
                                @if (Session::has('errors') && unserialize(session('errors'))->has('birth_year_to'))
                                    @foreach(unserialize(session('errors'))->get('birth_year_to') as $error)
                                        <div class="validationErrorText">{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="faculty_id" class="form-label">Факультет</label>
                        <select name="faculty_id" class="form-select facultyId" aria-label="Default select example">
                                    <option selected value=""></option>
                            @foreach($data['faculties'] as $faculty)
                                @if(Session::has('prev_request') && unserialize(session('prev_request'))['faculty_id'] == $faculty->id)
                                    <option selected value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @else
                                    <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if (Session::has('errors') && unserialize(session('errors'))->has('faculty_id'))
                            @foreach(unserialize(session('errors'))->get('faculty_id') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="department_id" class="form-label">Кафедра</label>
                        <select name="department_id" class="form-select departmentId" aria-label="Default select example">
                                    <option selected value=""></option>
                            @foreach($data['departments'] as $department)
                                @if(Session::has('prev_request') && unserialize(session('prev_request'))['department_id'] == $department->id)
                                    <option selected value="{{ $department->id }}">{{ $department->name }}</option>
                                @else
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if (Session::has('errors') && unserialize(session('errors'))->has('department_id'))
                            @foreach(unserialize(session('errors'))->get('department_id') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="professional_level_id" class="form-label">Профессиональный уровень</label>
                        <select name="professional_level_id" class="form-select" aria-label="Default select example">
                                    <option selected value=""></option>
                            @foreach($data['professional_levels'] as $professional_level)
                                @if(Session::has('prev_request') && unserialize(session('prev_request'))['professional_level_id'] == $professional_level->id)
                                    <option selected value="{{ $professional_level->id }}">{{ $professional_level->name }}</option>
                                @else
                                    <option value="{{ $professional_level->id }}">{{ $professional_level->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if (Session::has('errors') && unserialize(session('errors'))->has('professional_level_id'))
                            @foreach(unserialize(session('errors'))->get('professional_level_id') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="position_id" class="form-label">Должность</label>
                        <select name="position_id" class="form-select" aria-label="Default select example">
                                    <option selected value=""></option>
                            @foreach($data['positions'] as $position)
                                @if(Session::has('prev_request') && unserialize(session('prev_request'))['position_id'] == $position->id)
                                    <option selected value="{{ $position->id }}">{{ $position->name }}</option>
                                @else
                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if (Session::has('errors') && unserialize(session('errors'))->has('position_id'))
                            @foreach(unserialize(session('errors'))->get('position_id') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>                     --}}
    