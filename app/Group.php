<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Group extends Model
{
    use Sortable;
    public $sortable = ['name', 'faculty_id', 'study_program_id', 'study_orientation_id', 'study_degree_id', 'study_form_id', 'course', 'size'];
    
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function study_program()
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function study_orientation()
    {
        return $this->belongsTo(StudyOrientation::class);
    }

    public function study_degree()
    {
        return $this->belongsTo(StudyDegree::class);
    }

    public function study_form()
    {
        return $this->belongsTo(StudyForm::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public $additional_attributes = ['name'];
    
    public function getNameAttribute()
    {
        $study_degree = $this->study_degree->abbreviation;
        $study_form = $this->study_form->abbreviation;
        $faculty = $this->faculty->abbreviation;
        $cours = $this->course->number;
        $study_program = $this->study_program->abbreviation;
        $study_orientation = mb_strtolower($this->study_orientation->abbreviation);
        $additional_id = isset($this->additional_id) ? "/$this->additional_id" : "";
        
        return "{$study_degree}.{$study_form}.{$faculty}-{$cours}-{$study_program}({$study_orientation}){$additional_id}";
    }

    public static function rules($request)
    {
        return [
            'faculty_id' => 'required|integer|exists:App\Faculty,id',
            'study_program_id' => 'required|integer|exists:App\StudyProgram,id',
            'study_program_id' => function ($attribute, $value, $fail) use ($request) {
                if (!in_array($value, StudyProgram::where('faculty_id', $request->faculty_id)->pluck('id')->toArray())) $fail('Discrepancy between Faculty and Study Program!');
            },
            'study_program_id' => function ($attribute, $value, $fail) use ($request) {
                if (!in_array($value, StudyProgram::where('study_degree_id', $request->study_degree_id)->pluck('id')->toArray())) $fail('Discrepancy between Study Degree and Study Program!');
            },
            'study_orientation_id' => 'required|integer|exists:App\StudyOrientation,id',
            'study_orientation_id' => function ($attribute, $value, $fail) use ($request) {
                if (!in_array($value, StudyOrientation::where('study_program_id', $request->study_program_id)->pluck('id')->toArray())) $fail('Discrepancy between Study Program and Study Orientation!');
            },
            'study_degree_id' => 'required|integer|exists:App\StudyDegree,id',
            'study_form_id' => 'required|integer|exists:App\StudyForm,id',
            'course_id' => 'required|integer|exists:App\Course,id',
            'size' => 'required|integer', 
            'updating_id' => 'nullable|integer|exists:App\Group,id',
        ];
    }

    public static function filterRules()
    {
        return [
            'group_id' => 'nullable|array',
            'faculty_id' => 'nullable|array',
            'study_program_id' => 'nullable|array',
            'study_orientation_id' => 'nullable|array',
            'study_degree_id' => 'nullable|array',
            'study_form_id' => 'nullable|array',
            'course_id' => 'nullable|array',
            'size' => 'nullable|integer',
        ];
    }

    public static function attrNames()
    {
        return [
            'name' => 'name',
            'faculty_id' => 'faculty',
            'study_program_id' => 'study program',
            'study_orientation_id' => 'study orientation',
            'study_degree_id' => 'study degree',
            'study_form_id' => 'study form',
            'course_id' => 'course',
            'size' => 'size',
        ];
    }

    public static function filterConditions()
    {
        return [
            'id' => [
                'method' => 'whereIn'
            ], 
            'faculty_id' => [
                'method' => 'whereIn'
            ],
            'study_program_id' => [
                'method' => 'whereIn'
            ],
            'study_orientation_id' => [
                'method' => 'whereIn'
            ],
            'study_degree_id' => [
                'method' => 'whereIn'
            ],
            'study_form_id' => [
                'method' => 'whereIn'
            ],
            'course_id' => [
                'method' => 'whereIn'
            ],
            'size_from' => [
                'db_field' => 'size',
                'method' => 'where',
                'operator' => '>='
            ],
            'size_to' => [
                'db_field' => 'size',
                'method' => 'where',
                'operator' => '<='
            ],
        ];
    }

    public static function getAddFormFields()
    {
        return [
            [
                'type' => 'objects-select',
                'plural_name' => 'faculties',
                'name' => 'faculty',
                'header' => 'Факультет',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'study_programs',
                'name' => 'study_program',
                'header' => 'Учебная программа',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'study_orientations',
                'name' => 'study_orientation',
                'header' => 'Специальность',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'study_degrees',
                'name' => 'study_degree',
                'header' => 'Уровень образования',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'study_forms',
                'name' => 'study_form',
                'header' => 'Форма образования',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'courses',
                'name' => 'course',
                'header' => 'Курс',
            ],
            [
                'type' => 'input',
                'input_type' => 'number',
                'name' => 'size',
                'header' => 'Численность',
            ],
        ];
    }

    public static function getFilterFormFields()
    {
        return [
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3,
                ],
                'plural_name' => 'groups',
                'name' => '',
                'header' => 'Группа',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 2,
                ],
                'plural_name' => 'faculties',
                'name' => 'faculty',
                'header' => 'Факультет',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 2,
                ],
                'plural_name' => 'study_programs',
                'name' => 'study_program',
                'header' => 'Учебная программа',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 2,
                ],
                'plural_name' => 'study_orientations',
                'name' => 'study_orientation',
                'header' => 'Специальность',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 2,
                ],
                'plural_name' => 'study_degrees',
                'name' => 'study_degree',
                'header' => 'Уровень образования',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 2,
                ],
                'plural_name' => 'study_forms',
                'name' => 'study_form',
                'header' => 'Форма образования',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 2,
                ],
                'plural_name' => 'courses',
                'name' => 'course',
                'header' => 'Курс',
            ],
            [
                'type' => 'between',
                'name' => 'size',
                'header' => 'Численность',
                'min_value' => '1',
                'max_value' => '50',
                'step' => '1'
            ],
        ];
    }

    public static function getProperties() {
        return [
            'groups' => Group::orderBy('study_degree_id')->orderBy('study_form_id')->orderBy('faculty_id')->orderBy('course_id')->get(),
            'faculties' => Faculty::select('id', 'name')->get(),
            'study_programs' => StudyProgram::select('id', 'name')->get(),
            'study_orientations' => StudyOrientation::select('id', 'name')->get(),
            'study_degrees' => StudyDegree::select('id', 'name')->get(),
            'study_forms' => StudyForm::select('id', 'name')->get(),
            'courses' => Course::select('id', 'name')->get(),
        ];
    }
}
