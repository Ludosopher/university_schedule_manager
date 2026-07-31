@extends('layouts.app')
@section('content')
    <div class="container">
        @includeIf('parts.notices.errors_various')
        @includeIf('parts.headers.instance_reschedule')
        @includeIf('parts.matrices.reschedule')
    </div>
@endsection
