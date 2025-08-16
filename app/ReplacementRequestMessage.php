<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReplacementRequestMessage extends Model
{
    public function replacement_request()
    {
        return $this->belongsTo(ReplacementRequest::class);
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    
    public $additional_attributes = ['excerpt'];
    
    public function getExcerptAttribute()
    {
        return implode(' ', array_slice(explode(' ', $this->body), 0, 5)).' ...';
    }
}
