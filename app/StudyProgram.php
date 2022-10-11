<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudyProgram extends Model
{
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function study_degree()
    {
        return $this->belongsTo(StudyDegree::class);
    }

    public function study_orientations()
    {
        return $this->hasMany('App\StudyOrientation');
    }

    public function groups()
    {
        return $this->hasMany('App\Group');
    }
}
