<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    public function teachers()
    {
        return $this->hasMany('App\Teacher');
    }

    public function departments()
    {
        return $this->hasMany('App\Department');
    }

    public function studyPrograms()
    {
        return $this->hasMany('App\Studyprogram');
    }

    public function groups()
    {
        return $this->hasMany('App\Group');
    }
}
