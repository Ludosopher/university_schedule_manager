<?php

namespace App\Helpers;

use App\Teacher;

class FilterHelpers
{
    public static function getPaginationData($rows_per_page, $query, $page) {
        
        return [
            'pages_number' => ceil($query->count() / $rows_per_page),
            'instances' => $query->paginate(7)->withQueryString()// $query->limit($rows_per_page)->offset(($page - 1) * $rows_per_page)->get()
        ];
    }

    public static function getFilteredQuery($query, $data, $model_name) {
        
        $filter_conditions = $model_name::filterConditions();

        foreach ($filter_conditions as $field => $conditions) {
            if (is_array($conditions['operator'])) {
                if (isset($data[$field])) {
                    $method = $conditions['method'];
                    $query = $query->$method(function($q) use ($data, $conditions, $field) {
                        foreach ($conditions['operator'] as $sub_field => $sub_conditions) {
                            $sub_method = $sub_conditions['method'];
                            $is_like = $sub_conditions['operator'] == 'like';
                            if (isset($sub_conditions['db_field'])) {
                                $db_field = $sub_conditions['db_field'];
                                $q = $q->$sub_method($db_field, $sub_conditions['operator'], ($is_like ? '%'.$data[$field].'%' : $data[$field]));
                            } else {
                                $q = $q->$sub_method($sub_field, $sub_conditions['operator'], ($is_like ? '%'.$data[$field].'%' : $data[$field]));
                            }
                        }
                        $q;
                    });
                }
            } else {
                if (isset($data[$field])) {
                    $method = $conditions['method'];
                    $is_like = $conditions['operator'] == 'like';
                     if (isset($conditions['db_field'])) {
                        $db_field = $conditions['db_field'];
                        $query = $query->$method($db_field, $conditions['operator'], ($is_like ? '%'.$data[$field].'%' : $data[$field]));    
                    } else {
                        $query = $query->$method($field, $conditions['operator'], ($is_like ? '%'.$data[$field].'%' : $data[$field]));
                    }
                }
            }
        }

        return $query;
    }
    
    public static function getInstancesData($data, $rows_per_page, $query, $page, $model_name) {
        
        $pagination_data = FilterHelpers::getPaginationData($rows_per_page, $query, $page);
        $model = new $model_name;
        $instances_table_data = config('tables.'.$model->getTable());
        return array_merge($data, [
            'headers' => array_column($instances_table_data, 'header'),
            'fields' => array_column($instances_table_data, 'field'),
            'instances' => $pagination_data['instances'],
            'pages_number' => $pagination_data['pages_number'],
        ]);
    }
}