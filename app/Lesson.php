<?php

namespace App;

use App\Helpers\LessonHelpers;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Lesson extends Model
{
    use Sortable;
    public $sortable = ['name', 'lesson_type_id', 'week_day_id', 'weekly_period_id', 'class_period_id', 'group_id', 'teacher_id', 'profession_level_name'];

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
        $study_degree = __('dictionary.'.$this->groups[0]->study_degree->abbreviation);
        $study_form = __('dictionary.'.$this->groups[0]->study_form->abbreviation);
        $faculty = __('dictionary.'.$this->groups[0]->faculty->abbreviation);
        $cours = $this->groups[0]->course->number;

        $groups_name = "{$study_degree}.{$study_form}.{$faculty}-{$cours}-";

        if (count($this->groups) > 1) {
            $variative_part_arr = [];
            foreach ($this->groups as $group) {
                $study_program = __('dictionary.'.$group->study_program->abbreviation);
                $variative_part_arr[] = $study_program;
            }
            return $groups_name.'['.implode('; ', $variative_part_arr).']';
        }

        $study_program = __('dictionary.'.$this->groups[0]->study_program->abbreviation);
        $study_orientation = mb_strtolower(__('dictionary.'.$this->groups[0]->study_orientation->abbreviation));
        $additional_id = isset($this->groups[0]->additional_id) ? "/".__('dictionary.'.$this->additional_id) : "";

        return "{$groups_name}{$study_program}({$study_orientation}){$additional_id}";
    }

}
