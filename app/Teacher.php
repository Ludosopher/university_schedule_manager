<?php

namespace App;

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
    
    public $additional_attributes = ['full_name', 'profession_level_name', 'age'];
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

    public function getAgeAttribute()
    {
        $birth_year = new \DateTime($this->birth_year);
        $carrent_date = new \DateTime();
        $diff = $carrent_date->diff($birth_year);

        return $diff->y;
    }

    // public static function rules($request)
    // {
    //     return [
    //         'first_name' => 'required|string',
    //         'last_name' => 'required|string',
    //         'patronymic' => 'nullable|string',
    //         'gender' => 'required|in:мужчина,женщина,не указано',
    //         'birth_year' => 'required|integer|min:1900|max:2099',
    //         'phone' => 'required|string',
    //         'email' => 'required|email',
    //         'faculty_id' => 'required|integer|exists:App\Faculty,id',
    //         'department_id' => 'required|integer|exists:App\Department,id',
    //         'department_id' => function ($attribute, $value, $fail) use ($request) {
    //             if (!in_array($value, Department::where('faculty_id', $request->faculty_id)->pluck('id')->toArray())) $fail(__('user_validation.faculty_department_discrepancy'));
    //         },
    //         'professional_level_id' => 'required|integer|exists:App\ProfessionalLevel,id',
    //         'position_id' => 'required|integer|exists:App\Position,id',
    //         'academic_degree_id' => 'nullable|integer|exists:App\AcademicDegree,id',
    //         'updating_id' => 'nullable|integer|exists:App\Teacher,id',
    //     ];
    // }

    // public static function filterRules()
    // {
    //     return [
    //         'full_name' => 'nullable|string',
    //         'age_from' => 'nullable|integer',
    //         'age_to' => 'nullable|integer',
    //         'faculty_id' => 'nullable|array',
    //         'department_id' => 'nullable|array',
    //         'professional_level_id' => 'nullable|array',
    //         'position_id' => 'nullable|array',
    //         'academic_degree_id' => 'nullable|array',
    //     ];
    // }

    // public static function attrNames()
    // {
    //     return [
    //         'first_name' => __('attribute_names.first_name'),
    //         'last_name' => __('attribute_names.last_name'),
    //         'patronymic' => __('attribute_names.patronymic'),
    //         'gender' => __('attribute_names.gender'),
    //         'birth_year' => __('attribute_names.birth_year'),
    //         'phone' => __('attribute_names.phone'),
    //         'email' => __('attribute_names.email'),
    //         'faculty_id' => __('attribute_names.faculty_id'),
    //         'department_id' => __('attribute_names.department_id'),
    //         'professional_level_id' => __('attribute_names.professional_level_id'),
    //         'position_id' =>  __('attribute_names.position_id'),
    //         'academic_degree_id' => __('attribute_names.academic_degree_id'),
    //         'updating_id' => __('attribute_names.updating_id')
    //     ];
    // }

    // public static function filterAttrNames()
    // {
    //     return [
    //         'full_name' => __('attribute_names.full_name'),
    //         'age_from' => __('attribute_names.age_from'),
    //         'age_to' => __('attribute_names.age_to'),
    //         'faculty_id' => __('attribute_names.faculty_id'),
    //         'department_id' => __('attribute_names.department_id'),
    //         'professional_level_id' => __('attribute_names.professional_level_id'),
    //         'position_id' =>  __('attribute_names.position_id'),
    //         'academic_degree_id' => __('attribute_names.academic_degree_id'),
    //     ];
    // }

    // public static function filterConditions()
    // {
    //     return [
    //         'full_name' => [
    //             'method' => 'where',
    //             'operator' => [
    //                 'first_name' => [
    //                     'method' => 'orWhere',
    //                     'operator' => 'like'
    //                 ],
    //                 'last_name' => [
    //                     'method' => 'orWhere',
    //                     'operator' => 'like'
    //                 ],
    //                 'patronymic' => [
    //                     'method' => 'orWhere',
    //                     'operator' => 'like'
    //                 ],
    //             ]
    //         ],
    //         'age_from' => [
    //             'db_field' => 'birth_year',
    //             'method' => 'where',
    //             'operator' => '<',
    //             'calculated_value' => function ($age) {
    //                 return now()->subYear($age);
    //             } 
    //         ],
    //         'age_to' => [
    //             'db_field' => 'birth_year',
    //             'method' => 'where',
    //             'operator' => '>',
    //             'calculated_value' => function ($age) {
    //                 return now()->subYear($age);
    //             }
    //         ],
    //         'faculty_id' => [
    //             'method' => 'whereIn',
    //         ],
    //         'department_id' => [
    //             'method' => 'whereIn',
    //         ],
    //         'professional_level_id' => [
    //             'method' => 'whereIn',
    //         ],
    //         'position_id' => [
    //             'method' => 'whereIn',
    //         ],
    //         'academic_degree_id' => [
    //             'method' => 'whereIn',
    //         ],
    //     ];
    // }

    // public static function getAddFormFields()
    // {
    //     return [
    //         [
    //             'type' => 'enum-select',
    //             'plural_name' => 'genders',
    //             'name' => 'gender',
    //             'header' => 'Пол'
    //         ],
    //         [
    //             'type' => 'input',
    //             'input_type' => 'text',
    //             'name' => 'last_name',
    //             'header' => 'Фамилия',
    //         ],
    //         [
    //             'type' => 'input',
    //             'input_type' => 'text',
    //             'name' => 'first_name',
    //             'header' => 'Имя'
    //         ],
    //         [
    //             'type' => 'input',
    //             'input_type' => 'text',
    //             'name' => 'patronymic',
    //             'header' => 'Отчество'
    //         ],
    //         [
    //             'type' => 'input',
    //             'input_type' => 'number',
    //             'name' => 'birth_year',
    //             'header' => 'Год рождения'
    //         ],
    //         [
    //             'type' => 'input',
    //             'input_type' => 'text',
    //             'name' => 'phone',
    //             'header' => 'Телефон',
    //         ],
    //         [
    //             'type' => 'input',
    //             'input_type' => 'email',
    //             'name' => 'email',
    //             'header' => 'Электронная почта',
    //         ],
    //         [
    //             'type' => 'objects-select',
    //             'plural_name' => 'faculties',
    //             'name' => 'faculty',
    //             'header' => 'Факультет',
    //         ],
    //         [
    //             'type' => 'objects-select',
    //             'plural_name' => 'departments',
    //             'name' => 'department',
    //             'header' => 'Кафедра',
    //         ],
    //         [
    //             'type' => 'objects-select',
    //             'plural_name' => 'professional_levels',
    //             'name' => 'professional_level',
    //             'header' => 'Профессиональный уровень',
    //         ],
    //         [
    //             'type' => 'objects-select',
    //             'plural_name' => 'positions',
    //             'name' => 'position',
    //             'header' => 'Должность',
    //         ],
    //         [
    //             'type' => 'objects-select',
    //             'plural_name' => 'academic_degrees',
    //             'name' => 'academic_degree',
    //             'header' => 'Учёная степень',
    //         ]
    //     ];
    // }

    // public static function getFilterFormFields()
    // {
    //     return [
    //         [
    //             'type' => 'input',
    //             'input_type' => 'text',
    //             'name' => 'full_name',
    //             'header' => 'Фамилия, Имя или Отчество'
    //         ],
    //         [
    //             'type' => 'between',
    //             'name' => 'age',
    //             'header' => 'Возраст',
    //             'min_value' => '',
    //             'max_value' => '',
    //             'step' => '1'
    //         ],
    //         [
    //             'type' => 'objects-select',
    //             'multiple_options' => [
    //                 'is_multiple' => true,
    //                 'size' => 2,
    //                 // 'explanation' => "Для выбора нескольких факультетов нажмите и удерживайте клавишу 'Ctrl'"
    //             ],
    //             'plural_name' => 'faculties',
    //             'name' => 'faculty',
    //             'header' => 'Факультет',
    //         ],
    //         [
    //             'type' => 'objects-select',
    //             'multiple_options' => [
    //                 'is_multiple' => true,
    //                 'size' => 2,
    //                 // 'explanation' => "Для выбора нескольких кафедр нажмите и удерживайте клавишу 'Ctrl'"
    //             ],
    //             'plural_name' => 'departments',
    //             'name' => 'department',
    //             'header' => 'Кафедра',
    //         ],
    //         [
    //             'type' => 'objects-select',
    //             'multiple_options' => [
    //                 'is_multiple' => true,
    //                 'size' => 2,
    //                 // 'explanation' => "Для выбора нескольких уровней нажмите и удерживайте клавишу 'Ctrl'"
    //             ],
    //             'plural_name' => 'professional_levels',
    //             'name' => 'professional_level',
    //             'header' => 'Профессиональный уровень',
    //         ],
    //         [
    //             'type' => 'objects-select',
    //             'multiple_options' => [
    //                 'is_multiple' => true,
    //                 'size' => 2,
    //                 // 'explanation' => "Для выбора нескольких должностей нажмите и удерживайте клавишу 'Ctrl'"
    //             ],
    //             'plural_name' => 'positions',
    //             'name' => 'position',
    //             'header' => 'Должность',
    //         ],
    //         [
    //             'type' => 'objects-select',
    //             'multiple_options' => [
    //                 'is_multiple' => true,
    //                 'size' => 2,
    //                 // 'explanation' => "Для выбора нескольких степеней нажмите и удерживайте клавишу 'Ctrl'"
    //             ],
    //             'plural_name' => 'academic_degrees',
    //             'name' => 'academic_degree',
    //             'header' => 'Учёная степень',
    //         ]
            
    //     ];
    // }

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
