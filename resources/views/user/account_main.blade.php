@extends('layouts.personal')
@section('personal_content')
    <div class="container">
        <h2>Контактные данные</h2>
        <form method="POST" action="{{ route('user-self-update') }}">
        @csrf
            <input type="hidden" name="updating_id" value="{{ $data['id'] }}">
            <table>
                <tr>
                    <td class="account-contact-cell"><label for="phone" class="form-label account-contact-label">Телефон:</label></td>
                    <td class="account-contact-cell"><input class="account-contact-input" type="text" name="phone" value="{{ $data['phone'] ?? '' }}"></td>
                </tr>
                <tr>
                    <td class="account-contact-cell"><label for="email" class="form-label account-contact-label">Электронная почта:</label></td>
                    <td class="account-contact-cell"><input class="account-contact-input" type="text" name="email" value="{{ $data['email'] }}"></td>
                </tr>
                <tr>
                    <td class="account-contact-cell"></td>
                    <td class="account-contact-cell"></td>
                    <td class="account-contact-cell"><button class="btn btn-primary account-contact-button" type="submit">Обновить</button></td>
                </tr>
            </table>
        </form>
        <h2 style="margin-top: 40px;">Уровень</h2>
        <p> {{ $data['level'] }}</p>
        <h2>Допуск к управлению расписанием преподавателей</h2>
        @if(count($data['teacher_names']))
            @foreach($data['teacher_names'] as $teacher_name)
            <p> {{ $teacher_name }}</p> 
            @endforeach
        @else
            <p>Нет допуска к управлению расписанием ни одного преподавателя</p>
        @endif
        <h2 style="margin-top: 40px;">Допуск к управлению расписанием групп</h2>
        @if(count($data['group_names']))
            @foreach($data['group_names'] as $group_name)
                <p> {{ $group_name }}</p> 
            @endforeach
        @else
            <p>Нет допуска к управлению расписанием ни одной группы</p>
        @endif
    </div>
@endsection
