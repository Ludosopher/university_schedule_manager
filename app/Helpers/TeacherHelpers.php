<?php

namespace App\Helpers;

use App\Teacher;
use Illuminate\Support\Facades\Schema;

class TeacherHelpers
{
    public static function addOrUpdateInstance($data, $model) {

        if (isset($data['updating_id'])) {
            $instance = $model::where('id', $data['updating_id'])->first(); 
        } else {
            $instance = new $model();
        }

        $db_fields = Schema::getColumnListing($instance->getTable());

        foreach ($db_fields as $field) {
            if (isset($data[$field]) && $instance->$field != $data[$field]) {
                $instance->$field = $data[$field];
            }
        }
        
        $instance->save();

        return $instance;
    }

    public static function deleteInstance($id, $model) {
        $deleting_instance = $model::where('id', $id)->first();
        if ($deleting_instance) {
            $model::where('id', $id)->delete();
            return $deleting_instance;
        }

        return false;
    }

    // public static function _deleteTeacher($id, $data) {
        
    //     $deleting_teacher = Teacher::where('id', $id)->first();
    //     if ($deleting_teacher) {
    //         Teacher::where('id', $id)->delete();
    //         $data['deleted_message'] = "Данные о преподавателе {$deleting_teacher->teacher_full_name} удалены.";
    //     } else {
    //         $data['not_found_message'] = "Такой преподаватель не найден!";
    //     }

    //     return $data;
    // }

    // public static function addOrUpdateTeacher($data) {
        
        //     if (isset($data['updating_id'])) {
        //         $teacher = Teacher::where('id', $data['updating_id'])->first(); 
        //     } else {
        //         $teacher = new Teacher();
        //     }
            
        //     if ($teacher->first_name != $data['first_name']) {
        //         $teacher->first_name = $data['first_name'];
        //     }
        //     if ($teacher->last_name != $data['last_name']) {
        //         $teacher->last_name = $data['last_name'];
        //     }
        //     if (isset($data['patronymic']) && $teacher->patronymic != $data['patronymic']) {
        //         $teacher->patronymic = $data['patronymic'];
        //     }
        //     if ($teacher->gender != $data['gender']) {
        //         $teacher->gender = $data['gender'];
        //     }
        //     if ($teacher->birth_year != $data['birth_year']) {
        //         $teacher->birth_year = $data['birth_year'];
        //     }
        //     if ($teacher->phone != $data['phone']) {
        //         $teacher->phone = $data['phone'];
        //     }
        //     if ($teacher->email != $data['email']) {
        //         $teacher->email = $data['email'];
        //     }
        //     if ($teacher->faculty_id != $data['faculty_id']) {
        //         $teacher->faculty_id = $data['faculty_id'];
        //     }
        //     if ($teacher->department_id != $data['department_id']) {
        //         $teacher->department_id = $data['department_id'];
        //     }
        //     if ($teacher->professional_level_id != $data['professional_level_id']) {
        //         $teacher->professional_level_id = $data['professional_level_id'];
        //     }
        //     if ($teacher->position_id != $data['position_id']) {
        //         $teacher->position_id = $data['position_id'];
        //     }
            
        //     $teacher->save();
    
        //     return "{$teacher->last_name} {$teacher->first_name} ".(isset($teacher->patronymic) ? $teacher->patronymic : '');
        // }
}