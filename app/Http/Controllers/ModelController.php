<?php

namespace App\Http\Controllers;

use App\Helpers\FilterHelpers;
use App\Helpers\ModelHelpers;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    public function getInstances (Request $request)
    {
        $rows_per_page = config('site.rows_per_page');
                
        $request->flash();
        $data['table_properties'] = config("tables.{$this->instance_plural_name}");
        $data['filter_form_fields'] = $this->model_name::getFilterFormFields();
        $properties = $this->model_name::getProperties();
        
        if (!isset($request->sort)) {
            if (isset($request->deleted_instance_name)) {
                $data['deleted_instance_name'] = $request->deleted_instance_name;    
            }
            if (isset($request->deleting_instance_not_found)) {
                $data['deleting_instance_not_found'] = true;   
            }
            if (isset($request->updated_instance_name)) {
                $data['updated_instance_name'] = $request->updated_instance_name;    
            }
        }
        
        $instances = FilterHelpers::getFilteredQuery($this->model_name::with($this->eager_loading_fields), $request->all(), $this->model_name);
        $appends = ModelHelpers::getAppends($request);

        $data['instances'] = $instances->sortable()->paginate($rows_per_page)->appends($appends);

        return array_merge($data, $properties);
    }
    
    public function getInstanceFormData (Request $request)
    {
        $data = $this->model_name::getProperties();
        $data['add_form_fields'] = $this->model_name::getAddFormFields();
        if (isset($request->updating_id)) {
            $updating_instance = $this->model_name::where('id', $request->updating_id)->first();
            if ($updating_instance) {
                $data = array_merge($data, ['updating_instance' => $updating_instance]);
            }
        }
        if (isset($request->new_instance_name)) {
            $data = array_merge($data, ['new_instance_name' => $request->new_instance_name]);
        }

        return $data;
    }

    public function addOrUpdateInstance (Request $request)
    {
        $instance_name_field = $this->instance_name_field;
        
        $data = $this->model_name::getProperties();
        $data['filter_form_fields'] = $this->model_name::getFilterFormFields();
        $data['add_form_fields'] = $this->model_name::getAddFormFields();
        
        $instance = ModelHelpers::addOrUpdateInstance($request->all(), $this->model_name);
        if (isset($request->updating_id)) {
            return ['updated_instance_name' => $instance->$instance_name_field];
        } else {
            return ['new_instance_name' => $instance->$instance_name_field];
        }
    }
}
