<?php

namespace App\Helpers;

use App\AcademicDegree;
use App\ClassPeriod;
use App\Course;
use App\Department;
use App\Faculty;
use App\Group;
use App\Lesson;
use App\LessonRoom;
use App\LessonType;
use App\Mail\MailReplacementRequest;
use App\Position;
use App\ProfessionalLevel;
use App\ReplacementRequest;
use App\StudyDegree;
use App\StudyForm;
use App\StudyOrientation;
use App\StudyProgram;
use App\Teacher;
use App\WeekDay;
use App\WeeklyPeriod;
use Illuminate\Support\Facades\Mail;

class DictionaryHelpers
{
    public static function getTeacherProperties() {
        return [
            'faculties' => Faculty::select('id', 'name')->get(),
            'departments' => Department::select('id', 'name')->get(),
            'professional_levels' => ProfessionalLevel::select('id', 'name')->get(),
            'positions' => Position::select('id', 'name')->get(),
            'academic_degrees' => AcademicDegree::select('id', 'name')->get(),
            'genders' => config('enum.genders')
        ];
    }

    public static function getGroupProperties() {
        return [
            'groups' => Group::orderBy('study_degree_id')->orderBy('study_form_id')->orderBy('faculty_id')->orderBy('course_id')->get(),
            'faculties' => Faculty::select('id', 'name')->get(),
            'study_programs' => StudyProgram::select('id', 'name')->get(),
            'study_orientations' => StudyOrientation::select('id', 'name')->get(),
            'study_degrees' => StudyDegree::select('id', 'name')->get(),
            'study_forms' => StudyForm::select('id', 'name')->get(),
            'courses' => Course::select('id', 'name')->get(),
        ];
    }

    public static function getLessonProperties() {

        $groups = Group::orderBy('study_form_id')
                        ->orderBy('study_degree_id')
                        ->orderBy('faculty_id')
                        ->orderBy('course_id')
                        ->get();

        $teachers = Teacher::orderBy('last_name')->get();
        foreach ($teachers as &$teacher) {
            $teacher->name = $teacher->profession_level_name;
        }

        return [
            'lesson_types' => LessonType::select('id', 'name')->get(),
            'week_days' => WeekDay::select('id', 'name')->get(),
            'weekly_periods' => WeeklyPeriod::select('id', 'name')->get(),
            'class_periods' => ClassPeriod::select('id', 'name')->get(),
            'lesson_rooms' => LessonRoom::select('id', 'number AS name')->get(),
            'groups' => $groups,
            'teachers' => $teachers
        ];
    }

    public static function getUserProperties() {

        $groups = Group::orderBy('study_form_id')
                        ->orderBy('study_degree_id')
                        ->orderBy('faculty_id')
                        ->orderBy('course_id')
                        ->get();

        $teachers = Teacher::orderBy('last_name')->get();
        foreach ($teachers as &$teacher) {
            $teacher->name = $teacher->profession_level_name;
        }

        return [
            'groups' => $groups,
            'teachers' => $teachers
        ];
    }

    public static function getReplacementProperties() {

        $class_periods = ClassPeriod::get();
        
        return [
            'lesson_types' => LessonType::select('id', 'name')->get(),
            'week_days' => WeekDay::select('id', 'name')->get(),
            'weekly_periods' => WeeklyPeriod::select('id', 'name')->get(),
            'class_periods' => $class_periods,
            'normalize_class_periods' => array_combine(range(1, count($class_periods)), array_values($class_periods->toArray())),
            'faculties' => Faculty::select('id', 'name')->get(),
            'departments' => Department::select('id', 'name')->get(),
            'professional_levels' => ProfessionalLevel::select('id', 'name')->get(),
            'positions' => Position::select('id', 'name')->get(),
            'lesson_rooms' => LessonRoom::select('id', 'number AS name')->get(),
            'schedule_positions' => collect(config('enum.schedule_positions'))
        ];
    }
}
