<?php

namespace App\Helpers;

use App\Lesson;
use App\Teacher;

class FilterHelpers
{
    public static function getFilteredQuery($query, $data, $instance_name) {
       
        $filter_conditions = config("filters.{$instance_name}");
        foreach ($filter_conditions as $field => $conditions) {
            if ($conditions['method'] == 'where' && is_array($conditions['operator'])) {
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
            } elseif (isset($conditions['calculated_value']) && $conditions['method'] == 'where') {
                if (isset($data[$field])) {
                    $method = $conditions['method'];
                    if (isset($conditions['db_field'])) {
                        $db_field = $conditions['db_field'];
                        $query = $query->$method($db_field, $conditions['operator'], $conditions['calculated_value']($data[$field]));    
                    } else {
                        $query = $query->$method($field, $conditions['operator'], $conditions['calculated_value']($data[$field]));
                    }
                }
            } elseif ($conditions['method'] == 'whereHas' && is_array($conditions['operator'])) {
                if (isset($data[$field])) {
                    $method = $conditions['method'];
                    $query = $query->$method($conditions['eager_field'], function($q) use ($conditions, $data, $field) {
                        foreach ($conditions['operator'] as $sub_field => $sub_conditions) {
                            $sub_method = $sub_conditions['method'];
                            $q = $q->$sub_method($sub_field, $sub_conditions['operator'], $data[$field]);
                        }
                    });
                }    
            } elseif ($conditions['method'] == 'whereIn') {
                if (isset($data[$field])) {
                    $method = $conditions['method'];
                    if (isset($conditions['db_field'])) {
                        $db_field = $conditions['db_field'];
                        $query = $query->$method($db_field, $data[$field]);    
                    } else {
                        $query = $query->$method($field, $data[$field]);
                    }
                }    
            } elseif ($conditions['method'] == 'whereRaw') {
                if (isset($data[$field])) {
                    $method = $conditions['method'];
                    if (isset($conditions['db_field'])) {
                        $db_field = $conditions['db_field'];
                        $query = $query->$method($db_field, [$conditions['calculated_value']($data[$field])]);    
                    } else {
                        $query = $query->$method($field, [$conditions['calculated_value']($data[$field])]);
                    }
                }    
            } else  {
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

        $filterReplacementConditions = config('filters.lesson_replacement');
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
                    if ($condition['operator'] == 'multi_not_equal') {
                        if (is_array($array[$key])) {
                            if (!in_array($array[$key]['id'], $data[$key])) {
                                $is_suitable_element = false;
                                break;    
                            }
                        } else {
                            if (!in_array($array[$key], $data[$key])) {
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