<?php

namespace App\Http\Controllers;

use App\Department;
use App\Faculty;
use App\Helpers\DictionaryHelpers;
use App\Helpers\FilterHelpers;
use App\Helpers\ModelHelpers;
use App\Teacher;
use App\Helpers\TeacherHelpers;
use App\Position;
use App\ProfessionalLevel;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends ModelController
{
    protected $model_name = 'App\Teacher';
    protected $instance_name_field = 'teacher_full_name';
    protected $instance_deleted_message = 'Данные о преподавателе {name} удалены.';
    protected $instance_not_found_message = 'Такой преподаватель не найден!';
    protected $eager_loading_fields = ['faculty', 'department', 'professional_level', 'position'];
    protected $get_properties_function_name = 'getTeacherProperties';
    
    public function getTeachers (Request $request)
    {
        $data = [
            'model_name' => $this->model_name,
            'messages' => [
                'instance_name_field' => $this->instance_name_field,
                'instance_deleted' => $this->instance_deleted_message,
                'instance_not_found' => $this->instance_not_found_message,
            ],
            'eager_loading_fields' => $this->eager_loading_fields,
            'get_properties_function_name' => $this->get_properties_function_name,
        ];

        $data = $this->getInstances($request, $data);

        if (isset($data['validator'])) {
            return redirect()->route('teachers-filter')->withErrors($data['validator'])->withInput();   
        }
        
        return view('teacher.teachers')->with('data', $data);
    }
    
    public function addTeacherForm (Request $request)
    {
        $data = [
            'model_name' => $this->model_name,
            'get_properties_function_name' => $this->get_properties_function_name,
        ];
        
        $data = $this->getInstanceFormData($request, $data);
        
        return view('teacher.add_teacher_form')->with('data', $data);
    }

    public function addOrUpdateTeacher (Request $request)
    {
        $data = [
            'model_name' => $this->model_name,
            'instance_name_field' => $this->instance_name_field,
            'eager_loading_fields' => $this->eager_loading_fields,
            'get_properties_function_name' => $this->get_properties_function_name,
        ];
        
        $data = $this->addOrUpdateInstance($request, $data);
                
        if ($data['is_validation_errors'] && isset($data['validator'])) {
            return redirect()->route('teacher-form')->withErrors($data['validator'])->withInput();
        } elseif ($data['is_updating']) {
            return view('teacher.teachers')->with('data', $data['data']);
        } else {
            return view('teacher.add_teacher_form')->with('data', $data['data']);
        }
    }

    // public function addOrUpdateTeacher (Request $request)
    // {
    //     $data = [
    //         'model_name' => 'App\Teacher',
    //         'instance_name_field' => 'teacher_full_name',
    //         'eager_loading_fields' => ['faculty', 'department', 'professional_level', 'position'],
    //         'get_properties_function_name' => 'getTeacherProperties'
    //     ];
        
    //     $validator = Validator::make($request->all(), Teacher::rules(), [], Teacher::attrNames());
        
    //     $data = DictionaryHelpers::getTeacherProperties();

    //     if ($validator->fails()) {
    //         $data['errors'] = $validator->errors();
    //         $data['old_data'] = $request->all();
    //     } elseif (isset($request->updating_id)) {
    //         $rows_per_page = config('site.rows_per_page');
            
    //         $teacher = ModelHelpers::addOrUpdateInstance($request->all(), 'App\Teacher');
    //         $data['updated_teacher_name'] = "{$teacher->last_name} {$teacher->first_name} ".(isset($teacher->patronymic) ? $teacher->patronymic : '');
    //         $data_based_on_page = FilterHelpers::actionsBasedOnPage($request, Teacher::with(['faculty', 'department', 'professional_level', 'position']));
    //         $data = FilterHelpers::getInstancesData($data, $rows_per_page, $data_based_on_page);
            
    //         return view('teacher.teachers')->with('data', array_merge($data, DictionaryHelpers::getTeacherProperties()));
    //     } else {
    //         $teacher = ModelHelpers::addOrUpdateInstance($request->all(), 'App\Teacher');
    //         $data['new_teacher_name'] = "{$teacher->last_name} {$teacher->first_name} ".(isset($teacher->patronymic) ? $teacher->patronymic : '');
            
    //         return view('teacher.add_teacher_form')->with('data', $data);
    //     };
    // }  
}
