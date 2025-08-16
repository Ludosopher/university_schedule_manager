<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudyOrientation extends Model
{
    public function study_program()
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function groups()
    {
        return $this->hasMany('App\Group');
    }
}
