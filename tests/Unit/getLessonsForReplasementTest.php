<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Helpers\LessonHelpers;
//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class getLessonsForReplasementTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    use RefreshDatabase;
    
     public function setUp(): void
    {
        parent::setUp();
        
        //$this->artisan('db:seed');
        $this->seed();
    }
    
    public function testGetLessonsForReplasement()
    {
        $data = [
            "lesson_id" => 145,
            "teacher_id" => 23,
            "class_period_id" => 3,
            "weekly_period_id" => 1,
            "week_day_id" => 5,
            "date" => null,
        ];
        $week_number = null;
        $week_dates = null;
        
        $correct_result = [
            0 => [
                "lesson_id" => 47,
                "subject" => "geography",
                "week_day_id" => [
                "id" => 3,
                "name" => "wednesday",
                ],
                "date" => null,
                "weekly_period_id" => [
                "id" => 1,
                "name" => "every_week",
                ],
                "class_period_id" => [
                "id" => 4,
                "name" => "fourth",
                ],
                "lesson_room_id" => [
                "id" => 6,
                "name" => '1006',
                ],
                "department_id" => [
                "id" => 6,
                "name" => "history_philosophy_and_social_technologies",
                ],
                "position_id" => [
                "id" => 1,
                "name" => "lecturer",
                ],
                "lesson_type" => 'l',
                "lesson_room" => "1006",
                "groups_name" => "Б.О.БиСТ-4-М(пм)",
                "teacher_id" => 12,
                "profession_level_name" => "доц. Алтынцева З.С.",
                "phone" => "+71678498124",
                "age" => 41,
                "schedule_position_id" => [
                    "id" => 2,
                    "name" => "next_to_one_of_available_pairs",
                ],
                "replacing_date_time" => null,
                "replacing_hours_diff" => null,
            ],
            1 => [
                "lesson_id" => 135,
                "subject" => "management",
                "week_day_id" => [
                    "id" => 1,
                    "name" => "monday",
                ],
                "date" => null,
                "weekly_period_id" => [
                    "id" => 1,
                    "name" => "every_week",
                ],
                "class_period_id" => [
                    "id" => 4,
                    "name" => "fourth",
                ],
                "lesson_room_id" => [
                    "id" => 15,
                    "name" => "1015",
                ],
                "department_id" => [
                    "id" => 6,
                    "name" => "history_philosophy_and_social_technologies",
                ],
                "position_id" => [
                    "id" => 1,
                    "name" => "lecturer",
                ],
                "lesson_type" => 'll',
                "lesson_room" => "1015",
                "groups_name" => "Б.О.БиСТ-4-М(пм)",
                "teacher_id" => 22,
                "profession_level_name" => "доц. Вырицкий К.Н.",
                "phone" => "+71678487867",
                "age" => 45,
                "schedule_position_id" => [
                    "id" => 3,
                    "name" => "there_are_no_pairs_available_nearby",
                ],
                "replacing_date_time" => null,
                "replacing_hours_diff" => null,
            ]
        ];

        //$replacementLessons = (new TeacherScheduleElement())->getLessonsForReplacement($data, $week_number, $week_dates);

        //$this->assertEquals($replacementLessons, $correct_result);
    }
}
