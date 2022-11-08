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
            'week_day',
            'name'
         ],
         'header' => 'День недели',
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

   ]
];