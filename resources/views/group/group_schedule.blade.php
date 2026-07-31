@extends('layouts.app')
@section('content')
    <div class="container">
        @includeIf('parts.notices.response')
        @includeIf('parts.notices.errors_various')
        @includeIf('parts.headers.schedule')
        @includeIf('parts.matrices.schedule')
    </div>    
@endsection
