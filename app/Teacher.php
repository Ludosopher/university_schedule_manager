<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Teacher extends Model
{
    use Sortable;
    public $sortable = ['last_name', 'gender', 'birth_year','faculty_id', 'department_id', 'professional_level_id', 'position_id'];
    
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
    
    public $additional_attributes = ['full_name', 'profession_level_name'];
    public function getFullNameAttribute()
    {
        $patronymic = '';
        if (isset($this->patronymic)) {
            $patronymic = $this->patronymic;
        }
        return "{$this->last_name} {$this->first_name} {$patronymic}";
    }

    public function getProfessionLevelNameAttribute()
    {
        $professional_level = $this->professional_level->short_name;
        // $academic_degree = isset($this->academic_degree_id) ? ', '.$this->academic_degree->short_name : '';
        $last_name = $this->last_name;
        $first_name_abbr = mb_substr($this->first_name, 0, 1).'.';
        $patronymic_abbr = isset($this->patronymic) ? mb_substr($this->patronymic, 0, 1).'.' : '';
        
        return "{$professional_level} {$last_name} {$first_name_abbr}{$patronymic_abbr}";
    }
    
    public static function rules($request)
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'patronymic' => 'nullable|string',
            'gender' => 'required|in:мужчина,женщина,не указано',
            'birth_year' => 'required|integer|min:1900|max:2099',
            'phone' => 'required|string',
            'email' => 'required|email',
            'faculty_id' => 'required|integer|exists:App\Faculty,id',
            'department_id' => 'required|integer|exists:App\Department,id',
            'department_id' => function ($attribute, $value, $fail) use ($request) {
                if (!in_array($value, Department::where('faculty_id', $request->faculty_id)->pluck('id')->toArray())) $fail('Discrepancy between faculty and department!');
            },
            'professional_level_id' => 'required|integer|exists:App\ProfessionalLevel,id',
            'position_id' => 'required|integer|exists:App\Position,id',
            'academic_degree_id' => 'nullable|integer|exists:App\AcademicDegree,id',
            'updating_id' => 'nullable|integer|exists:App\Teacher,id',
        ];
    }

    public static function filterRules()
    {
        return [
            'full_name' => 'nullable|string',
            'birth_year_from' => 'nullable|integer|min:1900|max:2099',
            'birth_year_to' => 'nullable|integer|min:1900|max:2099',
            'faculty_id' => 'nullable|integer|exists:App\Faculty,id',
            'department_id' => 'nullable|integer|exists:App\Department,id',
            'professional_level_id' => 'nullable|integer|exists:App\ProfessionalLevel,id',
            'position_id' => 'nullable|integer|exists:App\Position,id',
            'academic_degree_id' => 'nullable|integer|exists:App\AcademicDegree,id',
        ];
    }

    public static function attrNames()
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'patronymic' => 'patronymic',
            'gender' => 'gender',
            'birth_year' => 'birth year',
            'phone' => 'phone',
            'email' => 'email',
            'faculty_id' => 'faculty',
            'department_id' => 'department',
            'professional_level_id' => 'professional level',
            'position_id' => 'position',
            'academic_degree_id' => 'academic degree',
        ];
    }

    public static function filterConditions()
    {
        return [
            'full_name' => [
                'method' => 'where',
                'operator' => [
                    'first_name' => [
                        'method' => 'orWhere',
                        'operator' => 'like'
                    ],
                    'last_name' => [
                        'method' => 'orWhere',
                        'operator' => 'like'
                    ],
                    'patronymic' => [
                        'method' => 'orWhere',
                        'operator' => 'like'
                    ],
                ]
            ],
            'birth_year_from' => [
                'db_field' => 'birth_year',
                'method' => 'where',
                'operator' => '>='
            ],
            'birth_year_to' => [
                'db_field' => 'birth_year',
                'method' => 'where',
                'operator' => '<='
            ],
            'faculty_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'department_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'professional_level_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'position_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'academic_degree_id' => [
                'method' => 'where',
                'operator' => '='
            ],
        ];
    }

    public static function getAddFormFields()
    {
        return [
            [
                'type' => 'enum-select',
                'plural_name' => 'genders',
                'name' => 'gender',
                'header' => 'Пол'
            ],
            [
                'type' => 'input',
                'input_type' => 'text',
                'name' => 'last_name',
                'header' => 'Фамилия',
            ],
            [
                'type' => 'input',
                'input_type' => 'text',
                'name' => 'first_name',
                'header' => 'Имя'
            ],
            [
                'type' => 'input',
                'input_type' => 'text',
                'name' => 'patronymic',
                'header' => 'Отчество'
            ],
            [
                'type' => 'input',
                'input_type' => 'number',
                'name' => 'birth_year',
                'header' => 'Год рождения'
            ],
            [
                'type' => 'input',
                'input_type' => 'text',
                'name' => 'phone',
                'header' => 'Телефон',
            ],
            [
                'type' => 'input',
                'input_type' => 'email',
                'name' => 'email',
                'header' => 'Электронная почта',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'faculties',
                'name' => 'faculty',
                'header' => 'Факультет',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'departments',
                'name' => 'department',
                'header' => 'Кафедра',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'professional_levels',
                'name' => 'professional_level',
                'header' => 'Профессиональный уровень',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'positions',
                'name' => 'position',
                'header' => 'Должность',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'academic_degrees',
                'name' => 'academic_degree',
                'header' => 'Учёная степень',
            ]
        ];
    }

    public static function getFilterFormFields()
    {
        return [
            [
                'type' => 'input',
                'input_type' => 'text',
                'name' => 'full_name',
                'header' => 'Ф.И.О.'
            ],
            [
                'type' => 'between',
                'name' => 'birth_year',
                'header' => 'Год рождения',
                'min_value' => '1900',
                'max_value' => '2099',
                'step' => '1'
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'faculties',
                'name' => 'faculty',
                'header' => 'Факультет',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'departments',
                'name' => 'department',
                'header' => 'Кафедра',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'professional_levels',
                'name' => 'professional_level',
                'header' => 'Профессиональный уровень',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'positions',
                'name' => 'position',
                'header' => 'Должность',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'academic_degrees',
                'name' => 'academic_degree',
                'header' => 'Учёная степень',
            ]
        ];
    }

    public static function getProperties() {
        return [
            'faculties' => Faculty::select('id', 'name')->get(),
            'departments' => Department::select('id', 'name')->get(),
            'professional_levels' => ProfessionalLevel::select('id', 'name')->get(),
            'positions' => Position::select('id', 'name')->get(),
            'academic_degrees' => AcademicDegree::select('id', 'name')->get(),
            'genders' => config('enum.genders')
        ];
    }

}
