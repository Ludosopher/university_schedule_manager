<?php

return [
   'teacher' => [
      [
         'type' => 'enum-select',
         'plural_name' => 'genders',
         'name' => 'gender',
<<<<<<< HEAD
         'header' => 'Пол'
=======
         'header' => 'Пол',
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'last_name',
         'header' => 'Фамилия',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'first_name',
<<<<<<< HEAD
         'header' => 'Имя'
=======
         'header' => 'Имя',
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'patronymic',
<<<<<<< HEAD
         'header' => 'Отчество'
=======
         'header' => 'Отчество',
         'is_required' => false,
>>>>>>> develop
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'birth_year',
<<<<<<< HEAD
         'header' => 'Год рождения'
=======
         'header' => 'Год рождения',
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'phone',
         'header' => 'Телефон',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'input',
         'input_type' => 'email',
         'name' => 'email',
         'header' => 'Электронная почта',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'faculties',
         'name' => 'faculty',
         'header' => 'Факультет',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'departments',
         'name' => 'department',
         'header' => 'Кафедра',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'professional_levels',
         'name' => 'professional_level',
         'header' => 'Профессиональный уровень',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'positions',
         'name' => 'position',
         'header' => 'Должность',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'academic_degrees',
         'name' => 'academic_degree',
         'header' => 'Учёная степень',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
   ],
   'teacher_filter' => [
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'full_name',
         'header' => 'Фамилия, Имя или Отчество'
      ],
      [
         'type' => 'between',
         'name' => 'age',
         'header' => 'Возраст',
         'min_value' => '',
         'max_value' => '',
         'step' => '1'
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
             // 'explanation' => "Для выбора нескольких факультетов нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'faculties',
         'name' => 'faculty',
         'header' => 'Факультет',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
             // 'explanation' => "Для выбора нескольких кафедр нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'departments',
         'name' => 'department',
         'header' => 'Кафедра',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
             // 'explanation' => "Для выбора нескольких уровней нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'professional_levels',
         'name' => 'professional_level',
         'header' => 'Профессиональный уровень',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
             // 'explanation' => "Для выбора нескольких должностей нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'positions',
         'name' => 'position',
         'header' => 'Должность',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
             // 'explanation' => "Для выбора нескольких степеней нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'academic_degrees',
         'name' => 'academic_degree',
         'header' => 'Учёная степень',
      ]
   ],
   'group' => [
      [
         'type' => 'objects-select',
         'plural_name' => 'faculties',
         'name' => 'faculty',
         'header' => 'Факультет',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_programs',
         'name' => 'study_program',
         'header' => 'Учебная программа',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_orientations',
         'name' => 'study_orientation',
         'header' => 'Специальность',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_degrees',
         'name' => 'study_degree',
         'header' => 'Уровень образования',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_forms',
         'name' => 'study_form',
         'header' => 'Форма образования',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'courses',
         'name' => 'course',
         'header' => 'Курс',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'size',
         'header' => 'Численность',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
   ],
   'group_filter' => [
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
         ],
         'plural_name' => 'groups',
         'name' => '',
         'header' => 'Группа',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'faculties',
         'name' => 'faculty',
         'header' => 'Факультет',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'study_programs',
         'name' => 'study_program',
         'header' => 'Учебная программа',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'study_orientations',
         'name' => 'study_orientation',
         'header' => 'Специальность',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'study_degrees',
         'name' => 'study_degree',
         'header' => 'Уровень образования',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'study_forms',
         'name' => 'study_form',
         'header' => 'Форма образования',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'courses',
         'name' => 'course',
         'header' => 'Курс',
      ],
      [
         'type' => 'between',
         'name' => 'size',
         'header' => 'Численность',
         'min_value' => '1',
         'max_value' => '50',
         'step' => '1'
      ],
   ],
   'lesson' => [
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'name',
         'header' => 'Предмет',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'lesson_types',
         'name' => 'lesson_type',
         'header' => 'Вид',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'week_days',
         'name' => 'week_day',
         'header' => 'День недели',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'weekly_periods',
         'name' => 'weekly_period',
         'header' => 'Недельная периодичность',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'class_periods',
         'name' => 'class_period',
         'header' => 'Пара',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 5,
             'explanation' => "Для выбора нескольких групп нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора."
         ],
         'plural_name' => 'groups',
         'name' => 'group',
         'header' => 'Группа',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'teachers',
         'name' => 'teacher',
         'header' => 'Преподаватель',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'lesson_rooms',
         'name' => 'lesson_room',
         'header' => 'Аудитория',
<<<<<<< HEAD
=======
         'is_required' => true,
>>>>>>> develop
      ],
      [
         'type' => 'input',
         'input_type' => 'date',
         'name' => 'date',
         'header' => 'Дата',
<<<<<<< HEAD
=======
         'is_required' => false,
>>>>>>> develop
      ],
   ],
   'lesson_filter' => [
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'name',
         'header' => 'Предмет',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2
         ],
         'plural_name' => 'lesson_types',
         'name' => 'lesson_type',
         'header' => 'Вид',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2
         ],
         'plural_name' => 'week_days',
         'name' => 'week_day',
         'header' => 'День недели',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2
         ],
         'plural_name' => 'weekly_periods',
         'name' => 'weekly_period',
         'header' => 'Недельная периодичность',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2
         ],
         'plural_name' => 'class_periods',
         'name' => 'class_period',
         'header' => 'Пара',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3
         ],
         'plural_name' => 'groups',
         'name' => 'group',
         'header' => 'Группа',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3
         ],
         'plural_name' => 'teachers',
         'name' => 'teacher',
         'header' => 'Преподаватель',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3
         ],
         'plural_name' => 'lesson_rooms',
         'name' => 'lesson_room',
         'header' => 'Аудитория',
      ],
   ],
   'lesson_replacement_filter' => [
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
             // 'explanation' => "Для выбора нескольких факультетов нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'week_days',
         'name' => 'week_day',
         'header' => 'День недели',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
             // 'explanation' => "Для выбора нескольких факультетов нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'weekly_periods',
         'name' => 'weekly_period',
         'header' => 'Недельная периодичность',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
             // 'explanation' => "Для выбора нескольких факультетов нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'class_periods',
         'name' => 'class_period',
         'header' => 'Пара',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
             // 'explanation' => "Для выбора нескольких факультетов нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'faculties',
         'name' => 'faculty',
         'header' => 'Факультет',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
             // 'explanation' => "Для выбора нескольких кафедр нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'departments',
         'name' => 'department',
         'header' => 'Кафедра',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
             // 'explanation' => "Для выбора нескольких уровней нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'professional_levels',
         'name' => 'professional_level',
         'header' => 'Профессиональный уровень',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
             // 'explanation' => "Для выбора нескольких должностей нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'positions',
         'name' => 'position',
         'header' => 'Должность',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
             // 'explanation' => "Для выбора нескольких должностей нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'lesson_rooms',
         'name' => 'lesson_room',
         'header' => 'Аудитория',
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
             // 'explanation' => "Для выбора нескольких должностей нажмите и удерживайте клавишу 'Ctrl'"
         ],
         'plural_name' => 'schedule_positions',
         'name' => 'schedule_position',
         'header' => 'Позиция в расписании заменяющего преподавателя',
       ],
<<<<<<< HEAD
   ]
   

   
];
=======
      ],
      'user' => [
         [
            'type' => 'switch',
            'input_type' => null,
            'name' => 'is_moderator',
            'header' => 'Модератор',
            'is_required' => false,
         ],
         [
            'type' => 'switch',
            'input_type' => null,
            'name' => 'is_admin',
            'header' => 'Администратор',
            'is_required' => false,
         ],
         [
            'type' => 'objects-select',
            'multiple_options' => [
                'is_multiple' => true,
                'size' => 5,
                'explanation' => "Для выбора нескольких преподавателей нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора."
            ],
            'plural_name' => 'teachers',
            'name' => 'teacher',
            'header' => 'Допуск к управлению расписанием преподавателей',
            'is_required' => false,
         ],
         [
            'type' => 'objects-select',
            'multiple_options' => [
                'is_multiple' => true,
                'size' => 5,
                'explanation' => "Для выбора нескольких групп нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора."
            ],
            'plural_name' => 'groups',
            'name' => 'group',
            'header' => 'Допуск к управлению расписанием групп',
            'is_required' => false,
         ],
      ],
      'user_filter' => [
         [
            'type' => 'input',
            'input_type' => 'text',
            'name' => 'name',
            'header' => 'Имя',
         ],
         [
            'type' => 'switch',
            'input_type' => null,
            'name' => 'is_moderator',
            'header' => 'Модератор',
            'is_required' => true,
         ],
         [
            'type' => 'switch',
            'input_type' => null,
            'name' => 'is_admin',
            'header' => 'Администратор',
            'is_required' => true,
         ],
         [
            'type' => 'objects-select',
            'multiple_options' => [
                'is_multiple' => true,
                'size' => 5,
                'explanation' => "Для выбора нескольких преподавателей нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора."
            ],
            'plural_name' => 'teachers',
            'name' => 'teacher',
            'header' => 'Допуск к управлению расписанием преподавателей',
            'is_required' => true,
         ],
         [
            'type' => 'objects-select',
            'multiple_options' => [
                'is_multiple' => true,
                'size' => 5,
                'explanation' => "Для выбора нескольких групп нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора."
            ],
            'plural_name' => 'groups',
            'name' => 'group',
            'header' => 'Допуск к управлению расписанием групп',
            'is_required' => true,
         ],
      ],

];
>>>>>>> develop