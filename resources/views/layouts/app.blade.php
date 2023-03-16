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
                            <a class="nav-link main-nav-link" href="{{ route('home') }}">{{ __('menu.home') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link main-nav-link" href="{{ route('about') }}">{{ __('menu.about_app') }}</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle main-nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('menu.teachers') }}
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
                                {{ __('menu.groups') }}
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
                                {{ __('menu.lessons') }}
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
                                <a class="nav-link main-nav-link" href="{{ route('users') }}">{{ __('menu.users') }}</a>
                            </li>
                        @endif
                        @if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_moderator))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle main-nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ __('menu.requests') }}
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
                        <li class="nav-item" style="padding: 0.5rem 1rem;">
                            <form method="POST" action="{{ route('user-set-locate') }}">
                            @csrf
                                <select name="lang" aria-label="Default select example" style="border: 1px solid Gainsboro; color: Gray;">
                                    @foreach(config('enum.languages') as $lang)
                                        @if (\Session::has('applocale') && $lang == \Session::get('applocale'))
                                            <option selected value="{{ $lang }}">{{ $lang }}</option>    
                                        @elseif (! \Session::has('applocale') && $lang == config('app.locale'))
                                            <option selected value="{{ $lang }}">{{ $lang }}</option>
                                        @else
                                            <option value="{{ $lang }}">{{ $lang }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" style="border-style: none; background-color: inherit; color: Gray;" title="Изменить">▼</button>
                            </form>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link main-nav-link" href="{{ route('user-account-main') }}">{{ __('menu.personal_account') }}</a>
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
                        @if (Auth::check() && (Auth::user()->is_admin))
                            <li class="nav-item">
                                <a class="nav-link main-nav-link" href="{{ route('settings-get') }}" title="Настройки" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                                        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                                    </svg>
                                </a>
                            </li>
                        @endif
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
