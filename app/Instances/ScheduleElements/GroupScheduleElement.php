<?php

namespace App\Instances\ScheduleElements;

use App\Group;

class GroupScheduleElement extends ScheduleElement
{
    protected $config = [
        'model_name' => 'App\Group',
        'instance_name' => 'group',
        'instance_plural_name' => 'groups',
        'instance_name_field' => 'name',
        'profession_level_name_field' => null,
        'eager_loading_fields' => ['faculty', 'study_program', 'study_orientation', 'study_degree', 'study_form', 'course'],
        'other_lesson_participant' => 'teacher',
        'other_lesson_participant_name' => ['teacher', 'profession_level_name'],
        'boolean_attributes' => [],
        'many_to_many_attributes' => [],
    ];

    public function deleteGroupLessonRelation($id) {
        
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