<?php

namespace App;

use App\Helpers\LessonHelpers;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Lesson extends Model
{
    use Sortable;
    public $sortable = ['name', 'lesson_type_id', 'week_day_id','weekly_period_id', 'class_period_id', 'group_id', 'teacher_id', 'profession_level_name'];

    public function professionLevelNameSortable($query, $direction)
    {
        return $query->join('teachers', 'lessons.teacher_id', '=', 'teachers.id')
                    ->orderBy('last_name', $direction)
                    ->select('lessons.*');
    }

    public function class_period()
    {
        return $this->belongsTo(ClassPeriod::class);
    }

    public function lesson_room()
    {
        return $this->belongsTo(LessonRoom::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function week_day()
    {
        return $this->belongsTo(WeekDay::class);
    }

    public function weekly_period()
    {
        return $this->belongsTo(WeeklyPeriod::class);
    }

    public function lesson_type()
    {
        return $this->belongsTo(LessonType::class);
    }

    public function replaceable_variants()
    {
        return $this->hasMany('App\ReplacementRequests', 'replaceable_lesson_id');
    }

    public function replacing_variants()
    {
        return $this->hasMany('App\ReplacementRequests', 'replacing_lesson_id');
    }

    public $additional_attributes = ['groups_name'];

    public function getGroupsNameAttribute()
    {
        $study_degree = $this->groups[0]->study_degree->abbreviation;
        $study_form = $this->groups[0]->study_form->abbreviation;
        $faculty = $this->groups[0]->faculty->abbreviation;
        $cours = $this->groups[0]->course->number;

        $groups_name = "{$study_degree}.{$study_form}.{$faculty}-{$cours}-";

        if (count($this->groups) > 1) {
            $variative_part_arr = [];
            foreach ($this->groups as $group) {
                $study_program = $group->study_program->abbreviation;
                $variative_part_arr[] = $study_program;
            }
            return $groups_name.'['.implode('; ', $variative_part_arr).']';
        }

        $study_program = $this->groups[0]->study_program->abbreviation;
        $study_orientation = mb_strtolower($this->groups[0]->study_orientation->abbreviation);
        $additional_id = isset($this->groups[0]->additional_id) ? "/$this->additional_id" : "";

        return "{$groups_name}{$study_program}({$study_orientation}){$additional_id}";
    }

    public static function getProperties() {

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

    public static function getReplacementProperties() {

        return [
            'lesson_types' => LessonType::select('id', 'name')->get(),
            'week_days' => WeekDay::select('id', 'name')->get(),
            'weekly_periods' => WeeklyPeriod::select('id', 'name')->get(),
            'class_periods' => ClassPeriod::get(),
            'faculties' => Faculty::select('id', 'name')->get(),
            'departments' => Department::select('id', 'name')->get(),
            'professional_levels' => ProfessionalLevel::select('id', 'name')->get(),
            'positions' => Position::select('id', 'name')->get(),
            'lesson_rooms' => LessonRoom::select('id', 'number AS name')->get(),
            'schedule_positions' => collect(config('enum.schedule_positions'))
        ];
    }
}
