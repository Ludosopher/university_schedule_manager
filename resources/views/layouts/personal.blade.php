@extends('layouts.app')
@section('content')
    <div class="container">
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
        <div class="getAllContainer">
            <div class="getAllLeft">
                <h4>Личный кабинет</h4>
                <nav class="nav flex-column">
                    <a class="nav-link main-nav-link" href="{{ route('user-account-main') }}">Основное</a>
                    <a class="nav-link main-nav-link" href="{{ route('my_replacement_requests') }}">Просьбы о замене</a>
                </nav>
            </div>
            <div class="getAllRight">
                @yield('personal_content')
            </div>
        </div>
    </div>
@endsection
