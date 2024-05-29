{{-- extends('lesson.replacement_lessons') --}}
@if($errors->any() && ($errors->has('replacement_lessons') 
                    || $errors->has('lessons') 
                    || $errors->has('header_data') 
                    || $errors->has('week_data')
                    || $errors->has('week_dates')
                    || $errors->has('week_number')
                    || $errors->has('is_red_week') 
                    || $errors->has('replaceable_lesson_id')
                    || $errors->has('replace_rules.*.week_day_id')
                    || $errors->has('replace_rules.*.weekly_period_id')
                    || $errors->has('replace_rules.*.class_period_id')
                    || $errors->has('replace_rules.*.teacher_id')
                    || $errors->has('replace_rules.*.date')))
    <div class="alertFail">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        {{ __('user_validation.invalid_input_data') }}
    </div>
@endif