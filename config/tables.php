<?php

return [
   'teachers' => [
      [
         'field' => 'full_name',
         'header' => 'Ф.И.О.',
         'sorting' => true
      ],
      [
         'field' => 'age',
         'header' => 'Возраст',
         'sorting' => true
      ],
      [
         'field' => 'phone',
         'header' => 'Телефон',
         'sorting' => false
      ],
      [
         'field' => 'email',
         'header' => 'Электронная почта',
         'sorting' => false
      ],
      [
         'field' => [
            'faculty',
            'name'
         ],
         'header' => 'Факультет',
         'sorting' => true
      ],
      [
         'field' => [
            'department',
            'name'
         ],
         'header' => 'Кафедра',
         'sorting' => true
      ],
      [
         'field' => [
            'professional_level',
            'name'
         ],
         'header' => 'Профессиональный уровень',
         'sorting' => true
      ],
      [
         'field' => [
            'position',
            'name'
         ],
         'header' => 'Должность',
         'sorting' => true
      ],
      [
         'field' => [
            'academic_degree',
            'name'
         ],
         'header' => 'Учёная степень',
         'sorting' => true
      ],
   ],
   'groups' => [
      [
         'field' => 'name',
         'header' => 'Название',
         'sorting' => false
      ],
      [
         'field' => [
            'faculty',
            'name'
         ],
         'header' => 'Факультет',
         'sorting' => true
      ],
      [
         'field' => [
            'study_program',
            'name'
         ],
         'header' => 'Учебная программа',
         'sorting' => true
      ],
      [
         'field' => [
            'study_orientation',
            'name'
         ],
         'header' => 'Специальность',
         'sorting' => true
      ],
      [
         'field' => [
            'study_degree',
            'name'
         ],
         'header' => 'Уровень образования',
         'sorting' => true
      ],
      [
         'field' => [
            'study_form',
            'name'
         ],
         'header' => 'Форма образования',
         'sorting' => true
      ],
      [
         'field' => [
            'course',
            'number'
         ],
         'header' => 'Курс',
         'sorting' => true
      ],
      [
         'field' => 'size',
         'header' => 'Численность',
         'sorting' => true
      ],
   ],
   'lessons' => [
      [
         'field' => [
            'class_period',
            'id'
         ],
         'header' => 'Пара',
         'sorting' => true
      ],
      [
         'field' => 'name',
         'header' => 'Предмет',
         'sorting' => true
      ],
      [
         'field' => [
            'lesson_type',
            'name'
         ],
         'header' => 'Вид',
         'sorting' => true
      ],
      [
        'field' => [
           'lesson_room',
           'number'
        ],
        'header' => 'Аудитория',
        'sorting' => true
      ],
      [
         'field' => [
            'week_day',
            'name'
         ],
         'header' => 'День недели (дата)',
         'sorting' => true
      ],
      [
         'field' => [
            'weekly_period',
            'name'
         ],
         'header' => 'Недельная периодичность',
         'sorting' => true
      ],
      [
         'field' => 'groups_name',
         'header' => 'Группа(ы)',
         'sorting' => false
      ],
      [
         'field' => [
            'teacher',
            'profession_level_name'
         ],
         'sort_name' => 'profession_level_name',
         'header' => 'Преподаватель',
         'sorting' => true
      ],
   ],
   'replacement_variants' => [
      [
         'field' => 'subject',
         'header' => 'Предмет',
         'sorting' => null
      ],
      [
         'field' => 'week_day_id',
         'header' => 'День недели',
         'sorting' => null
      ],
      [
         'field' => 'weekly_period_id',
         'header' => 'Недельная периодичность',
         'sorting' => null
      ],
      [
         'field' => 'class_period_id',
         'header' => 'Пара',
         'sorting' => null
      ],
      [
         'field' => 'lesson_room_id',
         'header' => 'Аудитория',
         'sorting' => null
      ],
      [
         'field' => 'profession_level_name',
         'header' => 'Заменяющий преподаватель',
         'sorting' => null
      ],
      [
         'field' => 'phone',
         'header' => 'Телефон',
         'sorting' => null
      ],
      [
         'field' => 'age',
         'header' => 'Возраст',
         'sorting' => null
      ],
      [
         'field' => 'department_id',
         'header' => 'Кафедра',
         'sorting' => null
      ],
      [
         'field' => 'position_id',
         'header' => 'Должность',
         'sorting' => null
      ],
      [
         'field' => 'schedule_position_id',
         'header' => 'Встроенность в расписание заменяющего преподавателя',
         'sorting' => null
      ],
   ],
   'users' => [
      [
         'field' => 'name',
         'header' => 'Имя',
         'sorting' => true
      ],
      [
         'field' => 'phone',
         'header' => 'Телефон',
         'sorting' => false
      ],
      [
         'field' => 'email',
         'header' => 'Электронная почта',
         'sorting' => false
      ],
      [
         'field' => [
            'moderator',
         ],
         'sort_name' => 'is_moderator',
         'header' => 'Модератор',
         'sorting' => true
      ],
      [
         'field' => [
            'admin',
         ],
         'sort_name' => 'is_admin',
         'header' => 'Администратор',
         'sorting' => true
      ],
      [
         'field' => 'teacher_names',
         'header' => 'Допуск к управлению расписанием преподавателей',
         'sorting' => false
      ],
      [
         'field' => 'group_names',
         'header' => 'Допуск к управлению расписанием групп',
         'sorting' => false
      ],
   ],
];
