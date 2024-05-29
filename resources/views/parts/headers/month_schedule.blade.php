{{-- extends('teacher.teacher_month_schedule', 'group.group_month_schedule') --}}
<h1 class="top-header">{{ str_replace('?', $data['month_name'], __('header.'.$data['appelation'].'_schedule_on')) }}</h1>
<div class="replacement-schedule-header-div">
    <h3>{{ __('header.'.$data['appelation']) }}: {{ $data['instance_name'] ?? ''}}</h3>
    <div class="schedule-button-group">
        <form method="POST" action="{{ route($data['appelation'].'-month-schedule-doc-export') }}">
        @csrf
            <input type="hidden" name="month_name" value="{{ $data['month_name'] }}">
            <input type="hidden" name="{{ $data['appelation'].'_name' }}" value="{{ $data['instance_name'] }}">
            <input type="hidden" name="weeks" value="{{ json_encode($data['weeks']) }}">
            <input type="hidden" name="prev_data" value="{{ json_encode(old()) }}">
            <button type="submit" class="btn btn-primary top-right-button">{{ __('form.ms_word') }}</button>
        </form>
    </div>
</div> 