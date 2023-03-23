<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Teacher extends Model
{
    use Sortable;
    public $sortable = ['last_name', 'gender', 'birth_year','faculty_id', 'department_id', 'professional_level_id', 'position_id', 'full_name'];
    
    public function fullNameSortable($query, $direction)
    {
        return  $query->orderBy('last_name', $direction);
    }

    public function ageSortable($query, $direction)
    {
        return  $query->orderBy('birth_year', $direction);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function professional_level()
    {
        return $this->belongsTo(ProfessionalLevel::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function academic_degree()
    {
        return $this->belongsTo(AcademicDegree::class);
    }

    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    
    public $additional_attributes = ['full_name', 'first_name_patronymic', 'profession_level_name', 'age'];
    
    public function getFullNameAttribute()
    {
        $patronymic = '';
        if (isset($this->patronymic)) {
            $patronymic = $this->patronymic;
        }
        return "{$this->last_name} {$this->first_name} {$patronymic}";
    }

    public function getFirstNamePatronymicAttribute()
    {
        $patronymic = '';
        if (isset($this->patronymic)) {
            $patronymic = $this->patronymic;
        }
        return "{$this->first_name} {$patronymic}";
    }

    public function getProfessionLevelNameAttribute()
    {
        $professional_level = __('dictionary.'.$this->professional_level->short_name);
        // $academic_degree = isset($this->academic_degree_id) ? ', '.$this->academic_degree->short_name : '';
        $last_name = $this->last_name;
        $first_name_abbr = mb_substr($this->first_name, 0, 1).'.';
        $patronymic_abbr = isset($this->patronymic) ? mb_substr($this->patronymic, 0, 1).'.' : '';
        
        return "{$professional_level} {$last_name} {$first_name_abbr}{$patronymic_abbr}";
    }

    public function getAgeAttribute()
    {
        return Carbon::parse("{$this->birth_year}-06-15")->diffInYears();
    }

}
