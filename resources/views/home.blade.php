@extends('layouts.app')

@section('content')
    <div id="home-main">
        @if (isset($permission_error))
            <div class="alertFail fail-on-hp">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                {{ __('user_validation.permission_error') }}
            </div>
        @endif
        <div id="home-content">
            <div id="home-header-div">
                <p id="home-header-p">{{ __('content.study_schedule_manager') }}</p>
            </div>
            <div id="home-list-div">
                <ul id="home-list-ul">
                    <li id="home-main-li-list">{{ __('content.lists') }}</li>
                    <li id="home-main-li-schedule">{{ __('content.schedules') }}</li>
                    <li id="home-main-li-schedule-change">{{ __('content.replacement_reschedule_variants') }}</li>
                    <li id="home-main-li-agree">{{ __('content.discussion_and_coordination') }}</li>
                </ul>
            </div>
        </div>
        
    </div>
    <footer class="text-center text-lg-start fixed-bottom bg-dark text-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <p class="copyright-text text-center">Copyright &copy; {{ date('Y') }} Viktor Alikin. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

@endsection
{{-- <div class="container">
    <div id="home-main">
        <h4 class="hp-university-name">Новочеркасский инженерно-мелиоративный институт имени А.К. Кортунова ФГБОУ ВО "Донской государственный аграрный университет"</h4>
        <div id="home-main-left">
            <h1 class="hp-main-header">Менеджер учебного <span id="schedule-word">расписания</span></h1>
            <h5 class="hp-main-description">Списки преподавателей, студенческих групп и занятий. Учебные расписания. Подбор вариантов замены и переноса занятий.</h5>
        </div>
    </div>
</div> --}}
