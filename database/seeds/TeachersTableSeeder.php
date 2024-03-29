<?php
use Illuminate\Database\Seeder;
use App\User;

class TeachersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('teachers')->insert([
            [
                'id' => 1,
                'last_name' => 'Петров',
                'first_name' => 'Семён',
                'patronymic' => 'Анатольевич',
                'gender' => 'male',
                'birth_year' => 1970,
                'phone' => '+71232343445',
                'email' => 'dfdff@dfdf.df',
                'faculty_id' => 1,
                'department_id' => 1,
                'professional_level_id' => 1,
                'position_id' => 1,
                'academic_degree_id' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 2,
                'last_name' => 'Ахметов',
                'first_name' => 'Нурали',
                'patronymic' => 'Болатович',
                'gender' => 'male',
                'birth_year' => 1980,
                'phone' => '+71678943445',
                'email' => 'dfiof@dfio.df',
                'faculty_id' => 1,
                'department_id' => 3,
                'professional_level_id' => 2,
                'position_id' => 3,
                'academic_degree_id' => 2,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 3,
                'last_name' => 'Семёнова',
                'first_name' => 'Галина',
                'patronymic' => 'Станиславовна',
                'gender' => 'female',
                'birth_year' => 1984,
                'phone' => '+71678498912',
                'email' => 'rtuifg@dfio.jk',
                'faculty_id' => 2,
                'department_id' => 6,
                'professional_level_id' => 3,
                'position_id' => 4,
                'academic_degree_id' => 3,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 4,
                'last_name' => 'Потёмкин',
                'first_name' => 'Сергей',
                'patronymic' => 'Фёдорович',
                'gender' => 'male',
                'birth_year' => 1977,
                'phone' => '+71678487232',
                'email' => 'rghghg@dfio.kl',
                'faculty_id' => 3,
                'department_id' => 8,
                'professional_level_id' => 4,
                'position_id' => 2,
                'academic_degree_id' => 4,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 5,
                'last_name' => 'Касатонов',
                'first_name' => 'Алексей',
                'patronymic' => 'Астахович',
                'gender' => 'male',
                'birth_year' => 1973,
                'phone' => '+71904543445',
                'email' => 'djkqw@dfdf.df',
                'faculty_id' => 1,
                'department_id' => 2,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 6,
                'last_name' => 'Алханов',
                'first_name' => 'Казбек',
                'patronymic' => 'Лиманович',
                'gender' => 'male',
                'birth_year' => 1994,
                'phone' => '+71600993445',
                'email' => 'klrdfdf@dfio.df',
                'faculty_id' => 2,
                'department_id' => 4,
                'professional_level_id' => 4,
                'position_id' => 1,
                'academic_degree_id' => 6,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 7,
                'last_name' => 'Покровская',
                'first_name' => 'Профья',
                'patronymic' => 'Николаевна',
                'gender' => 'female',
                'birth_year' => 1998,
                'phone' => '+71678411112',
                'email' => 'rtghjfg@dfio.jk',
                'faculty_id' => 2,
                'department_id' => 5,
                'professional_level_id' => 4,
                'position_id' => 1,
                'academic_degree_id' => 7,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 8,
                'last_name' => 'Быстрицкий',
                'first_name' => 'Констатнтин',
                'patronymic' => 'Никифорович',
                'gender' => 'male',
                'birth_year' => 1977,
                'phone' => '+71678481212',
                'email' => 'rassd@dfio.kl',
                'faculty_id' => 3,
                'department_id' => 9,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 9,
                'last_name' => 'Сафьянов',
                'first_name' => 'Дмитрий',
                'patronymic' => 'Анатольевич',
                'gender' => 'male',
                'birth_year' => 1975,
                'phone' => '+71678999212',
                'email' => 'raaje@dfio.kv',
                'faculty_id' => 3,
                'department_id' => 7,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => 9,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],

            [
                'id' => 10,
                'last_name' => 'Антонов',
                'first_name' => 'Семён',
                'patronymic' => 'Анатольевич',
                'gender' => 'male',
                'birth_year' => 1973,
                'phone' => '+71232343111',
                'email' => 'dfрпа@dfdf.df',
                'faculty_id' => 1,
                'department_id' => 1,
                'professional_level_id' => 1,
                'position_id' => 1,
                'academic_degree_id' => 10,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 11,
                'last_name' => 'Лапинский',
                'first_name' => 'Эдгар',
                'patronymic' => 'Юсупович',
                'gender' => 'male',
                'birth_year' => 1982,
                'phone' => '+71655543445',
                'email' => 'fgiuf@dfio.df',
                'faculty_id' => 1,
                'department_id' => 3,
                'professional_level_id' => 2,
                'position_id' => 1,
                'academic_degree_id' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 12,
                'last_name' => 'Алтынцева',
                'first_name' => 'Зинаида',
                'patronymic' => 'Станиславовна',
                'gender' => 'female',
                'birth_year' => 1981,
                'phone' => '+71678498124',
                'email' => 'kluifg@dfio.ii',
                'faculty_id' => 2,
                'department_id' => 6,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => 2,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 13,
                'last_name' => 'Метла',
                'first_name' => 'Борис',
                'patronymic' => 'Фёдорович',
                'gender' => 'male',
                'birth_year' => 1978,
                'phone' => '+71678487111',
                'email' => 'dsdghg@dfio.kl',
                'faculty_id' => 3,
                'department_id' => 8,
                'professional_level_id' => 4,
                'position_id' => 1,
                'academic_degree_id' => 3,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 14,
                'last_name' => 'Корецкий',
                'first_name' => 'Виктор',
                'patronymic' => 'Ярославович',
                'gender' => 'male',
                'birth_year' => 1971,
                'phone' => '+71904543987',
                'email' => 'djasd@dfdf.df',
                'faculty_id' => 1,
                'department_id' => 2,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => 4,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 15,
                'last_name' => 'Анопко',
                'first_name' => 'Борис',
                'patronymic' => 'Абрамович',
                'gender' => 'male',
                'birth_year' => 1998,
                'phone' => '+78770993445',
                'email' => 'jhtyfdf@dfio.df',
                'faculty_id' => 2,
                'department_id' => 4,
                'professional_level_id' => 4,
                'position_id' => 1,
                'academic_degree_id' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 16,
                'last_name' => 'Задонская',
                'first_name' => 'Клава',
                'patronymic' => 'Антоновна',
                'gender' => 'female',
                'birth_year' => 1998,
                'phone' => '+71678411112',
                'email' => 'rlkjjfg@dfio.ds',
                'faculty_id' => 2,
                'department_id' => 5,
                'professional_level_id' => 4,
                'position_id' => 1,
                'academic_degree_id' => 6,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 17,
                'last_name' => 'Манаев',
                'first_name' => 'Марат',
                'patronymic' => 'Назирович',
                'gender' => 'male',
                'birth_year' => 1979,
                'phone' => '+71678481444',
                'email' => 'rauio@dfio.kl',
                'faculty_id' => 3,
                'department_id' => 9,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => 7,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 18,
                'last_name' => 'Морозко',
                'first_name' => 'Анастасия',
                'patronymic' => 'Александровна',
                'gender' => 'female',
                'birth_year' => 1974,
                'phone' => '+71678999212',
                'email' => 'ratje@dfss.kv',
                'faculty_id' => 3,
                'department_id' => 7,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            ///
            [
                'id' => 19,
                'last_name' => 'Масатонов',
                'first_name' => 'Алексей',
                'patronymic' => 'Петрович',
                'gender' => 'male',
                'birth_year' => 1973,
                'phone' => '+71555543445',
                'email' => 'djkqw@derf.df',
                'faculty_id' => 2,
                'department_id' => 4,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => 9,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 20,
                'last_name' => 'Малахов',
                'first_name' => 'Алексей',
                'patronymic' => 'Лиманович',
                'gender' => 'male',
                'birth_year' => 1994,
                'phone' => '+71600997775',
                'email' => 'klrdfdf@dfio.df',
                'faculty_id' => 2,
                'department_id' => 4,
                'professional_level_id' => 4,
                'position_id' => 1,
                'academic_degree_id' => 10,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 21,
                'last_name' => 'Доровская',
                'first_name' => 'Профья',
                'patronymic' => 'Николаевна',
                'gender' => 'female',
                'birth_year' => 1998,
                'phone' => '+71678411112',
                'email' => 'rfjyjfg@dfio.jk',
                'faculty_id' => 2,
                'department_id' => 5,
                'professional_level_id' => 4,
                'position_id' => 1,
                'academic_degree_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 22,
                'last_name' => 'Вырицкий',
                'first_name' => 'Констатнтин',
                'patronymic' => 'Никифорович',
                'gender' => 'male',
                'birth_year' => 1977,
                'phone' => '+71678487867',
                'email' => 'riuid@dfio.kl',
                'faculty_id' => 2,
                'department_id' => 6,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 23,
                'last_name' => 'Сафьянский',
                'first_name' => 'Дмитрий',
                'patronymic' => 'Анатольевич',
                'gender' => 'male',
                'birth_year' => 1975,
                'phone' => '+71678998212',
                'email' => 'raaew@dfio.kv',
                'faculty_id' => 2,
                'department_id' => 5,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => 3,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],

            [
                'id' => 24,
                'last_name' => 'Антошин',
                'first_name' => 'Семён',
                'patronymic' => 'Анатольевич',
                'gender' => 'male',
                'birth_year' => 1973,
                'phone' => '+71232334891',
                'email' => 'duyuа@dfdf.df',
                'faculty_id' => 2,
                'department_id' => 6,
                'professional_level_id' => 1,
                'position_id' => 1,
                'academic_degree_id' => 4,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 25,
                'last_name' => 'Лапин',
                'first_name' => 'Александр',
                'patronymic' => 'Юсупович',
                'gender' => 'male',
                'birth_year' => 1982,
                'phone' => '+71655543333',
                'email' => 'fooof@dfio.df',
                'faculty_id' => 2,
                'department_id' => 6,
                'professional_level_id' => 2,
                'position_id' => 1,
                'academic_degree_id' => 5,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 26,
                'last_name' => 'Алтынова',
                'first_name' => 'Карина',
                'patronymic' => 'Станиславовна',
                'gender' => 'female',
                'birth_year' => 1981,
                'phone' => '+71678498111',
                'email' => 'kqwefg@dfio.ii',
                'faculty_id' => 2,
                'department_id' => 6,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 27,
                'last_name' => 'Метладзе',
                'first_name' => 'Борис',
                'patronymic' => 'Фёдорович',
                'gender' => 'male',
                'birth_year' => 1978,
                'phone' => '+71678487888',
                'email' => 'duyohg@dfio.kl',
                'faculty_id' => 2,
                'department_id' => 4,
                'professional_level_id' => 4,
                'position_id' => 1,
                'academic_degree_id' => 7,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 28,
                'last_name' => 'Коремец',
                'first_name' => 'Виктор',
                'patronymic' => 'Ярославович',
                'gender' => 'male',
                'birth_year' => 1971,
                'phone' => '+71904543987',
                'email' => 'djaytyu@dfdf.df',
                'faculty_id' => 2,
                'department_id' => 5,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => 8,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 29,
                'last_name' => 'Ревенко',
                'first_name' => 'Борис',
                'patronymic' => 'Абрамович',
                'gender' => 'male',
                'birth_year' => 1998,
                'phone' => '+78770993120',
                'email' => 'jhtiidf@dfio.df',
                'faculty_id' => 2,
                'department_id' => 4,
                'professional_level_id' => 4,
                'position_id' => 1,
                'academic_degree_id' => 9,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 30,
                'last_name' => 'Задольская',
                'first_name' => 'Клавдия',
                'patronymic' => 'Антоновна',
                'gender' => 'female',
                'birth_year' => 1998,
                'phone' => '+71678411444',
                'email' => 'rjjjjfg@dfio.ds',
                'faculty_id' => 2,
                'department_id' => 5,
                'professional_level_id' => 4,
                'position_id' => 1,
                'academic_degree_id' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 31,
                'last_name' => 'Манашев',
                'first_name' => 'Марат',
                'patronymic' => 'Казирович',
                'gender' => 'male',
                'birth_year' => 1979,
                'phone' => '+71675551444',
                'email' => 'japio@dfio.kl',
                'faculty_id' => 2,
                'department_id' => 6,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => 1,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 32,
                'last_name' => 'Морозова',
                'first_name' => 'Катерина',
                'patronymic' => 'Александровна',
                'gender' => 'female',
                'birth_year' => 1974,
                'phone' => '+71699999212',
                'email' => 'ralle@dfss.kv',
                'faculty_id' => 2,
                'department_id' => 4,
                'professional_level_id' => 3,
                'position_id' => 1,
                'academic_degree_id' => 2,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
        ]);
    }
}
