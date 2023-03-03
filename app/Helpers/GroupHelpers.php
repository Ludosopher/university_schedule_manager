<?php

namespace App\Helpers;

use App\ClassPeriod;
use App\Group;
use App\Lesson;
use App\Teacher;
use App\WeekDay;
use App\WeeklyPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class GroupHelpers
{
    public static function deleteGroupLessonRelation($id) {
        
        $group = Group::with('lessons.groups')->find($id);
        
        foreach ($group->lessons as $lesson) {
            
            if (count($lesson->groups) == 1) {
                return ['there_are_lessons_only_with_this_group' => true];
            }
        }

        $group->lessons()->detach();
        return true;
        
    }
   
}