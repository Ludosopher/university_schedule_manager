<?php

return [
   
   'faculties' => [
        1 => 'Инженерно-мелиоративный',
        2 => 'Бизнеса и социальных технологий',
        3 => 'Землеустроительный',
   ],

   'faculty_ids' => [
      'engineering_and_reclamation' => 1,
      'business_and_social_technologies' => 2,
      'land_management' => 3
   ],

   'genders' => [
      'male' => 'мужчина',
      'female' => 'женщина',
      'not_specified' => 'не указано'
   ],

   'class_period_ids' => [
      'first' => 1,
      'second' => 2,
      'third' => 3,
      'fourth' => 4,
      'fifth' => 5
   ],

   'class_periods' => [
      1 => 'first',
      2 => 'second',
      3 => 'third',
      4 => 'fourth',
      5 => 'fifth'
   ],

   'week_day_ids' => [
      'monday' => 1,
      'tuesday' => 2,
      'wednesday' => 3,
      'thursday' => 4,
      'friday' => 5,
      'saturday' => 6
   ],

   'week_days_ru' => [
      1 => 'понедельник',
      2 => 'вторник',
      3 => 'среда',
      4 => 'четверг',
      5 => 'пятница',
      6 => 'суббота'
   ],

   'weekly_periods' => [
      1 => 'every_week',
      2 => 'red_week',
      3 => 'blue_week'
   ],

   'lesson_type_ids' => [
      'lecture' => 1,
      'practical' => 2,
      'laboratory' => 3
   ],

   'lesson_types' => [
      1 => 'lecture',
      2 => 'practical',
      3 => 'laboratory'
   ],

   'weekly_period_ids' => [
      'every_week' => 1,
      'red_week' => 2,
      'blue_week' => 3
   ],

   'weekly_period_colors' => [
      1 => 'White',
      2 => '#ffb3b9',
      3 => '#ace7f2'
   ],

   'free_weekly_period_colors' => [
      1 => '#bcf5bc',
      2 => '#ffb3b9',
      3 => '#ace7f2'
   ],

   'schedule_positions' => [
      ['id' => 1,
      'name' => 'Между двумя имеющимися парами'],
      ['id' => 2,
      'name' => 'Рядом с одной из имеющихся пар'],
      ['id' => 3,
      'name' => 'Нет рядом имеющихся пар'],
   ],

   'schedule_position_ids' => [
      'between_available_lessons' => 1,
      'is_one_lesson_available' => 2,
      'no_lessons_available' => 3
   ],

   'months' => [
      '01' => 'январь',
      '02' => 'февраль',
      '03' => 'март',
      '04' => 'апрель',
      '05' => 'май',
      '06' => 'июнь',
      '07' => 'июль',
      '08' => 'август',
      '09' => 'сентябрь',
      '10' => 'октябрь',
      '11' => 'ноябрь',
      '12' => 'декабрь',
   ],

   'replacement_request_statuses' => [
      1 => 'В подготовке',
      2 => 'В ожидании согласия',
      3 => 'В ожидании разрешения',
      4 => 'Разрешено',
      5 => 'В реализации',
      6 => 'Завершено',
      7 => 'Отменено',
      8 => 'Отклонено',
   ],

   'replacement_request_status_ids' => [
      'in drafting' => 1,
      'in_consent_waiting' => 2,
      'in_permission_waiting' => 3,
      'permitted' => 4,
      'in_realization' => 5,
      'completed' => 6,
      'cancelled' => 7,
      'declined' => 8,
   ]
];