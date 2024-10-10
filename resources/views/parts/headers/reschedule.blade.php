{{-- extends('lesson.lesson_reschedule') --}}
<div>
    <div>
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
            <h1 class="top-header">{{ str_replace(['?-1', '?-2'], [$data['week_data']['start_date'], $data['week_data']['end_date']], __('header.dated_reschedule_variants')) }}<span style="background-color: {{ $bg_color }};">{{ str_replace('?', $week_color, __('header.week_color')) }}</span></h1>   
        @else
            <h1 class="top-header">{{ __('header.regular_reschedule_variants') }}</h1>
        @endif
        <h4>{{ __('header.rescheduling_lesson') }}: {{ __('dictionary.'.$data['lesson_name']) }} - {{ __('dictionary.'.$data['lesson_week_day']) }}, {{ __('dictionary.'.$data['lesson_class_period']) }} {{ __('header.class_period') }} ({{ $data['date_or_weekly_period'] }})</h4>
        <h4>{{ __('header.teacher') }}: {{ $data['teacher_name'] ?? ''}}</h4>
    </div>
    <div class="replacement-schedule-header-div">
        <h4>{{ __('header.group(s)') }}: {{ $data['groups_name'] ?? ''}}</h4>
        <div class="schedule-button-group">
            <form method="POST" action="{{ route('lesson-rescheduling') }}" class="top-right-button">
            @csrf
                <input type="hidden" name="teacher_id" value="{{ $data['teacher_id'] }}">
                <input type="hidden" name="lesson_id" value="{{ $data['lesson_id'] }}">
                @php
                    $week_number = isset($data['week_data']['week_number']) ? $data['week_data']['week_number'] : (isset($data['week_number']) ? $data['week_number'] : '');
                @endphp
                <input type="week" name="week_number" value="{{ $week_number }}" min="{{ $data['current_study_period_border_weeks']['start'] }}" max="{{ $data['current_study_period_border_weeks']['end'] }}">
                <button type="submit" class="btn btn-primary">{{ __('form.this_week') }}</button>
            </form>
        </div>
    </div>
</div>