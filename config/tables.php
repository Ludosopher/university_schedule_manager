<?php

return [
   'teachers' => [
      [
         'field' => 'teacher_full_name',
         'header' => 'Ф.И.О'
      ],
      [
         'field' => 'birth_year',
         'header' => 'Год рождения'
      ],
      [
         'field' => 'phone',
         'header' => 'Телефон'
      ],
      [
         'field' => 'email',
         'header' => 'Электронная почта'
      ],
      [
         'field' => [
            'faculty',
            'name'
         ],
         'header' => 'Факультет'
      ],
      [
         'field' => [
            'department',
            'name'
         ],
         'header' => 'Кафедра'
      ],
      [
         'field' => [
            'professional_level',
            'name'
         ],
         'header' => 'Профессиональный уровень'
      ],
      [
         'field' => [
            'position',
            'name'
         ],
         'header' => 'Должность'
      ],
   ],
   'groups' => [
      [
         'field' => 'name',
         'header' => 'Название'
      ],
      [
         'field' => [
            'faculty',
            'name'
         ],
         'header' => 'Факультет'
      ],
      [
         'field' => [
            'study_program',
            'name'
         ],
         'header' => 'Учебная программа'
      ],
      [
         'field' => [
            'study_orientation',
            'name'
         ],
         'header' => 'Специальность'
      ],
      [
         'field' => [
            'study_degree',
            'name'
         ],
         'header' => 'Уровень образования'
      ],
      [
         'field' => [
            'study_form',
            'name'
         ],
         'header' => 'Форма образования'
      ],
      [
         'field' => 'course',
         'header' => 'Курс'
      ],
      [
         'field' => 'size',
         'header' => 'Численность'
      ],
   ]
];