@extends('layouts.app')
@section('content')
    <div class="container">
        @includeIf('parts.notices.errors_instance_update')
        @includeIf('parts.notices.response')
        <div class="external-form-container">
            <div class="internal-form-container">
                @includeIf('parts.forms.add_update')
            </div>
        </div>

    </div>
@endsection


