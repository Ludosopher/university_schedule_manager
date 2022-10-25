<?php

namespace App\Helpers;

use App\Group;
use App\Lesson;
use App\Teacher;
use Illuminate\Support\Facades\Schema;

class LessonHelpers
{
    public static function getLessonsForReplacement ($data)
    {
        $weekly_period_ids = config('enum.weekly_period_ids');
        $replacement_lessons = [];

        $replacing_lesson = Lesson::where('id', $data['lesson_id'])->first();
        $seeking_teacher = Teacher::where('id', $data['teacher_id'])->with(['lessons'])->first();
        $group = Group::where('id', $data['group_id'])->with(['lessons.teacher.lessons'])->first();
        
        $is_suitable_teacher = true;
        $is_suitable_lesson = true;
        $looked_teachers = [$seeking_teacher->id];

        foreach ($group->lessons as $g_lesson) {
            if (!in_array($g_lesson->teacher->id, $looked_teachers)) {
                foreach ($g_lesson->teacher->lessons as $dt_lesson) {
                    if ($dt_lesson->week_day_id == $data['week_day_id']
                        && ($dt_lesson->weekly_period_id == $data['weekly_period_id'] || $dt_lesson->weekly_period_id == $weekly_period_ids['every_week'])
                        && $dt_lesson->class_period_id == $data['class_period_id'])
                    {
                        $is_suitable_teacher = false;
                        break;
                    }
                }
                $looked_teachers[] = $g_lesson->teacher->id;
                if ($is_suitable_teacher) {
                    foreach ($g_lesson->teacher->lessons as $dt_lesson) {
                        if ($dt_lesson->group_id == $data['group_id']) {
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
                                $replacement_lessons[] = [
                                    'subject' => $dt_lesson->name,
                                    'week_day_id' => ['id' => $dt_lesson->week_day->id, 'name' => $dt_lesson->week_day->name],
                                    'weekly_period_id' => ['id' => $dt_lesson->weekly_period->id, 'name' => $dt_lesson->weekly_period->name],
                                    'class_period_id' => ['id' => $dt_lesson->class_period->id, 'name' => $dt_lesson->class_period->name],
                                    'teacher_id' => $g_lesson->teacher->id,
                                    'profession_level_name' => $g_lesson->teacher->profession_level_name,
                                    'phone' => $g_lesson->teacher->phone,
                                    'age' => $g_lesson->teacher->age,
                                    'department_id' => ['id' => $g_lesson->teacher->department->id, 'name' => $g_lesson->teacher->department->name],
                                    'position_id' => ['id' => $g_lesson->teacher->position->id, 'name' => $g_lesson->teacher->position->name],
                                    'schedule_position_id' => self::getLessonSchedulePosition($replacing_lesson, $g_lesson->teacher)
                                ];
                            }
                            $is_suitable_lesson = true;
                        }
                    }
                }
                $is_suitable_teacher = true;
            }
        }

        return $replacement_lessons;
    }

    public static function getLessonSchedulePosition ($lesson, $teacher) {
              
        $is_prev_lesson = false;
        $is_next_lesson = false;

        foreach ($teacher->lessons as $teacher_lesson) {
            if ($teacher_lesson->week_day_id == $lesson->week_day_id) {
                if ($teacher_lesson->class_period_id == $lesson->class_period_id + 1) {
                    $is_prev_lesson = true;
                } elseif ($teacher_lesson->class_period_id == $lesson->class_period_id - 1) {
                    $is_next_lesson = true;
                }
            }
        }

        if ($is_prev_lesson && $is_next_lesson) {
            $result = ['id' => 1, 'name' => 'Между двумя имеющимися парами'];
        } elseif ($is_prev_lesson || $is_next_lesson) {
            $result = ['id' => 2, 'name' => 'Рядом с одной из имеющихся пар'];
        } else {
            $result = ['id' => 3, 'name' => 'Нет рядом имеющихся пар']; 
        }
       
        return $result;
    }

    // public static function addOrUpdateInstance($data, $model) {

    //     if (isset($data['updating_id'])) {
    //         $instance = $model::where('id', $data['updating_id'])->first(); 
    //     } else {
    //         $instance = new $model();
    //     }

    //     $db_fields = Schema::getColumnListing($instance->getTable());

    //     foreach ($db_fields as $field) {
    //         if (isset($data[$field]) && $instance->$field != $data[$field]) {
    //             $instance->$field = $data[$field];
    //         }
    //     }
        
    //     $instance->save();

    //     return $instance;
    // }

    // public static function deleteInstance($id, $model) {
    //     $deleting_instance = $model::where('id', $id)->first();
    //     if ($deleting_instance) {
    //         $model::where('id', $id)->delete();
    //         return $deleting_instance;
    //     }

    //     return false;
    // }
}