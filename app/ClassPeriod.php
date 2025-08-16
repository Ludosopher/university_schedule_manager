<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassPeriod extends Model
{
    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }
}
