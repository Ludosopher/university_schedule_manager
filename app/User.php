<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;

class User extends Authenticatable
{
    use Notifiable;
    
    use Sortable;
    public $sortable = ['name', 'email', 'is_moderator','is_admin'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }

    public function replacement_requests()
    {
        return $this->hasMany('App\ReplacementRequest', 'initiator_id');
    }

    public function replacement_request_messages()
    {
        return $this->hasMany('App\ReplacementRequestMessage', 'author_id');
    }

    public $additional_attributes = ['group_names', 'teacher_names', 'moderator', 'admin'];

    public function getTeacherNamesAttribute()
    {
        $teachers = [];
        foreach ($this->teachers as $teacher) {
            $teachers[] = $teacher->profession_level_name;
        }
        return implode('; ', $teachers);
    }

    public function getGroupNamesAttribute()
    {
        $groups = [];
        foreach ($this->groups as $group) {
            $groups[] = $group->name;
        }
        return implode('; ', $groups);
    }

    public function getModeratorAttribute()
    {
        return $this->is_moderator ? __('content.yes') : __('content.no');
    }

    public function getAdminAttribute()
    {
        return $this->is_admin ? __('content.yes') : __('content.no');
    }

}
