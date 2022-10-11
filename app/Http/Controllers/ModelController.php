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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModelController extends Controller
{
    public function getInstances (Request $request, $incoming_data)
    {
        $rows_per_page = config('site.rows_per_page');
        $data = [];
        $model_name = $incoming_data['model_name'];
        $get_properties_function_name = $incoming_data['get_properties_function_name'];
        $instance_name_field = $incoming_data['messages']['instance_name_field'];

        if (isset($request->deleting_id)) {
            $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $model_name); 
            if ($deleted_instance) {
                $data['deleted_message'] = str_replace('{name}', $deleted_instance->$instance_name_field, $incoming_data['messages']['instance_deleted']);
            } else {
                $data['not_found_message'] = $incoming_data['messages']['instance_not_found'];
            }
        }
        
        $query = $model_name::with($incoming_data['eager_loading_fields']);
                
        $page = 1;
        $data['current_page'] = $page;
        $data['prev_request'] = [];
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), $model_name::filterRules());
            if ($validator->fails()) {
                $data['validator'] = $validator;
            } elseif (count($request->all())) {
                if (isset($request->page_number)) {
                    $page = $request->page_number;
                    $data['current_page'] = $page;
                    if (isset($request->prev_request)) {
                         $prev_request = json_decode($request->prev_request, true);
                        if (isset($prev_request['prev_request'])) {
                            unset($prev_request['prev_request']);
                        }
                        $query = FilterHelpers::getFilteredQuery($query, $prev_request, $model_name);
                        $data['prev_request'] = $prev_request;
                    }    
                } else {
                    $query = FilterHelpers::getFilteredQuery($query, $validator->validated(), $model_name);
                    $data['prev_request'] = $validator->validated();
                }
            }
        }
        
        $data = FilterHelpers::getInstancesData($data, $rows_per_page, $query, $page, $model_name);
        $data['filter_form_fields'] = $model_name::getFilterFormFields();
                        
        return array_merge($data, DictionaryHelpers::$get_properties_function_name());
    }
    
    public function getInstanceFormData (Request $request, $incoming_data)
    {
        $get_properties_function_name = $incoming_data['get_properties_function_name'];
        $model_name = $incoming_data['model_name'];

        $data = DictionaryHelpers::$get_properties_function_name();
        $data['add_form_fields'] = $model_name::getAddFormFields();
        if (isset($request->updating_id)) {
            $updating_instance = $model_name::where('id', $request->updating_id)->first();
            if ($updating_instance) {
                $data = array_merge($data, ['updating_instance' => $updating_instance]);
            }
        }

        return $data;
    }

    public function addOrUpdateInstance (Request $request, $incoming_data)
    {
        $model_name = $incoming_data['model_name'];
        $get_properties_function_name = $incoming_data['get_properties_function_name'];
        $instance_name_field = $incoming_data['instance_name_field'];
        
        $validator = Validator::make($request->all(), $model_name::rules($request), [], $model_name::attrNames());
        
        $data = DictionaryHelpers::$get_properties_function_name();
        $data['filter_form_fields'] = $model_name::getFilterFormFields();
        $data['add_form_fields'] = $model_name::getAddFormFields();
        $data['prev_request'] = [];
        $data['current_page'] = 1;

        if ($validator->fails()) {
                    
            return ['data' => $data,
                    'validator' => $validator,
                    'is_updating' => false,
                    'is_validation_errors' => true
                ];
        } elseif (isset($request->updating_id)) {
            $rows_per_page = config('site.rows_per_page');
            
            $instance = ModelHelpers::addOrUpdateInstance($request->all(), $model_name);
            $data['updated_instance_name'] = $instance->$instance_name_field;
            $query = $model_name::with($incoming_data['eager_loading_fields']);
            $page = 1;
            $data = FilterHelpers::getInstancesData($data, $rows_per_page, $query, $page, $model_name);
            
            return ['data' => array_merge($data, DictionaryHelpers::$get_properties_function_name()),
                    'is_updating' => true,
                    'is_validation_errors' => false
                ];
        } else {
            $instance = ModelHelpers::addOrUpdateInstance($request->all(), $model_name);
            $data['new_instance_name'] = $instance->$instance_name_field;
                                    
            return ['data' => $data,
                    'is_updating' => false,
                    'is_validation_errors' => false
                ];
        };
    }
}
