<?php
use Illuminate\Database\Seeder;
use App\User;

class LessonRoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('lesson_rooms')->insert([
            [
                'id' => 1,
                'number' => '1001',
                'capacity' => 70,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 2,
                'number' => '1002',
                'capacity' => 50,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 3,
                'number' => '1003',
                'capacity' => 15,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 4,
                'number' => '1004',
                'capacity' => 25,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 5,
                'number' => '1005',
                'capacity' => 15,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 6,
                'number' => '1006',
                'capacity' => 50,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 7,
                'number' => '1007',
                'capacity' => 15,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 8,
                'number' => '1008',
                'capacity' => 40,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 9,
                'number' => '1009',
                'capacity' => 30,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 10,
                'number' => '1010',
                'capacity' => 15,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 11,
                'number' => '1011',
                'capacity' => 70,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 12,
                'number' => '1012',
                'capacity' => 50,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 13,
                'number' => '1013',
                'capacity' => 15,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 14,
                'number' => '1014',
                'capacity' => 25,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 15,
                'number' => '1015',
                'capacity' => 15,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 16,
                'number' => '1016',
                'capacity' => 50,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 17,
                'number' => '1017',
                'capacity' => 15,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 18,
                'number' => '1018',
                'capacity' => 40,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 19,
                'number' => '1019',
                'capacity' => 30,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
            [
                'id' => 20,
                'number' => '1020',
                'capacity' => 15,
                'description' => null,
                'created_at' => '2022-08-22 00:00:00',
                'updated_at' => '2022-08-22 00:00:00',
            ],
        ]);
    }
}
