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

   'genders' => ['male', 'female', 'not_specified'],

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

   'week_days' => [
      1 => 'monday',
      2 => 'tuesday',
      3 => 'wednesday',
      4 => 'thursday',
      5 => 'friday',
      6 => 'saturday'
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
      'name' => 'between_two_available_pairs'],
      ['id' => 2,
      'name' => 'next_to_one_of_available_pairs'],
      ['id' => 3,
      'name' => 'there_are_no_pairs_available_nearby'],
   ],

   'schedule_position_ids' => [
      'between_available_lessons' => 1,
      'is_one_lesson_available' => 2,
      'no_lessons_available' => 3
   ],

   'months' => [
      1 => 'january',
      2 => 'february',
      3 => 'march',
      4 => 'april',
      5 => 'may',
      6 => 'june',
      7 => 'july',
      8 => 'august',
      9 => 'september',
      10 => 'october',
      11 => 'november',
      12 => 'december',
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
      9 => 'Не разрешено',
   ],

   'replacement_request_status_ids' => [
      'in_drafting' => 1,
      'in_consent_waiting' => 2,
      'in_permission_waiting' => 3,
      'permitted' => 4,
      'in_realization' => 5,
      'completed' => 6,
      'cancelled' => 7,
      'declined' => 8,
      'not_permitted' => 9,
   ],

   'replacement_request_status_groups' => [
      'in_management' => [1, 2, 3, 4, 5],
      'active' => [2, 3, 4, 5],
   ],

   'replacement_request_status_colors' => [
      1 => 'rgb(255, 255, 255)',
      2 => 'rgb(255, 255, 215)',
      3 => 'rgb(215, 255, 255)',
      4 => 'rgb(150, 255, 150)',
      5 => 'rgb(190, 190, 255)',
      6 => 'rgb(230, 230, 230)',
      7 => 'rgb(255, 180, 180)',
      8 => 'rgb(255, 220, 255)',
      9 => 'rgb(255, 200, 200)',
   ],

   'external_dataset_ids' => [
      'xmlcalendar' => 1,
   ],

   'languages' => [
      'english' => 'en',
      'russian' => 'ru',
   ],

   'study_seasons' => [
      'studies' => 1,
      'session' => 2,
      'vacation' => 3,
   ],

   'study_seasons_ids' => [
      1 => 'studies',
      2 => 'session',
      3 => 'vacation',
   ],

   'study_season_name_parts' => [
      'studies' => '',
      'session' => 'session_',
   ],
];
