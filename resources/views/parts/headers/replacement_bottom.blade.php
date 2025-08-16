{{-- extends('lesson.replacement_lessons') --}}
<div class="replacement-schedule-header-div">
    <h3>{{ __('header.in_teacher_schedule') }}</h3>
    <form method="POST" action="{{ route('lesson-replacement-schedule-doc-export') }}">
    @csrf
        <input type="hidden" name="prev_replace_rules" value="{{ json_encode($data['prev_replace_rules']) }}">
        <input type="hidden" name="lessons" value="{{ json_encode($data['in_schedule']) }}">
        <input type="hidden" name="header_data" value="{{ json_encode($data['header_data']) }}">
        <input type="hidden" name="week_data" value="{{ json_encode($data['week_data']) }}">
        <input type="hidden" name="replaceable_lesson_id" value="{{ $data['prev_replace_rules']['lesson_id'] }}">
        <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
        <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
        <input type="hidden" name="date_or_weekly_period" value="{{ $data['date_or_weekly_period'] }}">
        <button type="submit" class="btn btn-primary replacement-doc-export-button">{{ __('form.ms_word') }}</button>
    </form>
</div>