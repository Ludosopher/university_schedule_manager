<?php

namespace App\Helpers;

use App\Department;
use App\Faculty;
use App\Position;
use App\ProfessionalLevel;
use App\StudyDegree;
use App\StudyForm;
use App\StudyOrientation;
use App\StudyProgram;

class DictionaryHelpers
{
    public static function getTeacherProperties() {
        return [
            'faculties' => Faculty::select('id', 'name')->get(),
            'departments' => Department::select('id', 'name')->get(),
            'professional_levels' => ProfessionalLevel::select('id', 'name')->get(),
            'positions' => Position::select('id', 'name')->get(),
            'genders' => config('enum.genders')
        ];
    }

    public static function getGroupProperties() {
        return [
            'faculties' => Faculty::select('id', 'name')->get(),
            'study_programs' => StudyProgram::select('id', 'name')->get(),
            'study_orientations' => StudyOrientation::select('id', 'name')->get(),
            'study_degrees' => StudyDegree::select('id', 'name')->get(),
            'study_forms' => StudyForm::select('id', 'name')->get(),
        ];
    }
}