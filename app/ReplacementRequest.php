<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReplacementRequest extends Model
{
    public function status()
    {
        return $this->belongsTo(ReplacementRequestStatus::class);
    }

    public function replaceable_lesson()
    {
        return $this->belongsTo('App\Lesson', 'replaceable_lesson_id');
    }

    public function replacing_lesson()
    {
        return $this->belongsTo('App\Lesson', 'replacing_lesson_id');
    }

    public function initiator()
    {
        return $this->belongsTo('App\Lesson', 'initiator_id');
    }

    public function messages()
    {
        return $this->hasMany('App\Message');
    }
}
