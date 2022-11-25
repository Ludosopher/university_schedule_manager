<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LessonRoom extends Model
{
    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }
}
