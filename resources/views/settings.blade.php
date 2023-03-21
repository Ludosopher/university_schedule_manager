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
            <div class="internal-form-container" style="width: 30%;">
                <h2 style="margin-top: 1.5rem">{{ __('header.settings_update') }}</h2>
                <form method="POST" action="{{ route('settings-update') }}">
                @csrf
                    @if(isset($data['settings']))
                        <table>
                            @foreach($data['settings'] as $setting)
                                @foreach($data['forms'] as $form)
                                    @if($setting->name === $form['name'])
                                        @if($form['type'] == 'switch')
                                            @php $field_name = $form['name']; @endphp
                                            <tr style="height: 50px;">
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
                                            <tr style="height: 50px;">
                                                <td>
                                                    <label for="{{ $field_name }}" class="form-label" style="margin: 0px; line-height: 100%;">{{ __('form.'.$form['name']) }}
                                                        @if (isset($form['is_required']) && $form['is_required'])
                                                            <span style="color: red;">*</span>
                                                        @endif
                                                    </label>
                                                </td>
                                                <td style="width: 10%;">
                                                    <input name="{{ $field_name }}" type="{{ $form['input_type'] }}" class="form-control form-control-sm" id="{{ $field_name }}" min="{{ $form['min'] ?? '' }}" max="{{ $form['max'] ?? '' }}" value="{{old($field_name) !== null ? old($field_name) : $setting->value }}" style="padding: 0px; text-align: center;">
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
                    <p class="form-explanation"><span style="color: red;">*</span>{{ __('form.required_field') }}</p>
                    <button type="submit" class="btn btn-primary" style="float: right;">{{ __('form.update') }}</button>
                </form>
            </div>
        </div>

    </div>
@endsection


