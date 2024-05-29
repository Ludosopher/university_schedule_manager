{{-- extends('teacher.teachers', 'lesson.lessons', 'group.grous', 'replacement_request.replacement_requests') --}}
<h4>{{ __('header.find') }}</h4>
<form method="POST" action="{{ route($data['appelation_plural_name']) }}">
@csrf
    @if(isset($data['filter_form_fields']))
        @foreach($data['filter_form_fields'] as $field)
            @if($field['type'] == 'between')
                @php $field_name = $field['name']; @endphp
                <h6>{{ __('form.'.$field['name']) }}</h6>
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
                        <label class="form-label">{{ __('form.'.$field['name']) }}<span class="settings-green-star">*</span></label>
                        <select multiple size="{{ $field['multiple_options']['size'] }}" name="{{ $field_name }}[]" class="form-select" aria-label="Default select example">
                    @else
                        <label for="{{ $field_name }}" class="form-label">{{ __('form.'.$field['name']) }}</label>
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