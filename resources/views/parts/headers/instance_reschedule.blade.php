{{-- extends('teacher.teacher_reshedule', 'group.group_reschedule') --}}
@if(isset($data['week_data']) && isset($data['is_red_week']))
@php
    $is_red_week = 0;
    $week_color = __('header.blue_week_color');
    $bg_color = '#ace7f2';
    if ($data['is_red_week']) {
        $is_red_week = 1;
        $week_color = __('header.red_week_color');
        $bg_color = '#ffb3b9';
    }
@endphp
    <h1 class="top-header">{{ str_replace(['?-1', '?-2'], [$data['week_data']['start_date'], $data['week_data']['end_date']], __('header.'.$data['appelation'].'_dated_reschedule_variants')) }} <span style="background-color: {{ $bg_color }};">{{ str_replace('?', $week_color, __('header.week_color')) }}</span></h1>   
@else
    <h1 class="top-header">{{ __('header.'.$data['appelation'].'_regular_reschedule_variants') }}</h1>
@endif
<div class="replacement-schedule-header-div">
    <h3>{{ __('header.'.$data['appelation']) }}: {{ $data[$data['appelation'].'_name'] ?? ''}}</h3>
    <div class="schedule-button-group">
        <form method="POST" action="{{ route($data['appelation'].'-reschedule') }}">
        @csrf
            <input type="hidden" name="{{ $data['appelation'].'_id' }}" value="{{ $data[$data['appelation'].'_id'] }}">
            <input type="hidden" name="lesson_id" value="{{ $data['rescheduling_lesson_id'] }}">
            <input type="hidden" name="teacher_id" value="{{ $data['teacher_id'] }}">
            <input type="hidden" name="prev_data" value="{{ json_encode(old()) }}">
            <input type="week" name="week_number" value="{{ $data['week_data']['week_number'] }}" min="{{ $data['current_study_period_border_weeks']['start'] }}" max="{{ $data['current_study_period_border_weeks']['end'] }}">
            <input type="hidden" name="rescheduling_lesson_date" value="{{ $data['rescheduling_lesson_date'] ?? '' }}">
            <button type="submit" class="btn btn-primary">{{ __('form.this_week') }}</button>
        </form>
        <form method="POST" action="{{ route($data['appelation'].'-reschedule-doc-export') }}">
        @csrf
            <input type="hidden" name="lessons" value="{{ json_encode($data['periods']) }}">
            <input type="hidden" name="week_data" value="{{ json_encode($data['week_data']) }}">
            <input type="hidden" name="{{ $data['appelation'].'_name' }}" value="{{ $data[$data['appelation'].'_name'] }}">
            <input type="hidden" name="rescheduling_lesson_id" value="{{ $data['rescheduling_lesson_id'] }}">
            <input type="hidden" name="prev_data" value="{{ json_encode(old()) }}">
            <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
            <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
            <button type="submit" class="btn btn-primary top-right-button">{{ __('form.ms_word') }}</button>
        </form>
    </div>
</div>