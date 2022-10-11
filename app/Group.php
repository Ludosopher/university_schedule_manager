<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public function lessons()
    {
        return $this->hasMany('App\Lesson');
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

    public static function rules($request)
    {
        return [
            'name' => 'required|string',
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
            'course' => 'required|integer',
            'size' => 'required|integer', 
            'updating_id' => 'nullable|integer|exists:App\Group,id',
            'page_number' => 'nullable|integer'
        ];
    }

    public static function filterRules()
    {
        return [
            'name' => 'nullable|string',
            'faculty_id' => 'nullable|integer|exists:App\Faculty,id',
            'study_program_id' => 'nullable|integer|exists:App\StudyProgram,id',
            'study_orientation_id' => 'nullable|integer|exists:App\StudyOrientation,id',
            'study_degree_id' => 'nullable|integer|exists:App\StudyDegree,id',
            'study_form_id' => 'nullable|integer|exists:App\StudyForm,id',
            'course' => 'nullable|integer',
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
            'course' => 'course',
            'size' => 'size',
        ];
    }

    public static function filterConditions()
    {
        return [
            'name' => [
                'method' => 'where',
                'operator' => 'like'
            ], 
            'faculty_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'study_program_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'study_orientation_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'study_degree_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'study_form_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'course' => [
                'method' => 'where',
                'operator' => '='
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
                'type' => 'input',
                'input_type' => 'text',
                'name' => 'name',
                'header' => 'Группа',
            ],
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
                'type' => 'input',
                'input_type' => 'number',
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
                'type' => 'input',
                'input_type' => 'text',
                'name' => 'name',
                'header' => 'Название группы'
            ],
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
                'type' => 'input',
                'input_type' => 'number',
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
}
