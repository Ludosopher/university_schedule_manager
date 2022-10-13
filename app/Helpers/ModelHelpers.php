<?php

namespace App\Helpers;

use App\Teacher;
use Illuminate\Support\Facades\Schema;

class ModelHelpers
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

    public static function getAppends($request) {
        $appends = [];
        foreach ($request->all() as $key => $value) {
            if ($key != '_token' 
                && $key != 'deleted_instance_name'
                && $key != 'deleting_instance_not_found'
                && $key != 'updated_instance_name' 
                && $value != null) 
            {
                $appends[$key] = $value;
            }
        }

        return $appends;
    }
}