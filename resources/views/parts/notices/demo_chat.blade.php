{{-- extends('replacement_request.chat') --}}
@if (env('is_socket_available') === false)
    <div class="alertAccess">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        Чат не функционирует из-за ограничений хостинга !
    </div>
@endif