@extends('layouts.app')
@section('content')
    <div class="container">
        @includeIf('parts.notices.errors_various')
        @includeIf('parts.notices.response')
        <div class="getAllContainer">
            <div class="getAllLeft">
                <h4>{{ __('header.account') }}</h4>
                <nav class="nav flex-column">
                    <a class="nav-link main-nav-link" href="{{ route('user-account-main') }}">{{ __('header.basic') }}</a>
                    <a class="nav-link main-nav-link" href="{{ route('my_replacement_requests') }}">{{ __('header.replacement_requests') }}</a>
                </nav>
            </div>
            <div class="getAllRight">
                @yield('personal_content')
            </div>
        </div>
    </div>
@endsection
