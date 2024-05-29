{{-- extends('teacher.teacher_schedule', 'group.group_month_schedule', 'group.group_reschedule', 'group.group_schedule',
             'layouts.personal', 'lesson.lesson_reschedule', 'teacher.teacher_month_schedule', 'teacher.teacher_reschedule',) --}}
@if($errors->any())
    <div class="alertFail">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        {{ __('user_validation.invalid_input_data') }}
    </div>
@endif