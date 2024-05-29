{{-- extends('teacher.teachers', 'settings', 'group.add_group_form', group.group_schedule', 'group.groups', 'layouts.personal',
             'lesson.add_lesson_form', 'lesson.lessons', 'replacement_requests.replacement_request', 'teacher.add_teacher_form', 
             'teacher.teacher_schedule', 'user.add_user_form', 'user.users') --}}
@if (\Session::has('response'))
    @if(\Session::get('response')['success'])
        <div class="alertAccess">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ \Session::get('response')['message'] }}
        </div>
    @else
        <div class="alertFail">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ \Session::get('response')['message'] }}
        </div>
    @endif
@endif