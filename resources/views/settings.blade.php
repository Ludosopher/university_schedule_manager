@extends('layouts.app')
@section('content')
    <div class="container">
        @if (\Session::has('response'))
            @if(\Session::get('response')['success'])
                <div class="alertAccess">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    {{ \Session::get('response')['message'] }}
                </div>
            @else
                <div class="alertFail">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    {{ \Session::get('response')['message'] }}
                </div>
            @endif
        @endif
        
        <div class="external-form-container">
            <div class="internal-form-container settings-container">
                <h2 class="settings-h2">{{ __('header.settings_update') }}</h2>
                <form method="POST" action="{{ route('settings-update') }}">
                @csrf
                    @if(isset($data['settings']))
                        <table>
                            @foreach($data['settings'] as $setting)
                                @foreach($data['forms'] as $form)
                                    @if($setting->name === $form['name'])
                                        @if($form['type'] == 'switch')
                                            @php $field_name = $form['name']; @endphp
                                            <tr class="settings-form-tr">
                                                <td>
                                                    <div class="mb-3">
                                                        <div class="form-check form-switch">
                                                            @if(old($field_name) !== null)
                                                                <input class="form-check-input" name="{{ $field_name }}" type="checkbox" id="{{ $field_name }}" value="{{ true }}" checked>
                                                            @elseif((boolean)$setting->value)
                                                                <input class="form-check-input" name="{{ $field_name }}" type="checkbox" id="{{ $field_name }}" value="{{ true }}" checked>
                                                            @else
                                                                <input class="form-check-input" name="{{ $field_name }}" type="checkbox" id="{{ $field_name }}" value="{{ true }}">
                                                            @endif
                                                            <label class="form-check-label" for="{{ $field_name }}">{{ __('form.'.$form['name']) }}</label>
                                                        </div>
                                                        @if ($errors !== null && $errors->has($field_name))
                                                            @foreach($errors->get($field_name) as $error)
                                                                <div class="validationErrorText">{{ $error }}</div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        @if($form['type'] == 'input')
                                            @php $field_name = $form['name']; @endphp
                                            <tr class="settings-form-tr">
                                                <td>
                                                    <label for="{{ $field_name }}" class="form-label settings-form-label">{{ __('form.'.$form['name']) }}
                                                        @if (isset($form['is_required']) && $form['is_required'])
                                                            <span class="settings-red-star">*</span>
                                                        @endif
                                                    </label>
                                                </td>
                                                <td class="settings-second-td">
                                                    <input name="{{ $field_name }}" type="{{ $form['input_type'] }}" class="form-control form-control-sm settings-input" id="{{ $field_name }}" min="{{ $form['min'] ?? '' }}" max="{{ $form['max'] ?? '' }}" value="{{old($field_name) !== null ? old($field_name) : $setting->value }}">
                                                    @if ($errors !== null && $errors->has($field_name))
                                                        @foreach($errors->get($field_name) as $error)
                                                            <div class="validationErrorText">{{ $error }}</div>
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @break   
                                    @endif
                                @endforeach
                            @endforeach
                        </table>
                    @endif
                    <p class="form-explanation"><span class="settings-red-star">*</span>{{ __('form.required_field') }}</p>
                    <button type="submit" class="btn btn-primary settings-button">{{ __('form.update') }}</button>
                </form>
            </div>
        </div>

    </div>
@endsection


