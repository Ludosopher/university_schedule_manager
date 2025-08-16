<?php
use Illuminate\Database\Seeder;
use App\User;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('settings')->insert([
            [
                'id' => 1,
                'name' => 'red_week_is_odd',
                'value' => '1',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 2,
                'name' => 'full_time_week_days_limit',
                'value' => '5',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 3,
                'name' => 'distance_week_days_limit',
                'value' => '6',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 4,
                'name' => 'full_time_class_periods_limit',
                'value' => '5',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 5,
                'name' => 'distance_class_periods_limit',
                'value' => '5',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 6,
                'name' => 'default_rows_per_page',
                'value' => '15',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
            [
                'id' => 7,
                'name' => 'min_replacement_period',
                'value' => '18',
                'created_at' => '2022-12-26 00:00:00',
                'updated_at' => '2022-12-26 00:00:00',
            ],
        ]);
    }
}
