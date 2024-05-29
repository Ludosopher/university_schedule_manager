{{-- extends('teacher.add_teacher_form', 'group.add_group_form', 'lesson.add_lesson_form') --}}
@if(isset($data['updating_instance']))
<h2 class="ad_up_form_h2">{{ __('header.'.$data['appelation'].'_update') }}</h2>
@else
    <h2 class="ad_up_form_h2">{{ __('header.'.$data['appelation'].'_add') }}</h2>
@endif

<form method="POST" action="{{ route($data['appelation'].'-add-update') }}">
@csrf
    @if(isset($data['updating_instance']))
        <input type="hidden" name="updating_id" value="{{ $data['updating_instance']->id }}">
    @endif
    @if(isset($data['add_form_fields']))
        @foreach($data['add_form_fields'] as $field)
            @if($field['type'] == 'enum-select')
                @php $field_name = $field['name']; @endphp
                <div class="mb-3">
                    <label for="{{ $field_name }}" class="form-label">{{ __('form.'.$field['name']) }}
                        @if (isset($field['is_required']) && $field['is_required'])
                            <span class="settings-green-star">*</span>
                        @endif
                    </label>
                    <select name="{{ $field_name }}" class="form-select" aria-label="Default select example">
                        @foreach($data[$field['plural_name']] as $value)
                            @php
                                $localized_value = $field['is_localized'] ? __('dictionary.'.$value) : $value;
                            @endphp
                            @if(old($field_name) !== null && old($field_name) == $value)
                                <option selected value="{{ $value }}">{{ $localized_value }}</option>
                            @elseif(isset($data['updating_instance']) && $data['updating_instance']->$field_name == $value)
                                <option selected value="{{ $value }}">{{ $localized_value }}</option>
                            @else
                                <option value="{{ $value }}">{{ $localized_value }}</option>
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
                    <label for="{{ $field_name }}" class="form-label">{{ __('form.'.$field['name']) }}
                        @if (isset($field['is_required']) && $field['is_required'])
                            <span class="settings-green-star">*</span>
                        @endif
                    </label>
                    <select name="{{ $field_name }}" class="form-select" aria-label="Default select example">
                        @foreach($data[$field['plural_name']] as $value)
                            @php
                                $localized_value = $field['is_localized'] ? __('dictionary.'.$value->name) : $value->name;
                            @endphp
                            @if(old($field_name) !== null && old($field_name) == $value->id)
                                <option selected value="{{ $value->id }}">{{ $localized_value }}</option>
                            @elseif(isset($data['updating_instance']) && $data['updating_instance']->$field_name == $value->id)
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
            @if($field['type'] == 'input')
                @php $field_name = $field['name']; @endphp
                <div class="mb-3">
                    <label for="{{ $field_name }}" class="form-label">{{ __('form.'.$field['name']) }}
                        @if (isset($field['is_required']) && $field['is_required'])
                            <span class="settings-green-star">*</span>
                        @endif
                    </label>
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
    <p class="form-explanation"><span class="settings-green-star">*</span>{{ __('form.required_field') }}</p>
    <button type="submit" class="btn btn-primary">{{isset($data['updating_instance']) ? __('form.update') : __('form.add')}}</button>
</form>