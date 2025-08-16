<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    public function teachers()
    {
        return $this->hasMany('App\Teacher');
    }
}
