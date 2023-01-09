@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="getAllContainer">
            <div class="getAllLeft">
                <h4>Личный кабинет</h4>
                <nav class="nav flex-column">
                    <a class="nav-link main-nav-link" href="{{ route('user-account-main') }}">Основное</a>
                    <a class="nav-link main-nav-link" href="{{ route('my_replacement_requests') }}">Просьбы о замене</a>
                    {{-- <a class="nav-link dropdown-toggle main-nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Преподаватели
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="nav-link" href="{{ route('teachers') }}">Список</a>
                        @if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_moderator))
                            <a class="nav-link" href="{{ route('teacher-add-form') }}">Добавить</a>
                        @endif
                    </div> --}}
                </nav>
            </div>
            <div class="getAllRight">
                @yield('personal_content')
            </div>
        </div>
    </div>
@endsection
