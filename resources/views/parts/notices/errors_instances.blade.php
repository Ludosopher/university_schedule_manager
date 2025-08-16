{{-- extends('teacher.teachers', 'group.groups') --}}
@if($errors->any() && ($errors->has('schedule_'.$data['appelation'].'_id') || $errors->has('week_number')))
    <div class="alertFail">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        {{ __('user_validation.invalid_input_data') }}
    </div>
@endif