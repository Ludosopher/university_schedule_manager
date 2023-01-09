@extends('layouts.personal')
@section('personal_content')
    <div class="container">
        <h2>Контактные данные</h2>
        <p><span style="font-weight: bold;">Телефон:</span> {{ $data['phone'] }}</p>
        <p><span style="font-weight: bold;">Электронная почта:</span> {{ $data['email'] }}</p>
        <h2>Уровень</h2>
        <p> {{ $data['level'] }}</p>
        <h2>Допуск к управлению расписанием преподавателей</h2>
        @foreach($data['teacher_names'] as $teacher_name)
           <p> {{ $teacher_name }}</p> 
        @endforeach
        <h2>Допуск к управлению расписанием групп</h2>
        @foreach($data['group_names'] as $group_name)
           <p> {{ $group_name }}</p> 
        @endforeach
    </div>
@endsection
