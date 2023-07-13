<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GetTeachersTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    private $user;
    private $name = 'сафьянский';

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        //$this->artisan('db:seed');
        $this->seed();

        $this->user = User::where('email', env('ADMIN_EMAIL', ''))->first();
        $this->user->email_verified_at = \Carbon\Carbon::now();
    }

    

    public function testGetTeachers()
    {
        $response = $this->actingAs($this->user)
                         ->get('/teacher/get-all');


        $response->assertStatus(200);
    }

    public function testGetTeachersByNameFiltering()
    {
        $response = $this->actingAs($this->user)
                         ->post('/teacher/get-all', [
                            'full_name' => 'сафьянский',
                            'age_from' => null,
                            'age_to' => null,
                            'faculty_id' => null,
                            'department_id' => null,
                            'professional_level_id' => null,
                            'position_id' => null,
                            'academic_degree_id' => null,
                         ]);
        
        $response->assertStatus(200);
        
        $teacherCount = DB::select('SELECT COUNT(*) 
                                    FROM teachers 
                                    WHERE first_name LIKE ?
                                        OR last_name LIKE ?
                                        OR patronymic LIKE ?', ['%'.$this->name.'%', '%'.$this->name.'%', '%'.$this->name.'%']);
        
        $response->assertViewHas('data', function ($data) use ($teacherCount) {
            return count($data['instances']) == count($teacherCount);
        });
    }
}
