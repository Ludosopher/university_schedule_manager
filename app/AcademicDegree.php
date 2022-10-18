<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AcademicDegree extends Model
{
    public function teachers()
    {
        return $this->hasMany('App\Teacher');
    }
}
