@extends('layouts.app')
@section('content')
    <div class="container">
        @includeIf('parts.notices.errors_various')
        @includeIf('parts.headers.month_schedule')
        @includeIf('parts.matrices.month_schedule')
    </div>    
@endsection
