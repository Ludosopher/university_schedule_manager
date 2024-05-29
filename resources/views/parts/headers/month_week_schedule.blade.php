{{-- extends('teacher.teacher_month_schedule', 'group.group_month_schedule') --}}
@if($week_content['week_data']['current_study_season'] === $data['study_seasons']['studies'])
    <h5></h5>
@elseif($week_content['week_data']['current_study_season'] === $data['study_seasons']['session'])
    <h5>{{ __('header.session') }}</h5>
@else
    <h5>{{ __('header.vacation') }}</h5>
@endif