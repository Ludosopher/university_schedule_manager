@extends('layouts.app')
@section('content')
    <div class="container">
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alertFail">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    {{ $error }}
                </div>    
            @endforeach
            </div>
        @endif
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
