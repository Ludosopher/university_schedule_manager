@extends('layouts.app')
@section('content')
    <div class="container">
        @includeIf('parts.notices.demo_data')
        @includeIf('parts.notices.response')
        @includeIf('parts.notices.errors_instances')
        @includeIf('parts.notices.errors_instance_delete')
        <div class="getAllContainer">
            <div class="getAllLeft">
                 @includeIf('parts.forms.find')
            </div>
            <div class="getAllRight">
                @includeIf('parts.tables.instances')
            </div>
        </div>
    </div>
@endsection
