{{-- extends('lesson.replacement_lessons') --}}
<h4>{{ __('header.find') }}</h4>
<form method="POST" action="{{ route('lesson-replacement') }}">
@csrf
    @if(isset($data['filter_form_fields']))
        @foreach($data['filter_form_fields'] as $field)
            @if($field['type'] == 'between')
                @php $field_name = $field['name']; @endphp
                <h6>{{ __('form.'.$field['name']) }}</h6>
                <div class="birthYear">
                    <div>
                        <label for="{{$field_name}}_from" class="form-label">{{ __('form.from') }}</label>
                        <input name="{{$field_name}}_from" type="number" min="{{ $field['min_value'] }}" max="{{ $field['max_value'] }}" step="{{ $field['step'] }}" class="form-control form-control-sm" id="{{$field_name}}_from" value="{{ old($field_name.'_from') !== null ? old($field_name.'_from') : '' }}">
                        @if ($errors !== null && $errors->has($field_name.'_from'))
                            @foreach($errors->get($field_name.'_from') as $error)
                                <div class="validationErrorText">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div>
                        <label for="{{$field_name}}_to" class="form-label">{{ __('form.to') }}</label>
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
                    @if(isset($field['multiple_options']) && is_array($field['multiple_options']) && $field['multiple_options']['is_multiple'])
                        <label class="form-label">{{ __('form.'.$field['name']) }}<span class="settings-red-star">*</span></label>
                        <select multiple size="{{ $field['multiple_options']['size'] }}" name="{{ $field_name }}[]" class="form-select" aria-label="Default select example">    
                    @else
                        <label for="{{ $field_name }}" class="form-label">{{ __('form.'.$field['name']) }}</label>
                        <select name="{{ $field_name }}" class="form-select" aria-label="Default select example">
                    @endif
                        @foreach($data[$field['plural_name']] as $value)
                            @php
                                $localized_value = $field['is_localized'] ? (is_object($value) ? __('dictionary.'.$value->name) 
                                                                                                : __('dictionary.'.$value['name'])) 
                                                                            : (is_object($value) ? $value->name 
                                                                                                : $value['name']);
                            @endphp
                            @if(old($field_name) !== null
                                && (old($field_name) == (is_object($value) ? $value->id : $value['id'])
                                    || (is_array(old($field_name)) && in_array((is_object($value) ? $value->id : $value['id']), old($field_name)))))
                                    <option selected value="{{ is_object($value) ? $value->id : $value['id'] }}">{{ $localized_value }}</option>
                            @elseif(isset($data['updating_instance']) && $data['updating_instance']->$field_name == $value->id)
                                <option selected value="{{ is_object($value) ? $value->id : $value['id'] }}">{{ $localized_value }}</option>
                            @elseif(isset($data['updating_instance']) 
                                    && is_array($data['updating_instance']->$field_name) 
                                    && in_array($value->id, $data['updating_instance']->$field_name))
                                <option selected value="{{ is_object($value) ? $value->id : $value['id'] }}">{{ $localized_value }}</option>    
                            @else
                                <option value="{{ is_object($value) ? $value->id : $value['id'] }}">{{ $localized_value }}</option>
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
    <input class="input_week_number" type="week" name="week_number" value="{{ $data['week_data']['week_number'] }}" min="{{ $data['in_schedule']['current_study_period_border_weeks']['start'] }}" max="{{ $data['in_schedule']['current_study_period_border_weeks']['end'] }}">
    <input type="hidden" name="prev_replace_rules" value="{{ json_encode($data['prev_replace_rules']) }}">
    <input type="hidden" name="week_data" value="{{ json_encode($data['week_data']) }}">
    <input type="hidden" name="is_red_week" value="{{ isset($data['is_red_week']) ? ($data['is_red_week'] ? 1 : 0) : '' }}">
    <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
    <p for="{{ $field_name }}" class="form-explanation"><span class="settings-red-star">*</span>{{ __('form.multiple_fields_select') }}</p>
    <button type="submit" class="btn btn-primary form-button">{{ __('form.show') }}</button>
</form>