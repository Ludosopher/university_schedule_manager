<?php

return [
   'teachers' => [
      [
         'field' => 'full_name',
         'header' => 'Ф.И.О',
         'sorting' => false
      ],
      [
         'field' => 'birth_year',
         'header' => 'Год рождения',
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
         'sorting' => true
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
         'field' => 'course',
         'header' => 'Курс',
         'sorting' => true
      ],
      [
         'field' => 'size',
         'header' => 'Численность',
         'sorting' => true
      ],
   ]
];