<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public function addressee()
    {
        return $this->belongsTo('App\User', 'addressee_id');
    }
}
