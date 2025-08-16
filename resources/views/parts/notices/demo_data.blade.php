{{-- extends('teacher.teachers', 'group.groups', 'lesson.lessons') --}}
@php
    $first_msg = false;
    if (session('promo_data_msg') === null) {
        $first_msg = true;
        session(['promo_data_msg' => true]);
    }
@endphp
@if (env('is_testing') === true && $first_msg)
    <div class="alertAccess">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        Демоверсия содержит вымышленные данные и только для факультета бизнеса и социальных технологий !
    </div>
@endif