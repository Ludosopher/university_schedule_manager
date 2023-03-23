<?php

namespace App;

use App\Helpers\ReplacementRequestHelpers;
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
        return $this->hasMany('App\ReplacementRequestMessage');
    }

    public $additional_attributes = ['regular', 'name'];

    public function getRegularAttribute()
    {
        return $this->is_regular ? __('content.yes') : __('content.no');
    }

    public function getNameAttribute()
    {
        $data = [
            'replaceable_teacher' => $this->replaceable_lesson->teacher->profession_level_name,
            'replacing_teacher' => $this->replacing_lesson->teacher->profession_level_name,
            'group' => $this->replaceable_lesson->groups_name,
            'replaceable_week_day' => $this->replaceable_lesson->week_day->shot_notation,
            'replacing_week_day' => $this->replacing_lesson->week_day->shot_notation,
            'replaceable_class_period' => $this->replaceable_lesson->class_period_id,
            'replacing_class_period' => $this->replacing_lesson->class_period_id,
            'replaceable_date' => '',
            'replacing_date' => '',
        ];

        if (! $this->is_regular) {
            $data['replaceable_date'] = date('d.m.y', strtotime($this->replaceable_date));
            $data['replacing_date'] = date('d.m.y', strtotime($this->replacing_date));
        }

        return str_replace(array_keys($data), array_values($data), __('replacement_request.name'));
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

    protected static function booted()
    {
        static::creating(function ($replacement_request) {

        });

        static::created(function ($replacement_request) {

        });

        static::updating(function ($replacement_request) {
            ReplacementRequestHelpers::updatingStatus($replacement_request);
        });

        static::updated(function ($replacement_request) {
            ReplacementRequestHelpers::updatadStatus($replacement_request);
        });
    }
}
