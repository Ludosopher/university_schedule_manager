<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="https://ngma.su/" target="_blank">
                    {{-- {{ config('app.name', 'Laravel') }} --}}
                    <img src="{{ asset('storage/home-page/university_brand.png') }}" width="52" height="53" alt="lorem">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link main-nav-link" href="{{ route('home') }}">Домашняя</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link main-nav-link" href="{{ route('about') }}">О сервисе</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle main-nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Преподаватели
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="nav-link" href="{{ route('teachers') }}">Список</a>
                                @if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_moderator))
                                    <a class="nav-link" href="{{ route('teacher-add-form') }}">Добавить</a>
                                @endif
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle main-nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Группы
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="nav-link" href="{{ route('groups') }}">Список</a>
                                @if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_moderator))
                                    <a class="nav-link" href="{{ route('group-add-form') }}">Добавить</a>
                                @endif
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle main-nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Занятия
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="nav-link" href="{{ route('lessons') }}">Список</a>
                                @if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_moderator))
                                    <a class="nav-link" href="{{ route('lesson-add-form') }}">Добавить</a>
                                @endif
                            </div>
                        </li>
                        @if (Auth::check() && Auth::user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link main-nav-link" href="{{ route('users') }}">Пользователи</a>
                            </li>
                        @endif
                        @if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_moderator))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle main-nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Запросы
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <a class="nav-link" href="{{ route('replacement_requests') }}">На замену</a>
                                    <a class="nav-link" href="#">На перенос</a>
                                </div>
                            </li>
                        @endif
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link main-nav-link" href="{{ route('user-account-main') }}">Личный кабинет</a>
                        </li>
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Вход</a>
                                {{-- {{ __('Login') }} --}}
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                                    {{-- {{ __('Register') }} --}}
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle main-nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{-- {{ __('Logout') }} --}}
                                        Выход
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
