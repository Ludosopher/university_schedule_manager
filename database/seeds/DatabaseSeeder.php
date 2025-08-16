<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *  php artisan migrate:fresh --seed  // deletes all the tables (and its contents) and starts a fresh migration with seeding
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(PropertyTablesSeeder::class);
        $this->call(TeachersTableSeeder::class);
        $this->call(GroupsTableSeeder::class);
        $this->call(LessonRoomsTableSeeder::class);
        $this->call(LessonsTableSeeder::class);
        $this->call(GroupsLessonsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);

         
        \DB::table('external_datasets')->insert([
            [
                'id' => 1,
                'name' => 'xmlcalendar',
                'url_pattern' => 'http://xmlcalendar.ru/data/ru/{Y}/calendar.xml',
                'body' => '["01.01.2023","02.01.2023","03.01.2023","04.01.2023","05.01.2023","06.01.2023","07.01.2023","08.01.2023","23.02.2023","24.02.2023","08.03.2023","01.05.2023","08.05.2023","09.05.2023","12.06.2023","04.11.2023","06.11.2023"]',
                'update_date' => '2022-12-26',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
        ]);

    }
}
