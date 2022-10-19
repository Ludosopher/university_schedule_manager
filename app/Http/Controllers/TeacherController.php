<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Group;
use App\Helpers\ModelHelpers;
use App\Lesson;
use App\Teacher;
use App\WeekDay;
use App\WeeklyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends ModelController
{
    protected $model_name = 'App\Teacher';
    protected $instance_name = 'teacher';
    protected $instance_plural_name = 'teachers';
    protected $instance_name_field = 'full_name';
    protected $profession_level_name_field = 'profession_level_name';
    protected $eager_loading_fields = ['faculty', 'department', 'professional_level', 'position'];
    protected $other_lesson_participant = 'group';
    protected $other_lesson_participant_name = 'name';
    
    public function getTeachers (Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), $this->model_name::filterRules());
            if ($validator->fails()) {
                return redirect()->route("{$this->instance_name}.{$this->instance_plural_name}")->withErrors($validator)->withInput();
            }
        }
        
        $data = $this->getInstances($request);

        return view("{$this->instance_name}.{$this->instance_plural_name}")->with('data', $data);
    }

    public function addTeacherForm (Request $request)
    {
        $data = $this->getInstanceFormData($request);
        
        return view("{$this->instance_name}.add_{$this->instance_name}_form")->with('data', $data);
    }

    public function addOrUpdateTeacher (Request $request)
    {
        $validator = Validator::make($request->all(), $this->model_name::rules($request), [], $this->model_name::attrNames());
        if ($validator->fails()) {
            if (isset($request->updating_id)) {
                return redirect()->route("{$this->instance_name}-form", ['updating_id' => $request->updating_id])->withErrors($validator)->withInput();    
            }
            return redirect()->route("{$this->instance_name}-form")->withErrors($validator)->withInput(); 
        }
        
        $data = $this->addOrUpdateInstance($request);
                
        if (isset($data['updated_instance_name'])) {
            return redirect()->route("{$this->instance_plural_name}", ['updated_instance_name' => $data['updated_instance_name']]);
        } elseif (isset($data['new_instance_name'])) {
            return redirect()->route("{$this->instance_name}-form", ['new_instance_name' => $data['new_instance_name']]);
        }
    }

    public function deleteTeacher (Request $request)
    {
        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->model_name); 
            
        if ($deleted_instance) {
            $instance_name_field = $this->instance_name_field;
            return redirect()->route("{$this->instance_plural_name}", ['deleted_instance_name' => $deleted_instance->$instance_name_field]);
        } else {
            return redirect()->route("{$this->instance_plural_name}", ['deleting_instance_not_found' => true]);
        }
    }

    public function getTeacherSchedule (Request $request)
    {
        $validator = Validator::make($request->all(), [
            "schedule_{$this->instance_name}_id" => "required|integer|exists:{$this->model_name},id"
        ]);
        if ($validator->fails()) {
            return redirect()->route("{$this->instance_name}.{$this->instance_plural_name}")->withErrors($validator); 
        }

        $data = $this->getSchedule($request);

        if (isset($data['duplicated_lesson'])) {
            return redirect()->route("{$this->instance_plural_name}", ['duplicated_lesson' => $data['duplicated_lesson']]);
        }
        
        return view("{$this->instance_name}.{$this->instance_name}_schedule")->with('data', $data);
    }

    public function getTeachersForReplacement (Request $request)
    {
        $weekly_period_ids = config('enum.weekly_period_ids');
        $replace_lessons = [];
        
        $seeking_teacher = Teacher::where('id', $request->teacher_id)->with(['lessons'])->first();

        $group = Group::where('id', $request->group_id)->with(['lessons.teacher.lessons'])->first();
        
        $is_suitable_teacher = true;
        $is_suitable_lesson = true;
        
        $looked_teachers = [$seeking_teacher->id];
        foreach ($group->lessons as $g_lesson) {
            if (!in_array($g_lesson->teacher->id, $looked_teachers)) {
                foreach ($g_lesson->teacher->lessons as $dt_lesson) {
                    if ($dt_lesson->week_day_id == $request->week_day_id
                        && ($dt_lesson->weekly_period_id == $request->weekly_period_id || $dt_lesson->weekly_period_id == $weekly_period_ids['every_week'])
                        && $dt_lesson->class_period_id == $request->class_period_id)
                    {
                        $is_suitable_teacher = false;
                        break;
                    }
                }
                $looked_teachers[] = $g_lesson->teacher->id;
                if ($is_suitable_teacher) {
                    
                    foreach ($g_lesson->teacher->lessons as $dt_lesson) {
                        if ($dt_lesson->group_id == $request->group_id) {
                            foreach ($seeking_teacher->lessons as $st_lesson) {
                                if ($st_lesson->week_day_id == $dt_lesson->week_day_id
                                    && ($st_lesson->weekly_period_id == $dt_lesson->weekly_period_id || $st_lesson->weekly_period_id == $weekly_period_ids['every_week'])
                                    && $st_lesson->class_period_id == $dt_lesson->class_period_id)
                                {
                                    $is_suitable_lesson = false;
                                    break;
                                }
                            }
                            if ($is_suitable_lesson) {
                                
                                $replace_lessons[] = [
                                    'teacher_id' => $dt_lesson->teacher_id,
                                    'group_id' => $dt_lesson->group_id,
                                    'week_day_id' => $dt_lesson->week_day_id,
                                    'weekly_period_id' => $dt_lesson->weekly_period_id,
                                    'class_period_id' => $dt_lesson->class_period_id
                                ];
                            }
                            $is_suitable_lesson = true;
                        }
                    }
                }
                $is_suitable_teacher = true;
            }
        }

dd(['replace_lessons'=>$replace_lessons]);

        $group = Group::where('id', $request->group_id)->with(['lessons.teacher.lessons' => function ($query) use ($request, $weekly_period_ids) {
            $conditions = [
                ['group_id', '=', $request->group_id],
                ['teacher_id', '!=', $request->teacher_id],
                ['week_day_id', '!=', $request->week_day_id],
                ['class_period_id', '!=', $request->class_period_id],
            ];

            if ($request->weekly_period_id != $weekly_period_ids['every_week']) {
                $conditions = array_merge($conditions, [['weekly_period_id', '!=', $request->weekly_period_id], ['weekly_period_id', '!=', $weekly_period_ids['every_week']]]); 
            }
            $query = $query->where($conditions);
        }])->first();

        foreach ($group->lessons as $g_lesson) {
            
            foreach ($g_lesson->teacher->lessons as $dt_lesson) {
                echo '<pre>';
                echo print_r(['teacher_id'=>$dt_lesson->teacher_id, 'group_id'=>$dt_lesson->group_id, 'week_day_id'=>$dt_lesson->week_day_id, 'weekly_period_id'=>$dt_lesson->weekly_period_id, 'class_period_id'=>$dt_lesson->class_period_id]);
                echo '</pre>';
            }
        }
dd();
        $is_suitable = true;
        foreach ($group->lessons as $g_lesson) {
            foreach ($g_lesson->teacher->lessons as $dt_lesson) {
                // if ($dt_lesson->group_id == $request->group_id) {
                    foreach ($seeking_teacher->lessons as $st_lesson) {
                        if ($st_lesson->week_day_id == $dt_lesson->week_day_id
                            && $st_lesson->weekly_period_id == $dt_lesson->weekly_period_id
                            && $st_lesson->class_period_id == $dt_lesson->class_period_id)
                        {
                            $is_suitable = false;
                            break;
                        }
                    }
                    if ($is_suitable) {
                        
                        $replace_lessons[] = [
                            'teacher_id' => $dt_lesson->teacher_id,
                            'group_id' => $dt_lesson->group_id,
                            'week_day_id' => $dt_lesson->week_day_id,
                            'weekly_period_id' => $dt_lesson->weekly_period_id,
                            'class_period_id' => $dt_lesson->class_period_id
                        ];
                    }
                    $is_suitable = true;
                // }
            }   
        }
$replace_lessons = array_map("unserialize", array_unique(array_map("serialize", $replace_lessons)));
dd($replace_lessons);
        // $week_days = WeekDay::get();
        // $weekly_periods = WeeklyPeriod::get();
        // $class_periods = ClassPeriod::get();
        // $seeking_teacher = Teacher::where('id', $request->teacher_id)->with(['lessons'])->first();
        // $free_periods = [];
        // $is_free = true;
        // foreach ($week_days as $week_day) {
        //     foreach ($weekly_periods as $weekly_period) {
        //         foreach ($class_periods as $class_period) {
        //             foreach ($seeking_teacher->lessons as $lesson) {
        //                 if ($lesson->week_day_id == $week_day->id
        //                     && $lesson->weekly_period_id == $weekly_period->id
        //                     && $lesson->class_period_id == $class_period->id)
        //                 {
        //                     $is_free = false;
        //                     break;
        //                 }
        //             }
        //             if ($is_free) {
        //                 $free_periods[] = [
        //                     'week_day_id' => $week_day->id,
        //                     'weekly_period_id' => $weekly_period->id,
        //                     'class_period_id' => $class_period->id
        //                 ];
        //             }
        //             $is_free = true;
                    
        //         }
        //     }
        // }

// dd($free_periods);

        ////////////////
        $lesson_teachers = [$seeking_teacher->id];
echo '<pre>';
print_r(['request' => $request->all()]);
echo '</pre>';
        foreach ($group->lessons as $group_lesson) {
echo '<pre>';
echo '---------------------------------------------------------------------------------------------------------------------------------';
print_r(['group_lesson' => ['lesson_id' => $group_lesson->id, 'group_id' => $group_lesson->group_id, 'teacher_id' => $group_lesson->teacher_id, 'week_day_id' => $group_lesson->week_day_id, 'class_period_id' => $group_lesson->class_period_id]]);
echo '</pre>';
            if (!in_array($group_lesson->teacher->id, $lesson_teachers)) {
                foreach ($group_lesson->teacher->lessons as $teacher_lesson) {
    echo '<pre>';
    print_r(['desired_teacher_lesson' => ['lesson_id' => $teacher_lesson->id, 'group_id' => $teacher_lesson->group_id, 'teacher_id' => $teacher_lesson->teacher_id, 'week_day_id' => $teacher_lesson->week_day_id, 'class_period_id' => $teacher_lesson->class_period_id]]);
    echo '</pre>';                
                    if ($teacher_lesson->week_day_id == $request->week_day_id
                        && $teacher_lesson->weekly_period_id == $request->weekly_period_id
                        && $teacher_lesson->class_period_id == $request->class_period_id)
                    {
                        unset($replacement_lessons[$group_lesson->teacher->id]);
                        break;
                    }

                    if ($teacher_lesson->group_id == $request->group_id) {
                        foreach ($seeking_teacher->lessons as $seeking_teacher_lesson) {
    echo '<pre>';
    print_r(['seeking_teacher_lesson' => ['lesson_id' => $seeking_teacher_lesson->id, 'group_id' => $seeking_teacher_lesson->group_id, 'teacher_id' => $seeking_teacher_lesson->teacher_id, 'week_day_id' => $seeking_teacher_lesson->week_day_id, 'class_period_id' => $seeking_teacher_lesson->class_period_id]]);
    echo '</pre>';                        
                            if ($teacher_lesson->week_day_id != $seeking_teacher_lesson->week_day_id
                                || $teacher_lesson->weekly_period_id != $seeking_teacher_lesson->weekly_period_id
                                || $teacher_lesson->class_period_id != $seeking_teacher_lesson->class_period_id)
                            {
                                $replacement_lessons[$group_lesson->teacher->id][] = [
                                    'desired_lesson_id' => $teacher_lesson->id,
                                    'seeking_lesson_id' => $seeking_teacher_lesson->id,
                                ];
    echo '<pre>';
    print_r(['replacement_lessons_1' => ['desired_lesson_id' => $teacher_lesson->id, 'desired_teacher_id' => $group_lesson->teacher->id, 'seeking_lesson_id' => $seeking_teacher_lesson->id]]);
    echo '</pre>';
                            }
                        }
                    }
                }
                $lesson_teachers[] = $group_lesson->teacher->id;
            }
            if (count($replacement_lessons) > 0) {
                dd(['replacement_lessons_2' => $replacement_lessons]);
            }
        }

        dd(['replacement_lessons_3' => $replacement_lessons]);
                                        
    }
}
