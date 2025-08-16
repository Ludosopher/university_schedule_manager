<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudyPeriod extends Model
{
    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }
}
