{{-- extends('teacher.add_teacher_form', 'group.add_group_form', 'lesson.add_lesson_form', 'user.add_user_form') --}}
@if ($errors->any() && $errors->has('updating_id'))
    @foreach($errors->get('updating_id') as $error)
        <div class="alertFail">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ $error }}
        </div>
    @endforeach
@endif