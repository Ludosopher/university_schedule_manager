<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ReplacementRequest extends Model
{
    use Sortable;
    public $sortable = ['is_regular', 'replaceable_date', 'replaceable_lesson_id', 'replacing_date', 'replacing_lesson_id', 'status_id', 'initiator_id'];
    
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
        return $this->belongsTo('App\User', 'initiator_id');
    }

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public $additional_attributes = ['regular'];

    public function getRegularAttribute()
    {
        return $this->is_regular ? 'Да' : 'Нет';
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
            'statuses' => ReplacementRequestStatus::select('id', 'name')->get(),
            'groups' => $groups,
            'teachers' => $teachers,
            'users' => User::select('id', 'name')->get(),
        ];
    }
}
