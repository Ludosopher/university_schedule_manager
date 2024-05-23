<?php

return [
   'teacher' => [
      [
         'type' => 'enum-select',
         'plural_name' => 'genders',
         'name' => 'gender',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'last_name',
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'first_name',
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'patronymic',
         'is_required' => false,
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'birth_year',
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'phone',
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'email',
         'name' => 'email',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'faculties',
         'name' => 'faculty',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'departments',
         'name' => 'department',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'professional_levels',
         'name' => 'professional_level',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'positions',
         'name' => 'position',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'academic_degrees',
         'name' => 'academic_degree',
         'is_required' => true,
         'is_localized' => true,
      ],
   ],
   'teacher_filter' => [
      [
         'type' => 'input',
         'input_type' => 'text',
         'name' => 'full_name',
      ],
      [
         'type' => 'between',
         'name' => 'age',
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
         ],
         'plural_name' => 'faculties',
         'name' => 'faculty',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'departments',
         'name' => 'department',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'professional_levels',
         'name' => 'professional_level',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'positions',
         'name' => 'position',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'academic_degrees',
         'name' => 'academic_degree',
         'is_localized' => true,
      ]
   ],
   'group' => [
      [
         'type' => 'objects-select',
         'plural_name' => 'faculties',
         'name' => 'faculty',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_programs',
         'name' => 'study_program',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_orientations',
         'name' => 'study_orientation',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_degrees',
         'name' => 'study_degree',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_forms',
         'name' => 'study_form',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'courses',
         'name' => 'course',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'size',
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
         'is_localized' => false,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'faculties',
         'name' => 'faculty',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'study_programs',
         'name' => 'study_program',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'study_orientations',
         'name' => 'study_orientation',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'study_degrees',
         'name' => 'study_degree',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'study_forms',
         'name' => 'study_form',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2,
         ],
         'plural_name' => 'courses',
         'name' => 'course',
         'is_localized' => true,
      ],
      [
         'type' => 'between',
         'name' => 'size',
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
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'study_periods',
         'name' => 'study_period',
         'is_required' => true,
         'is_localized' => false,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'lesson_types',
         'name' => 'lesson_type',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'week_days',
         'name' => 'week_day',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'weekly_periods',
         'name' => 'weekly_period',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'class_periods',
         'name' => 'class_period',
         'is_required' => true,
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 5,
         ],
         'plural_name' => 'groups',
         'name' => 'group',
         'is_required' => true,
         'is_localized' => false,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'teachers',
         'name' => 'teacher',
         'is_required' => true,
         'is_localized' => false,
      ],
      [
         'type' => 'objects-select',
         'plural_name' => 'lesson_rooms',
         'name' => 'lesson_room',
         'is_required' => true,
         'is_localized' => false,
      ],
      [
         'type' => 'input',
         'input_type' => 'date',
         'name' => 'date',
         'is_required' => false,
      ],
   ],
   'lesson_filter' => [
      // [
      //    'type' => 'input',
      //    'input_type' => 'text',
      //    'name' => 'name',
      // ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2
         ],
         'plural_name' => 'study_periods',
         'name' => 'study_period',
         'is_localized' => false,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2
         ],
         'plural_name' => 'lesson_types',
         'name' => 'lesson_type',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2
         ],
         'plural_name' => 'week_days',
         'name' => 'week_day',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2
         ],
         'plural_name' => 'weekly_periods',
         'name' => 'weekly_period',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 2
         ],
         'plural_name' => 'class_periods',
         'name' => 'class_period',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3
         ],
         'plural_name' => 'groups',
         'name' => 'group',
         'is_localized' => false,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3
         ],
         'plural_name' => 'teachers',
         'name' => 'teacher',
         'is_localized' => false,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3
         ],
         'plural_name' => 'lesson_rooms',
         'name' => 'lesson_room',
         'is_localized' => false,
      ],
   ],
   'lesson_replacement_filter' => [
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
         ],
         'plural_name' => 'week_days',
         'name' => 'week_day',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
         ],
         'plural_name' => 'weekly_periods',
         'name' => 'weekly_period',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
         ],
         'plural_name' => 'class_periods',
         'name' => 'class_period',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
         ],
         'plural_name' => 'faculties',
         'name' => 'faculty',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
         ],
         'plural_name' => 'departments',
         'name' => 'department',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
         ],
         'plural_name' => 'professional_levels',
         'name' => 'professional_level',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
         ],
         'plural_name' => 'positions',
         'name' => 'position',
         'is_localized' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
         ],
         'plural_name' => 'lesson_rooms',
         'name' => 'lesson_room',
         'is_localized' => false,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
             'is_multiple' => true,
             'size' => 3,
         ],
         'plural_name' => 'schedule_positions',
         'name' => 'schedule_position',
         'is_localized' => true,
      ],
   ],
   'user' => [
      [
         'type' => 'switch',
         'input_type' => null,
         'name' => 'is_moderator',
         'is_required' => false,
      ],
      [
         'type' => 'switch',
         'input_type' => null,
         'name' => 'is_admin',
         'is_required' => false,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
               'is_multiple' => true,
               'size' => 5,
         ],
         'plural_name' => 'teachers',
         'name' => 'teacher',
         'is_required' => false,
         'is_localized' => false,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
               'is_multiple' => true,
               'size' => 5,
         ],
         'plural_name' => 'groups',
         'name' => 'group',
         'is_required' => false,
         'is_localized' => false,
      ],
   ],
   'user_filter' => [
         [
            'type' => 'input',
            'input_type' => 'text',
            'name' => 'name',
            'is_required' => false,
         ],
         [
            'type' => 'switch',
            'input_type' => null,
            'name' => 'is_moderator',
            'is_required' => true,
         ],
         [
            'type' => 'switch',
            'input_type' => null,
            'name' => 'is_admin',
            'is_required' => true,
         ],
         [
            'type' => 'objects-select',
            'multiple_options' => [
                'is_multiple' => true,
                'size' => 5,
            ],
            'plural_name' => 'teachers',
            'name' => 'teacher',
            'is_required' => true,
            'is_localized' => false,
         ],
         [
            'type' => 'objects-select',
            'multiple_options' => [
                'is_multiple' => true,
                'size' => 5,
            ],
            'plural_name' => 'groups',
            'name' => 'group',
            'is_required' => true,
            'is_localized' => false,
         ],
   ],
   'replacement_request_filter' => [
      [
         'type' => 'objects-select',
         'multiple_options' => [
               'is_multiple' => true,
               'size' => 3,
         ],
         'plural_name' => 'groups',
         'name' => 'group',
         'is_required' => true,
         'is_localized' => false,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
               'is_multiple' => true,
               'size' => 3,
         ],
         'plural_name' => 'teachers',
         'name' => 'teacher',
         'is_required' => true,
         'is_localized' => false,
      ],
      [
         'type' => 'between',
         'name' => 'date',
         'input_type' => 'date',
         'min_value' => '',
         'max_value' => '',
         'step' => '1'
      ],
      [
         'type' => 'switch',
         'input_type' => null,
         'name' => 'is_regular',
         'is_required' => true,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
               'is_multiple' => true,
               'size' => 3,
         ],
         'plural_name' => 'users',
         'name' => 'user',
         'is_required' => true,
         'is_localized' => false,
      ],
      [
         'type' => 'objects-select',
         'multiple_options' => [
               'is_multiple' => true,
               'size' => 3,
         ],
         'plural_name' => 'statuses',
         'name' => 'status',
         'is_required' => true,
         'is_localized' => true,
      ],
   ],
   'settings' => [
      [
         'type' => 'switch',
         'input_type' => null,
         'name' => 'red_week_is_odd',
         'is_required' => false,
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'full_time_week_days_limit',
         'max' => 6,
         'min' => 5,
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'distance_week_days_limit',
         'max' => 6,
         'min' => 5,
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'full_time_class_periods_limit',
         'max' => 5,
         'min' => 4,
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'distance_class_periods_limit',
         'max' => 5,
         'min' => 4,
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'default_rows_per_page',
         'max' => 50,
         'min' => 5,
         'is_required' => true,
      ],
      [
         'type' => 'input',
         'input_type' => 'number',
         'name' => 'min_replacement_period',
         'min' => 2,
         'is_required' => true,
      ],
   ],


];
