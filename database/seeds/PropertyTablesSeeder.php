<?php

use Illuminate\Database\Seeder;

class PropertyTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('academic_degrees')->insert([
            [
                'id' => 1,
                'name' => 'candidate_of_economic_sciences',
                'short_name' => 'c_econ_sciences',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'doctor_of_economic_sciences',
                'short_name' => 'd_econ_sciences',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'candidate_of_philosophical_sciences',
                'short_name' => 'c_philos_sciences',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 4,
                'name' => 'doctor_of_philosophical_sciences',
                'short_name' => 'd_philos_sciences',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 5,
                'name' => 'candidate_of_technical_sciences',
                'short_name' => 'c_tech_sciences',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 6,
                'name' => 'doctor_of_technical_sciences',
                'short_name' => 'd_tech_science',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 7,
                'name' => 'candidate_of_sociological_sciences',
                'short_name' => 'c_soc_sciences',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 8,
                'name' => 'doctor_of_sociological_sciences',
                'short_name' => 'd_soc_sciences',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 9,
                'name' => 'candidate_of_agricultural_sciences',
                'short_name' => 'c_agri_sciences',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 10,
                'name' => 'doctor_of_agricultural_sciences',
                'short_name' => 'd_agri_sciences',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
        ]);

        \DB::table('professional_levels')->insert([
            [
                'id' => 1,
                'name' => 'assistant',
                'short_name' => 'ass',
                'level' => 1,
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'senior_teacher',
                'short_name' => 'sen_teach',
                'level' => 2,
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'docent',
                'short_name' => 'doc',
                'level' => 3,
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 4,
                'name' => 'professor',
                'short_name' => 'prof',
                'level' => 4,
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
        ]);

        \DB::table('positions')->insert([
            [
                'id' => 1,
                'name' => 'lecturer',
                'level' => 1,
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'head_of_department',
                'level' => 2,
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'dean_of_faculty',
                'level' => 3,
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 4,
                'name' => 'associate_director',
                'level' => 4,
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 5,
                'name' => 'director',
                'level' => 5,
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
        ]);

        \DB::table('lesson_types')->insert([
            [
                'id' => 1,
                'name' => 'lecture',
                'short_notation' => 'l',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'practical',
                'short_notation' => 'pl',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'laboratory',
                'short_notation' => 'll',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 4,
                'name' => 'exam',
                'short_notation' => 'ex',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 5,
                'name' => 'test',
                'short_notation' => 't',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ]
        ]);

        \DB::table('week_days')->insert([
            [
                'id' => 1,
                'name' => 'monday',
                'short_notation' => 'mo',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'tuesday',
                'short_notation' => 'tu',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'wednesday',
                'short_notation' => 'we',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 4,
                'name' => 'thursday',
                'short_notation' => 'th',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 5,
                'name' => 'friday',
                'short_notation' => 'fr',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 6,
                'name' => 'saturday',
                'short_notation' => 'sa',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 7,
                'name' => 'sunday',
                'short_notation' => 'su',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
        ]);

        \DB::table('weekly_periods')->insert([
            [
                'id' => 1,
                'name' => 'every_week',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'red_week',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'blue_week',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ]
        ]);

        \DB::table('class_periods')->insert([
            [
                'id' => 1,
                'name' => 'first',
                'start' => '08:00:00',
                'end' => '09:35:00',
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'second',
                'start' => '09:45:00',
                'end' => '11:20:00',
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'third',
                'start' => '12:00:00',
                'end' => '13:35:00',
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 4,
                'name' => 'fourth',
                'start' => '13:45:00',
                'end' => '15:20:00',
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 5,
                'name' => 'fifth',
                'start' => '15:30:00',
                'end' => '17:05:00',
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 6,
                'name' => 'sixth',
                'start' => '17:15:00',
                'end' => '18:50:00',
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
        ]);

        \DB::table('study_periods')->insert([
            [
                'id' => 1,
                'year' => '2024',
                'season' => 'spring',
                'start' => '2024-01-29 00:00:00',
                'micro_end' => '2024-03-29 00:00:00',
                'end' => '2024-05-31 00:00:00',
                'session_start' => '2024-06-03 00:00:00',
                'session_end' => '2024-01-26 00:00:00',
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 2,
                'year' => '2024',
                'season' => 'autumn',
                'start' => '2024-09-01 00:00:00',
                'micro_end' => '2024-11-03 00:00:00',
                'end' => '2024-12-29 00:00:00',
                'session_start' => '2025-01-11 00:00:00',
                'session_end' => '2025-01-26 00:00:00',
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
        ]);

        \DB::table('study_degrees')->insert([
            [
                'id' => 1,
                'name' => 'bachelor',
                'abbreviation' => 'b',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'magistracy',
                'abbreviation' => 'm',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
        ]);

        \DB::table('study_forms')->insert([
            [
                'id' => 1,
                'name' => 'full_time',
                'abbreviation' => 'ft',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'part_time',
                'abbreviation' => 'pt',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'full_part_time',
                'abbreviation' => 'fpt',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ]
        ]);

        \DB::table('courses')->insert([
            [
                'id' => 1,
                'number' => 1,
                'name' => 'first',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 2,
                'number' => 2,
                'name' => 'second',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 3,
                'number' => 3,
                'name' => 'third',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 4,
                'number' => 4,
                'name' => 'fourth',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 5,
                'number' => 5,
                'name' => 'fifth',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
        ]);

        \DB::table('faculties')->insert([
            [
                'id' => 1,
                'name' => 'engineering_and_reclamation',
                'abbreviation' => 'er',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'business_and_social_technologies',
                'abbreviation' => 'bst',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'land_management',
                'abbreviation' => 'lm',
                'created_at' => '2022-05-17 00:00:00',
                'updated_at' => '2022-05-17 00:00:00',
            ],
        ]);

        \DB::table('study_programs')->insert([
            [
                'id' => 1,
                'code' => '08.03.01',
                'name' => 'building',
                'abbreviation' => 'bu',
                'faculty_id' => 1,
                'study_degree_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 2,
                'code' => '20.03.02',
                'name' => 'environmental_management_and_water_use',
                'abbreviation' => 'emwu',
                'faculty_id' => 1,
                'study_degree_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 3,
                'code' => '35.03.11',
                'name' => 'hydromelioration',
                'abbreviation' => 'hy',
                'faculty_id' => 1,
                'study_degree_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 4,
                'code' => '38.03.01',
                'name' => 'economy',
                'abbreviation' => 'emy',
                'faculty_id' => 2,
                'study_degree_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 5,
                'code' => '38.03.02',
                'name' => 'management',
                'abbreviation' => 'men',
                'faculty_id' => 2,
                'study_degree_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 6,
                'code' => '38.03.05',
                'name' => 'business_informatics',
                'abbreviation' => 'bi',
                'faculty_id' => 2,
                'study_degree_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 7,
                'code' => '21.03.02',
                'name' => 'land_management_and_cadastres',
                'abbreviation' => 'lmc',
                'faculty_id' => 3,
                'study_degree_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ]
        ]);

        \DB::table('study_orientations')->insert([
            [
                'id' => 1,
                'name' => 'hydrauli_ engineering_construction',
                'abbreviation' => 'hec',
                'study_program_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'engineering_systems_of_agricultural_water_supply_irrigation_and_drainage',
                'abbreviation' => 'esawsid',
                'study_program_id' => 2,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'hydromelioration',
                'abbreviation' => 'hy',
                'study_program_id' => 3,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 4,
                'name' => 'economics_of_enterprises_and_organizations',
                'abbreviation' => 'eeo',
                'study_program_id' => 4,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 5,
                'name' => 'production_management',
                'abbreviation' => 'pm',
                'study_program_id' => 5,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 6,
                'name' => 'electronic_business',
                'abbreviation' => 'eb',
                'study_program_id' => 6,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 7,
                'name' => 'land_management',
                'abbreviation' => 'lm',
                'study_program_id' => 7,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 8,
                'name' => 'real_estate_cadastre',
                'abbreviation' => 'rec',
                'study_program_id' => 7,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ]
        ]);

        \DB::table('departments')->insert([
            [
                'id' => 1,
                'name' => 'water_supply_and_use_of_water_resources',
                'faculty_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'hydraulic_engineering_construction',
                'faculty_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'land_ reclamation',
                'faculty_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 4,
                'name' => 'economy',
                'faculty_id' => 2,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 5,
                'name' => 'management_and_computer_science',
                'faculty_id' => 2,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 6,
                'name' => 'history_philosophy_and_social_technologies',
                'faculty_id' => 2,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 7,
                'name' => 'land_use_and_land_management',
                'faculty_id' => 3,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 8,
                'name' => 'cadastre_and_land_monitoring',
                'faculty_id' => 3,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 9,
                'name' => 'soil_science_irrigated_agriculture_and_geodesy',
                'faculty_id' => 3,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
        ]);

        \DB::table('replacement_request_statuses')->insert([
            [
                'id' => 1,
                'name' => 'in_drafting',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'in_consent_waiting',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'in_permission_waiting',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 4,
                'name' => 'permitted',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 5,
                'name' => 'in_realization',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 6,
                'name' => 'completed',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 7,
                'name' => 'cancelled',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 8,
                'name' => 'declined',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 9,
                'name' => 'not_permitted',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
        ]);
    }
}
