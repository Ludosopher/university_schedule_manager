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
        return $this->is_moderator ? 'Да' : 'Нет';
    }

    public function getAdminAttribute()
    {
        return $this->is_admin ? 'Да' : 'Нет';
    }

    public static function getProperties() {

        $groups = Group::orderBy('study_form_id')
                        ->orderBy('study_degree_id')
                        ->orderBy('faculty_id')
                        ->orderBy('course_id')
                        ->get();

        $teachers = Teacher::orderBy('last_name')->get();
        foreach ($teachers as &$teacher) {
            $teacher->name = $teacher->profession_level_name;
        }

        return [
            'groups' => $groups,
            'teachers' => $teachers
        ];
    }
}
