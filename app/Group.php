<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Group extends Model
{
    use Sortable;
    public $sortable = ['name', 'faculty_id', 'study_program_id', 'study_orientation_id', 'study_degree_id', 'study_form_id', 'course', 'size'];
    
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function study_program()
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function study_orientation()
    {
        return $this->belongsTo(StudyOrientation::class);
    }

    public function study_degree()
    {
        return $this->belongsTo(StudyDegree::class);
    }

    public function study_form()
    {
        return $this->belongsTo(StudyForm::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public $additional_attributes = ['name'];
    
    public function getNameAttribute()
    {
        $study_degree = $this->study_degree->abbreviation;
        $study_form = $this->study_form->abbreviation;
        $faculty = $this->faculty->abbreviation;
        $cours = $this->course->number;
        $study_program = $this->study_program->abbreviation;
        $study_orientation = mb_strtolower($this->study_orientation->abbreviation);
        $additional_id = isset($this->additional_id) ? "/$this->additional_id" : "";
        
        return "{$study_degree}.{$study_form}.{$faculty}-{$cours}-{$study_program}({$study_orientation}){$additional_id}";
    }

}
