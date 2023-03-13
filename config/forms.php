<?php

return [
   'teacher' => [
      [
         'type' => 'enum-select',
         'plural_name' => 'genders',
         'name' => 'gender',
         'header' => 'Пол',
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'last_name',
         'header' => 'Фамилия',
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'first_name',
         'header' => 'Имя',
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'patronymic',
         'header' => 'Отчество',
         'is_required' => false,
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'birth_year',
         'header' => 'Год рождения',
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'phone',
         'header' => 'Телефон',
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'email',
         'name' => 'email',
         'header' => 'Электронная почта',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'faculties',
         'name' => 'faculty',
         'header' => 'Факультет',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'departments',
         'name' => 'department',
         'header' => 'Кафедра',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'professional_levels',
         'name' => 'professional_level',
         'header' => 'Профессиональный уровень',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'positions',
         'name' => 'position',
         'header' => 'Должность',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'academic_degrees',
         'name' => 'academic_degree',
         'header' => 'Учёная степень',
         'is_required' => true,
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
         'input_type' => 'number',
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
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_programs',
         'name' => 'study_program',
         'header' => 'Учебная программа',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_orientations',
         'name' => 'study_orientation',
         'header' => 'Специальность',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_degrees',
         'name' => 'study_degree',
         'header' => 'Уровень образования',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_forms',
         'name' => 'study_form',
         'header' => 'Форма образования',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'courses',
         'name' => 'course',
         'header' => 'Курс',
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'size',
         'header' => 'Численность',
         'is_required' => true,
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
         'input_type' => 'number',
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
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'lesson_types',
         'name' => 'lesson_type',
         'header' => 'Вид',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'week_days',
         'name' => 'week_day',
         'header' => 'День недели',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'weekly_periods',
         'name' => 'weekly_period',
         'header' => 'Недельная периодичность',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'class_periods',
         'name' => 'class_period',
         'header' => 'Пара',
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
         'header' => 'Группа',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'teachers',
         'name' => 'teacher',
         'header' => 'Преподаватель',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'lesson_rooms',
         'name' => 'lesson_room',
         'header' => 'Аудитория',
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'date',
         'name' => 'date',
         'header' => 'Дата',
         'is_required' => false,
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
      'replacement_request_filter' => [
         [
            'type' => 'objects-select',
            'multiple_options' => [
                'is_multiple' => true,
                'size' => 3,
                'explanation' => "Для выбора нескольких групп нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора."
            ],
            'plural_name' => 'groups',
            'name' => 'group',
            'header' => 'Группа(ы)',
            'is_required' => true,
         ],
         [
            'type' => 'objects-select',
            'multiple_options' => [
                'is_multiple' => true,
                'size' => 3,
                'explanation' => "Для выбора нескольких преподавателей нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора."
            ],
            'plural_name' => 'teachers',
            'name' => 'teacher',
            'header' => 'Преподаватель(и)',
            'is_required' => true,
         ],
         [
            'type' => 'between',
            'name' => 'date',
            'header' => 'Дата',
            'input_type' => 'date',
            'min_value' => '',
            'max_value' => '',
            'step' => '1'
         ],
         [
            'type' => 'switch',
            'input_type' => null,
            'name' => 'is_regular',
            'header' => 'Постоянная замена',
            'is_required' => true,
         ],
         [
            'type' => 'objects-select',
            'multiple_options' => [
                'is_multiple' => true,
                'size' => 3,
                'explanation' => "Для выбора нескольких инициаторов нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора."
            ],
            'plural_name' => 'users',
            'name' => 'user',
            'header' => 'Инициатор(ы)',
            'is_required' => true,
         ],
         [
            'type' => 'objects-select',
            'multiple_options' => [
                'is_multiple' => true,
                'size' => 3,
                'explanation' => "Для выбора нескольких статусов нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора."
            ],
            'plural_name' => 'statuses',
            'name' => 'status',
            'header' => 'Статус(ы)',
            'is_required' => true,
         ],
         
      ],
      'settings' => [
         [
            'type' => 'switch',
            'input_type' => null,
            'name' => 'red_week_is_odd',
            'header' => '"Красная"(Первая) неделя - нечётная',
            'is_required' => false,
         ],
         [
            'type' => 'input',
            'input_type' => 'number',
            'name' => 'full_time_week_days_limit',
            'header' => 'Число дней недели на очном обучении',
            'max' => 6,
            'min' => 5,
            'is_required' => true,
         ],
         [
            'type' => 'input',
            'input_type' => 'number',
            'name' => 'distance_week_days_limit',
            'header' => 'Число дней недели на заочном обучении',
            'max' => 6,
            'min' => 5,
            'is_required' => true,
         ],
         [
            'type' => 'input',
            'input_type' => 'number',
            'name' => 'full_time_class_periods_limit',
            'header' => 'Максимальное число пар в день на очном обучении',
            'max' => 5,
            'min' => 4,
            'is_required' => true,
         ],
         [
            'type' => 'input',
            'input_type' => 'number',
            'name' => 'distance_class_periods_limit',
            'header' => 'Максимальное число пар в день на заочном обучении',
            'max' => 5,
            'min' => 4,
            'is_required' => true,
         ],
         [
            'type' => 'input',
            'input_type' => 'number',
            'name' => 'default_rows_per_page',
            'header' => 'Изначальное число строк на странице',
            'max' => 50,
            'min' => 5,
            'is_required' => true,
         ],
         [
            'type' => 'input',
            'input_type' => 'number',
            'name' => 'min_replacement_period',
            'header' => 'Минимальное допустимое время перед началом замены, часов',
            'min' => 2,
            'is_required' => true,
         ],
      ],


];
