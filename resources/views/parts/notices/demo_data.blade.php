{{-- extends('teacher.teachers', 'group.groups', 'lesson.lessons') --}}
@php
    $first_msg = false;
    if (session('promo_data_msg') === null) {
        $first_msg = true;
        session(['promo_data_msg' => true]);
    }
@endphp
@if (env('IS_TESTING') === true && $first_msg)
    <div class="alertAccess">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        В демоверсии используются тестовые данные. В полном объеме (для всех сценариев) они заполнены только для факультета бизнеса и социальных технологий.
    </div>
@endif