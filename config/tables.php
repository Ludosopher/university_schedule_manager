<?php

return [
   'teachers' => [
      [
         'field' => 'full_name',
         'header' => 'full_name',
         'sorting' => true
      ],
      [
         'field' => 'age',
         'header' => 'age',
         'sorting' => true
      ],
      [
         'field' => 'phone',
         'header' => 'phone',
         'sorting' => false
      ],
      [
         'field' => 'email',
         'header' => 'email',
         'sorting' => false
      ],
      [
         'field' => [
            'faculty',
            'name'
         ],
         'header' => 'faculty',
         'sorting' => true
      ],
      [
         'field' => [
            'department',
            'name'
         ],
         'header' => 'department',
         'sorting' => true
      ],
      [
         'field' => [
            'professional_level',
            'name'
         ],
         'header' => 'professional_level',
         'sorting' => true
      ],
      [
         'field' => [
            'position',
            'name'
         ],
         'header' => 'position',
         'sorting' => true
      ],
      [
         'field' => [
            'academic_degree',
            'name'
         ],
         'header' => 'academic_degree',
         'sorting' => true
      ],
   ],
   'groups' => [
      [
         'field' => 'name',
         'header' => 'name',
         'sorting' => false
      ],
      [
         'field' => [
            'faculty',
            'name'
         ],
         'header' => 'faculty',
         'sorting' => true
      ],
      [
         'field' => [
            'study_program',
            'name'
         ],
         'header' => 'study_program',
         'sorting' => true
      ],
      [
         'field' => [
            'study_orientation',
            'name'
         ],
         'header' => 'study_orientation',
         'sorting' => true
      ],
      [
         'field' => [
            'study_degree',
            'name'
         ],
         'header' => 'study_degree',
         'sorting' => true
      ],
      [
         'field' => [
            'study_form',
            'name'
         ],
         'header' => 'study_form',
         'sorting' => true
      ],
      [
         'field' => [
            'course',
            'number'
         ],
         'header' => 'course',
         'sorting' => true
      ],
      [
         'field' => 'size',
         'header' => 'size',
         'sorting' => true
      ],
   ],
   'lessons' => [
      [
         'field' => [
            'class_period',
            'id'
         ],
         'header' => 'class_period',
         'sorting' => true
      ],
      [
         'field' => 'name',
         'header' => 'subject',
         'sorting' => true
      ],
      [
         'field' => [
            'lesson_type',
            'name'
         ],
         'header' => 'lesson_type',
         'sorting' => true
      ],
      [
        'field' => [
           'lesson_room',
           'number'
        ],
        'header' => 'lesson_room',
        'sorting' => true
      ],
      [
         'field' => [
            'week_day',
            'name'
         ],
         'header' => 'week_day',
         'sorting' => true
      ],
      [
         'field' => [
            'weekly_period',
            'name'
         ],
         'header' => 'weekly_period',
         'sorting' => true
      ],
      [
         'field' => 'groups_name',
         'header' => 'group',
         'sorting' => false
      ],
      [
         'field' => [
            'teacher',
            'profession_level_name'
         ],
         'sort_name' => 'profession_level_name',
         'header' => 'teacher',
         'sorting' => true
      ],
   ],
   'replacement_variants' => [
      [
         'field' => 'subject',
         'header' => 'subject',
         'sorting' => null
      ],
      [
         'field' => 'week_day_id',
         'header' => 'week_day',
         'sorting' => null
      ],
      [
         'field' => 'weekly_period_id',
         'header' => 'weekly_period',
         'sorting' => null
      ],
      [
         'field' => 'class_period_id',
         'header' => 'class_period',
         'sorting' => null
      ],
      [
         'field' => 'lesson_room_id',
         'header' => 'lesson_room',
         'sorting' => null
      ],
      [
         'field' => 'profession_level_name',
         'header' => 'replacing_teacher',
         'sorting' => null
      ],
      [
         'field' => 'phone',
         'header' => 'phone',
         'sorting' => null
      ],
      [
         'field' => 'age',
         'header' => 'age',
         'sorting' => null
      ],
      [
         'field' => 'department_id',
         'header' => 'department',
         'sorting' => null
      ],
      [
         'field' => 'position_id',
         'header' => 'position',
         'sorting' => null
      ],
      [
         'field' => 'schedule_position_id',
         'header' => 'schedule_position',
         'sorting' => null
      ],
   ],
   'users' => [
      [
         'field' => 'name',
         'header' => 'nickname',
         'sorting' => true
      ],
      [
         'field' => 'phone',
         'header' => 'phone',
         'sorting' => false
      ],
      [
         'field' => 'email',
         'header' => 'email',
         'sorting' => false
      ],
      [
         'field' => [
            'moderator',
         ],
         'sort_name' => 'is_moderator',
         'header' => 'moderator',
         'sorting' => true
      ],
      [
         'field' => [
            'admin',
         ],
         'sort_name' => 'is_admin',
         'header' => 'admin',
         'sorting' => true
      ],
      [
         'field' => 'teacher_names',
         'header' => 'teacher_names',
         'sorting' => false
      ],
      [
         'field' => 'group_names',
         'header' => 'group_names',
         'sorting' => false
      ],
   ],
   'replacement_requests' => [
      [
         'field' => [
            'replaceable_lesson',
            'groups_name'
         ],
         'header' => 'group',
         'sorting' => false
      ],
      [
         'field' => [
            'regular',
         ],
         'sort_name' => 'is_regular',
         'header' => 'is_regular',
         'sorting' => false
      ],
      [
         'field' => 'replaceable_date',
         'header' => 'date',
         'sorting' => false
      ],
      [
         'field' => [
            'replaceable_lesson',
            'week_day',
            'name'
         ],
         'header' => 'week_day',
         'sorting' => false
      ],
      [
         'field' => [
            'replaceable_lesson',
            'class_period',
            'id'
         ],
         'header' => 'class_period',
         'sorting' => false
      ],
      [
         'field' => [
            'replaceable_lesson',
            'lesson_room',
            'number'
         ],
         'header' => 'lesson_room',
         'sorting' => false
      ],
      [
         'field' => [
            'replaceable_lesson',
            'teacher',
            'profession_level_name'
         ],
         'header' => 'teacher',
         'sorting' => false
      ],
      [
         'field' => 'replacing_date',
         'header' => 'date',
         'sorting' => false
      ],
      [
         'field' => [
            'replacing_lesson',
            'week_day',
            'name'
         ],
         'header' => 'week_day',
         'sorting' => false
      ],
      [
         'field' => [
            'replacing_lesson',
            'class_period',
            'id'
         ],
         'header' => 'class_period',
         'sorting' => false
      ],
      [
         'field' => [
            'replacing_lesson',
            'lesson_room',
            'number'
         ],
         'header' => 'lesson_room',
         'sorting' => false
      ],
      [
         'field' => [
            'replacing_lesson',
            'teacher',
            'profession_level_name'
         ],
         'header' => 'teacher',
         'sorting' => false
      ],
      [
         'field' => [
            'status',
            'name'
         ],
         'header' => 'status',
         'sorting' => false
      ],
      [
         'field' => [
            'initiator',
            'name'
         ],
         'header' => 'initiator',
         'sorting' => false
      ],
   ],
];
