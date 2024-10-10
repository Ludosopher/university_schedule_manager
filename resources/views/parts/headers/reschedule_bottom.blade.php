{{-- extends('lesson.lesson_reschedule') --}}
<div class="replacement-schedule-header-div">
    <h5>{{ __('header.view_in_schedule') }}:</h5>
    <div class="schedule-button-group">
        <form method="POST" action="{{ route('teacher-reschedule') }}" target="_blank">
        @csrf
            <input type="hidden" name="lesson_id" value="{{ $data['lesson_id'] }}">
            <input type="hidden" name="teacher_id" value="{{ $data['teacher_id'] }}">
            <input type="hidden" name="week_number" value="{{ $data['week_data']['week_number'] }}">
            <input type="hidden" name="prev_data" value="{{ json_encode(old()) }}">
            <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
            <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
            <input type="hidden" name="rescheduling_lesson_date" value="{{ $data['rescheduling_lesson_date'] ?? '' }}">
            <button type="submit" class="btn btn-light schedule-dropdown">{{ __("form.teacher's") }}</button>
        </form>
        @if (isset($data['groups_ids_names']) && is_array($data['groups_ids_names']))
            @foreach ($data['groups_ids_names'] as $group)
                <form method="POST" action="{{ route('group-reschedule') }}" target="_blank">
                @csrf
                    <input type="hidden" name="lesson_id" value="{{ $data['lesson_id'] }}">
                    <input type="hidden" name="teacher_id" value="{{ $data['teacher_id'] }}">
                    <input type="hidden" name="group_id" value="{{ $group['id']  }}">
                    <input type="hidden" name="week_number" value="{{ $data['week_data']['week_number'] }}">
                    <input type="hidden" name="prev_data" value="{{ json_encode(old()) }}">
                    <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                    <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                    <input type="hidden" name="rescheduling_lesson_date" value="{{ $data['rescheduling_lesson_date'] ?? '' }}">
                    <button type="submit" class="btn btn-light schedule-dropdown">{{ $group['name'] }}</button>
                </form>
            @endforeach
        @endif
    </div>
</div>