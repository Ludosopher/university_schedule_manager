<?php

namespace App\Services;

use App\Helpers\DateHelpers;
use App\Helpers\DictionaryHelpers;
use App\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class Service
{
    /** @var array $config */
    protected $config;
    
    /**
     * Add or update instance.
     * 
     * @return Model|bool
     */
    public function addOrUpdate(array $data, string $model_name) 
    {
        if (!class_exists($model_name) || !is_subclass_of($model_name, Model::class)) {
            return false;
        }
        if (isset($data['updating_id'])) {
            $instance = $model_name::where('id', $data['updating_id'])->first();
        } else {
            $instance = new $model_name();
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

    /**
     * Delete instance and get deleted instance name.
     */
    public function deleteInstance(int $id): array 
    {
        $model_name = $this->config['model_name'];
        $deleting_instance = $model_name::where('id', $id)->first();
        
        $instance_name_field = $this->config['instance_name_field'];
        $model_name::where('id', $id)->delete();
        return ['deleted_instance_name' => $deleting_instance->$instance_name_field];
    }

    /**
     * Get additional parameters for pagination links.
     */
    protected function getAppends(array $data): array 
    {
        $appends = [];
        foreach ($data as $key => $value) {
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

    /**
     * Get the form data for adding or updating an instance.
     */
    public function getInstanceFormData (array $incoming_data)
    {
        $model_name = $this->config['model_name'];
        $dictionary_function = 'get'.ucfirst($this->config['instance_name']).'Properties';
        $data = DictionaryHelpers::$dictionary_function();
        $data['add_form_fields'] = config("forms.{$this->config['instance_name']}");
        $data['appelation'] = $this->config['instance_name'];
        if (isset($incoming_data['updating_id'])) {
            $updating_instance = $model_name::where('id', $incoming_data['updating_id'])->first();
            if ($updating_instance) {
                $data = array_merge($data, ['updating_instance' => $updating_instance]);
            }
        }
        if (isset($incoming_data['new_instance_name'])) {
            $data = array_merge($data, ['new_instance_name' => $incoming_data['new_instance_name']]);
        }

        return $data;
    }

    /**
     * Add or update instance and get created/updated instance name.
     */
    public function addOrUpdateInstance(array $data): array
    {
        $instance_name_field = $this->config['instance_name_field'];
        $model_name = $this->config['model_name'];

        $instance = $this->addOrUpdate($data, $model_name);
        if (!$instance) {
            return ['error' => __('model.invalid_model_name')];
        }
        
        if (isset($data['updating_id'])) {
            return ['id' => $instance->id, 'updated_instance_name' => $instance->$instance_name_field];
        } else {
            return ['id' => $instance->id, 'new_instance_name' => $instance->$instance_name_field];
        }
    }

    /**
     * Get filtered instances data.
     */
    public function getInstances(array $incoming_data): array
    {
        $rows_per_page_setting = Setting::where('name', 'default_rows_per_page')->first();
        $rows_per_page = $rows_per_page_setting ? $rows_per_page_setting->value : config('site.rows_per_page');
        $model_name = $this->config['model_name'];

        $instance_name_arr = explode('_', $this->config['instance_name']);
        foreach ($instance_name_arr as &$name_part) {
            $name_part = ucfirst($name_part);
        }
        $dictionary_instance_name = implode('', $instance_name_arr);
        $dictionary_function = 'get'.$dictionary_instance_name.'Properties';

        if (count($incoming_data)) {
            request()->flash();
        }

        $data['table_properties'] = config("tables.{$this->config['instance_plural_name']}");
        $data['filter_form_fields'] = config()->has("forms.{$this->config['instance_name']}_filter") ? config("forms.{$this->config['instance_name']}_filter") : [];
        $properties = DictionaryHelpers::$dictionary_function();

        $instances = $this->getFilteredQuery($model_name::with($this->config['eager_loading_fields']), $incoming_data);
        $appends = $this->getAppends($incoming_data);
       
        $data['instances'] = $instances->sortable()->paginate($rows_per_page)->appends($appends);
        $data['appelation'] = $this->config['instance_name'];
        $data['appelation_plural_name'] = $this->config['instance_plural_name'];

        return array_merge($data, $properties);
    }

    /**
     * Sync many-to-many relationships for a model.
     */
    public function addOrUpdateManyToManyAttributes(array $data, int $model_id) 
    {

        $model_name = $this->config['model_name'];
        $model = $model_name::find($model_id);
        
        foreach ($this->config['many_to_many_attributes'] as $field => $attribute) {
            if (isset($data[$field])) {
                $model->$attribute()->sync($data[$field]);
            } else {
                $model->$attribute()->detach();
            }
        }
        return true;
    }

    /**
     * Remove all many-to-many associations for the model.
     */
    public function deleteManyToManyAttributes(int $model_id): void 
    {
        $model_name = $this->config['model_name'];
        $model = $model_name::with(array_values($this->config['many_to_many_attributes']))->find($model_id);
        foreach ($this->config['many_to_many_attributes'] as $attribute) {
            $model->$attribute()->detach();
        }
    }

    /**
     * Populate data array with IDs from many-to-many relations.
     */
    public function getManyToManyData(array $data): array 
    {
        $instance = $data['updating_instance'];
        foreach ($this->config['many_to_many_attributes'] as $field => $attribute) {
            $attribute_ids = [];
            foreach ($instance->$attribute as $elem) {
                $attribute_ids[] = $elem->id;
            }
            $data['updating_instance']->$field = $attribute_ids;
        }
        return $data;
    }

    /**
     * Prepare boolean fields by setting unchecked fields to 0.
     */
    public function preparingBooleans(array $data): array 
    {
        foreach ($this->config['boolean_attributes'] as $attribute) {
            if (! isset($data[$attribute])) {
                $data[$attribute] = 0;
            }
        }
        return $data;
    }

    /**
     * Apply dynamic filters to the query based on configuration.
     *
     * This method reads filter conditions from the config file and applies them
     * to the query builder. Supports various filter types: where, whereHas,
     * whereIn, whereRaw, and nested conditions with logical grouping.
     */
    protected function getFilteredQuery(Builder $query, array $data): Builder  
    {
        $filter_conditions = config("filters.{$this->config['instance_name']}");
        foreach ($filter_conditions as $field => $conditions) {
            if ($conditions['method'] == 'where' && is_array($conditions['operator'])) {
                if (isset($data[$field])) {
                    $method = $conditions['method'];
                    $query = $query->$method(function($q) use ($data, $conditions, $field) {
                        foreach ($conditions['operator'] as $sub_field => $sub_conditions) {
                            $sub_method = $sub_conditions['method'];
                            if ($sub_method === 'whereHas' || $sub_method === 'orWhereHas') {
                                $q = $q->$sub_method($sub_field, function(Builder $que) use ($sub_conditions, $sub_method, $sub_field, $data, $field) {
                                    $que = $que->where($sub_conditions['final_field'], $sub_conditions['operator'], $data[$field]);
                                });    
                            } else {
                                $is_like = $sub_conditions['operator'] == 'like';
                                if (isset($sub_conditions['db_field'])) {
                                    $db_field = $sub_conditions['db_field'];
                                    $q = $q->$sub_method($db_field, $sub_conditions['operator'], ($is_like ? '%'.$data[$field].'%' : $data[$field]));
                                } else {
                                    $q = $q->$sub_method($sub_field, $sub_conditions['operator'], ($is_like ? '%'.$data[$field].'%' : $data[$field]));
                                }
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
                    $query = $query->$method($conditions['eager_field'], function(Builder $q) use ($conditions, $data, $field) {
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

    /**
     * Filter an array of arrays based on configuration conditions.
     *
     * This method applies filter conditions defined in the 'lesson_replacement'
     * config to an array of associative arrays. Supports various operators:
     * not_equal, multi_not_equal, not_like, less_then, more_then.
    */
    public function getFilteredArrayOfArrays(array $array_arrays, array $data): array
    {
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

    /**
     * Check if a lesson should be included in the schedule based on various conditions.
     *
     * This method verifies if a lesson (either object or array) matches the given
     * week number, study period, and replacement criteria. It handles both
     * regular and one-time lessons, and supports additional checks for
     * replaceable lessons and holiday dates.
     *
     * @param object|array $checking_lesson The lesson to check (Eloquent model or array).
     * @param string|null $week_number The week number to check against.
     * @param string $type The lesson type ('additionally' or empty string).
     * @param object|null $replaceble_lesson The lesson being replaced (for replacement logic).
     * @param array|null $week_dates Array of week dates with holiday information.
     * @param int|null $selected_study_period_id The selected study period ID.
     * @return bool|object
     */
    protected function checkLesson(
        $checking_lesson, 
        $week_number, 
        $type='', 
        $replaceble_lesson=null, 
        $week_dates=null, 
        $selected_study_period_id=null)
    {
        $weekly_period_ids = config('enum.weekly_period_ids');
        $study_period_id = null;
        $date = null;
        if (is_object($checking_lesson)) {
            $study_period_id = $checking_lesson->study_period_id ?? null;
            $date = $checking_lesson->date ?? null;
        } elseif (is_array($checking_lesson)) {
            $study_period_id = $checking_lesson['study_period_id'] ?? null;
            $date = $checking_lesson['date'] ?? null;
        }

        $current_period_id = $selected_study_period_id;
        if (! isset($selected_study_period_id)) {
            $study_periods_data = DateHelpers::getStudyPeriodsData();
            $current_period_id = $study_periods_data['current_period_id'];
        }
       
        if (! isset($week_number) && isset($study_period_id) && $study_period_id !== $current_period_id) {
            return false;
        }
        if (isset($week_number) && isset($study_period_id) && ! DateHelpers::checkRegularLessonToWeek($study_period_id, $week_number)) {
            return false;
        }
        if (! DateHelpers::checkOneTimeLessonToWeek($week_number, $date)) {
            return false;
        };
        $week_schedule_lesson = DateHelpers::getWeeklyScheduleLesson($week_number, $checking_lesson);
        $w_p_field = 'weekly_period_id';
        if (isset($week_schedule_lesson)) {
            if ($week_schedule_lesson) {
                $w_p_field = 'real_weekly_period_id'; // ??? 
                return $week_schedule_lesson;
            } else {
                return false;
            }
        }
        if ($type === 'additionally') {
            $is_periodic_replacing_lesson = $replaceble_lesson->weekly_period_id != $weekly_period_ids['every_week'];
            if ($checking_lesson->$w_p_field == $weekly_period_ids['every_week'] && $is_periodic_replacing_lesson
                || $checking_lesson->$w_p_field != $weekly_period_ids['every_week'] && ! $is_periodic_replacing_lesson)
            {
                return false;
            }
            if (isset($week_dates) && is_array($week_dates[$checking_lesson->weekly_period_id]) && isset($week_dates[$checking_lesson->weekly_period_id]['is_holiday'])) {
                return false;
            }
        }
        
        return true;
    }

}