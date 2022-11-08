<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function groups()
    {
        return $this->hasMany('App\Group');
    }
}
