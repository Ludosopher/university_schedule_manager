@extends('layouts.personal')
@section('personal_content')
    <div class="container">
        <h2>{{ __('header.contact_details') }}</h2>
        <form method="POST" action="{{ route('user-self-update') }}">
        @csrf
            <input type="hidden" name="updating_id" value="{{ $data['id'] }}">
            <table>
                <tr>
                    <td class="account-contact-cell"><label for="phone" class="form-label account-contact-label">{{ __('header.phone') }}:</label></td>
                    <td class="account-contact-cell"><input class="account-contact-input" type="text" name="phone" value="{{ $data['phone'] ?? '' }}"></td>
                </tr>
                <tr>
                    <td class="account-contact-cell"><label for="email" class="form-label account-contact-label">{{ __('header.email') }}:</label></td>
                    <td class="account-contact-cell"><input class="account-contact-input" type="text" name="email" value="{{ $data['email'] }}"></td>
                </tr>
                <tr>
                    <td class="account-contact-cell"></td>
                    <td class="account-contact-cell"></td>
                    <td class="account-contact-cell"><button class="btn btn-primary account-contact-button" type="submit">{{ __('form.update') }}</button></td>
                </tr>
            </table>
        </form>
        <h2 style="margin-top: 40px;">{{ __('header.level') }}</h2>
        <p> {{ $data['level'] }}</p>
        <h2>{{ __('header.access_to_teacher_schedule_management') }}</h2>
        @if(count($data['teacher_names']))
            @foreach($data['teacher_names'] as $teacher_name)
            <p> {{ $teacher_name }}</p> 
            @endforeach
        @else
            <p>{{ __('content.there_is_no_access_to_teacher_schedule_management') }}</p>
        @endif
        <h2 style="margin-top: 40px;">{{ __('header.access_to_group_schedule_management') }}</h2>
        @if(count($data['group_names']))
            @foreach($data['group_names'] as $group_name)
                <p> {{ $group_name }}</p> 
            @endforeach
        @else
            <p>{{ __('content.there_is_no_access_to_group_schedule_management') }}</p>
        @endif
    </div>
@endsection
