{{-- extends('lesson.replacement_lessons') --}}
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
    <h1 class="top-header">{{ str_replace(['?-1', '?-2'], [$data['week_data']['start_date'], $data['week_data']['end_date']], __('header.dated_replacement_variants')) }}<span style="background-color: {{ $bg_color }};">{{ str_replace('?', $week_color, __('header.week_color')) }}</span></h1>   
@else
    <h1 class="top-header">{{ __('header.regular_replacement_variants') }}</h1>
@endif
<h5>{{ __('header.replaceable_lesson') }}: {{ __('dictionary.'.$data['header_data']['class_period']) }} {{ __('header.class_period') }}, {{ __('dictionary.'.$data['header_data']['week_day']) }}, ({{ $data['date_or_weekly_period'] }})</h5>
<h5>{{ __('header.teacher') }}: {{ $data['header_data']['teacher'] }}</h5>
<div class="replacement-schedule-header-div">
    <h5>{{ __('header.group') }}: {{ $data['header_data']['group'] }}</h5>
    <div class="schedule-button-group">
        <form method="POST" action="{{ route('lesson-replacement-doc-export') }}">
        @csrf
            <input type="hidden" name="prev_replace_rules" value="{{ json_encode($data['prev_replace_rules']) }}">
            <input type="hidden" name="replacement_lessons" value="{{ json_encode($data['replacement_lessons']) }}">
            <input type="hidden" name="header_data" value="{{ json_encode($data['header_data']) }}">
            <input type="hidden" name="week_data" value="{{ json_encode($data['week_data']) }}">
            <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
            <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
            <input type="hidden" name="date_or_weekly_period" value="{{ $data['date_or_weekly_period'] }}">
            <button type="submit" class="btn btn-primary top-right-button">{{ __('form.ms_word') }}</button>
        </form>
    </div>
</div>