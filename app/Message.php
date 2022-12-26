<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function replacement_request()
    {
        return $this->belongsTo(ReplacementRequest::class);
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }
}
