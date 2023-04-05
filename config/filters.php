<?php

return [
   'teacher' => [
      'full_name' => [
         'method' => 'where',
         'operator' => [
             'first_name' => [
                 'method' => 'orWhere',
                 'operator' => 'like'
             ],
             'last_name' => [
                 'method' => 'orWhere',
                 'operator' => 'like'
             ],
             'patronymic' => [
                 'method' => 'orWhere',
                 'operator' => 'like'
             ],
         ]
      ],
      'age_from' => [
         'db_field' => 'birth_year',
         'method' => 'where',
         'operator' => '<',
         'calculated_value' => function ($age) {
             return now()->subYear($age);
         } 
      ],
      'age_to' => [
         'db_field' => 'birth_year',
         'method' => 'where',
         'operator' => '>',
         'calculated_value' => function ($age) {
             return now()->subYear($age);
         }
      ],
      'faculty_id' => [
         'method' => 'whereIn',
      ],
      'department_id' => [
         'method' => 'whereIn',
      ],
      'professional_level_id' => [
         'method' => 'whereIn',
      ],
      'position_id' => [
         'method' => 'whereIn',
      ],
      'academic_degree_id' => [
         'method' => 'whereIn',
      ],
   ],
   'group' => [
      'id' => [
         'method' => 'whereIn'
      ], 
      'faculty_id' => [
         'method' => 'whereIn'
      ],
      'study_program_id' => [
         'method' => 'whereIn'
      ],
      'study_orientation_id' => [
         'method' => 'whereIn'
      ],
      'study_degree_id' => [
         'method' => 'whereIn'
      ],
      'study_form_id' => [
         'method' => 'whereIn'
      ],
      'course_id' => [
         'method' => 'whereIn'
      ],
      'size_from' => [
         'db_field' => 'size',
         'method' => 'where',
         'operator' => '>='
      ],
      'size_to' => [
         'db_field' => 'size',
         'method' => 'where',
         'operator' => '<='
     ],
   ],
   'lesson' => [
      // 'name' => [
      //    'method' => 'where',
      //    'operator' => 'like'
      // ],
      'lesson_type_id' => [
         'method' => 'whereIn'
      ],
      'week_day_id' => [
         'method' => 'whereIn'
      ],
      'weekly_period_id' => [
         'method' => 'whereIn'
      ],
      'class_period_id' => [
         'method' => 'whereIn'
      ],
      'group_id' => [
         'method' => 'whereHas',
         'operator' => [
             'id' => [
                 'method' => 'where',
                 'operator' => '='
             ],
         ],
         'eager_field' => 'groups',
      ],
      'teacher_id' => [
         'method' => 'whereIn',
      ],
      'lesson_room_id' => [
         'method' => 'whereIn',
      ],
      'week_number' => [
         'db_field' => 'WEEK(date) = ? OR date IS NULL',
         'method' => 'whereRaw',
         'calculated_value' => function ($week_number) {
             return date('W', strtotime($week_number));
         } 
      ],
   ],
   'lesson_replacement' => [
      'week_day_id' => [
         'operator' => 'multi_not_equal'
      ],
      'weekly_period_id' => [
         'operator' => 'multi_not_equal'
      ],
      'class_period_id' => [
         'operator' => 'multi_not_equal'
      ],
      'faculty_id' => [
         'operator' => 'multi_not_equal'
      ],
      'department_id' => [
         'operator' => 'multi_not_equal'
      ],
      'professional_level_id' => [
         'operator' => 'multi_not_equal'
      ],
      'position_id' => [
         'operator' => 'multi_not_equal'
      ],
      'lesson_room_id' => [
         'operator' => 'multi_not_equal'
      ],
      'schedule_position_id' => [
         'operator' => 'multi_not_equal'
      ]
   ],
   'user' => [
      'name' => [
         'method' => 'where',
         'operator' => 'like'
      ],
      'teacher_id' => [
         'method' => 'whereHas',
         'operator' => [
             'id' => [
                 'method' => 'where',
                 'operator' => '='
             ],
         ],
         'eager_field' => 'teachers',
      ],
      'group_id' => [
         'method' => 'whereHas',
         'operator' => [
             'id' => [
                 'method' => 'where',
                 'operator' => '='
             ],
         ],
         'eager_field' => 'groups',
      ],
      'is_moderator' => [
         'method' => 'where',
         'operator' => '='
      ],
      'is_admin' => [
         'method' => 'where',
         'operator' => '='
      ],
   ],
   'replacement_request' => [
      'group_id' => [
         'method' => 'whereHas',
         'operator' => [
             'id' => [
                 'method' => 'where',
                 'operator' => '='
             ],
         ],
         'eager_field' => 'replaceable_lesson.groups',
      ],
      'teacher_id' => [
         'method' => 'where',
         'operator' => [
            'replaceable_lesson.teacher' => [
               'final_field' => 'id', 
               'method' => 'orWhereHas',
               'operator' => '='
            ],
            'replacing_lesson.teacher' => [
               'final_field' => 'id', 
               'method' => 'orWhereHas',
               'operator' => '='
            ],
         ]
      ],
      'date_from' => [
         'method' => 'where',
         'operator' => [
            'replaceable_date' => [
               'method' => 'orWhere',
               'operator' => '>'
            ],
            'replacing_date' => [
               'method' => 'orWhere',
               'operator' => '>'
            ],
         ]
      ],
      'date_to' => [
         'method' => 'where',
         'operator' => [
            'replaceable_date' => [
               'method' => 'orWhere',
               'operator' => '<'
            ],
            'replacing_date' => [
               'method' => 'orWhere',
               'operator' => '<'
            ],
         ]
      ],
      'is_regular' => [
         'method' => 'where',
         'operator' => '='
      ],
      'user_id' => [
         'db_field' => 'initiator_id',
         'method' => 'where',
         'operator' => '='
      ],
      'status_id' => [
         'method' => 'where',
         'operator' => '='
      ],
   ],
  
];
