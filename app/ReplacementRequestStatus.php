<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReplacementRequestStatus extends Model
{
    public function requests()
    {
        return $this->hasMany('App\ReplacementRequest');
    }
}
