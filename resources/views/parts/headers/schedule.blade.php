{{-- extends('teacher.teacher_schedule', 'group.group_schedule') --}}
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
    @if ($data['week_data']['current_study_season'] === $data['study_seasons']['studies'])
        <h1 class="top-header">{{ str_replace(['?-1', '?-2'], [$data['week_data']['start_date'], $data['week_data']['end_date']], __('header.'.$data['appelation'].'_dated_schedule')) }} <span style="background-color: {{ $bg_color }};">{{ str_replace('?', $week_color, __('header.week_color')) }}</span></h1>   
    @elseif($data['week_data']['current_study_season'] === $data['study_seasons']['session'])
        <h1 class="top-header">{{ __('header.session') }} {{ __('header.'.$data['required_study_period']->season) }} {{ $data['required_study_period']->year }} {{ __('header.of_year') }}</h1>
    @else
        <h1 class="top-header">{{ __('header.vacation') }}</h1>
    @endif        
@else
    <h1 class="top-header">{{ __('header.'.$data['appelation'].'_regular_schedule') }} {{ __('header.'.$data['required_study_period']->season) }} {{ $data['required_study_period']->year }} {{ __('header.of_year') }}</h1>
@endif
<div class="replacement-schedule-header-div">
        <h3>{{ __('header.'.$data['appelation']) }}: {{ $data['instance_name'] ?? ''}}</h3>
        <div class="schedule-button-group">
            <form class="schedule-form" method="POST" action="{{ route($data['appelation'].'-schedule', ['schedule_'.$data['appelation'].'_id' => $data['schedule_instance_id']]) }}" target="_blank">
            @csrf
                <select name="study_period_id" id="study-period-id">
                    <option selected value="">-------- ----</option>
                    @foreach($data['study_periods'] as $study_period)
                        @if ($study_period->id === $data['required_study_period']->id)
                            @if(isset($data['week_data']['week_number']))
                                <option value="{{ $study_period->id }}">{{ __('form.'.$study_period->season) }} {{ $study_period->year }}</option>    
                            @else
                                <option selected value="{{ $study_period->id }}">{{ __('form.'.$study_period->season) }} {{ $study_period->year }}</option>
                            @endif
                        @else
                            <option value="{{ $study_period->id }}">{{ __('form.'.$study_period->season) }} {{ $study_period->year }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">{{ __('form.this_study_period') }}</button>
            </form>
            <form class="schedule-form" method="POST" action="{{ route($data['appelation'].'-month-schedule') }}" target="_blank">
            @csrf
                <input type="month" name="month_number" value="">
                <input type="hidden" name="{{ 'schedule_'.$data['appelation'].'_id' }}" value="{{ $data['schedule_instance_id'] }}">
                <button type="submit" class="btn btn-success month-schedule-button">{{ __('form.this_month') }}</button>
            </form>
            <form class="schedule-form" method="POST" action="{{ route($data['appelation'].'-schedule', ['schedule_'.$data['appelation'].'_id' => $data['schedule_instance_id']]) }}" target="_blank">
            @csrf
                <input type="week" name="week_number" value="{{ $data['week_data']['week_number'] }}">
                <button type="submit" class="btn btn-primary">{{ __('form.this_week') }}</button>
            </form>
            <form method="POST" action="{{ route($data['appelation'].'-schedule-doc-export') }}">
            @csrf
                <input type="hidden" name="lessons" value="{{ isset($data['lessons']) ? json_encode($data['lessons']) : '' }}">
                <input type="hidden" name="{{ $data['appelation'].'_name' }}" value="{{ $data['instance_name'] }}">
                <input type="hidden" name="week_data" value="{{ isset($data['week_data']) ? json_encode($data['week_data']) : '' }}">
                <input type="hidden" name="week_dates" value="{{ isset($data['week_dates']) ? json_encode($data['week_dates']) : '' }}">
                <input type="hidden" name="is_red_week" value="{{ $is_red_week ?? '' }}">
                <input type="hidden" name="study_period_id" value="{{ $data['study_period_id'] ?? '' }}">
                <button type="submit" class="btn btn-primary top-right-button">{{ __('form.ms_word') }}</button>
            </form>
        </div>
    </div>  