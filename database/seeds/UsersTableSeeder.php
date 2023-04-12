<?php
use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // if(config('admin.admin_name')) {
        //     User::firstOrCreate(
        //         [   
        //             'email' => config('admin.admin_email')], [
        //             'name' => config('admin.admin_name'),
        //             'password' => bcrypt(config('admin.admin_password')),
        //             'is_admin' => true,
        //         ]
        //     );
        // }

        \DB::table('users')->insert([
            [
                'id' => 1,
                'email' => 'schedule_manager@mail.ru',
                'name' => 'Viktor Alikin',
                'password' => bcrypt('schedule-admin'),
                'is_admin' => true,
            ],
        ]);
    }
}
