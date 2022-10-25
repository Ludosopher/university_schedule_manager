<?php

namespace App\Helpers;

use App\Lesson;
use App\Teacher;

class FilterHelpers
{
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
            } elseif (isset($conditions['calculated value'])) {
                if (isset($data[$field])) {
                    $method = $conditions['method'];
                    if (isset($conditions['db_field'])) {
                        $db_field = $conditions['db_field'];
                        $query = $query->$method($db_field, $conditions['operator'], $conditions['calculated value']($data[$field]));    
                    } else {
                        $query = $query->$method($field, $conditions['operator'], $conditions['calculated value']($data[$field]));
                    }
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

    public static function getFilteredArrayOfArrays($array_arrays, $data) {

        $filterReplacementConditions = Lesson::filterReplacementConditions();
        $filtered_array_arrays = [];
        $is_suitable_element = true;

        foreach ($array_arrays as $array) {
            foreach ($filterReplacementConditions as $key => $condition) {
                if (isset($data[$key]) && isset($array[$key])) {
                    if ($condition['operator'] == 'not_equal') {
                        if (is_array($array[$key])) {
                            if ($array[$key]['id'] != $data[$key]) {
                                $is_suitable_element = false;
                                break;    
                            }
                        } else {
                            if ($array[$key] != $data[$key]) {
                                $is_suitable_element = false;
                                break;    
                            }
                        }
                    }
                    if ($condition['operator'] == 'not_like') {
                        if (strpos($array[$key], $data[$key]) === false) {
                            $is_suitable_element = false;
                            break;    
                        }
                    }
                    if ($condition['operator'] == 'less_then') {
                        if ($array[$key] < $data[$key]) {
                            $is_suitable_element = false;
                            break;    
                        }
                    }
                    if ($condition['operator'] == 'more_then') {
                        if ($array[$key] > $data[$key]) {
                            $is_suitable_element = false;
                            break;    
                        }
                    }
                }
            }
            if ($is_suitable_element) {
                $filtered_array_arrays[] = $array;
            }
            $is_suitable_element = true;
        }
        
        return $filtered_array_arrays;
    }
    

}