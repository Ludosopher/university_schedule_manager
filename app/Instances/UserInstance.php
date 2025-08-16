<?php

namespace App\Instances;

use App\Instances\Instance;
use App\User;

class UserInstance extends Instance
{
    protected $config = [
        'model_name' => 'App\User',
        'instance_name' => 'user',
        'instance_plural_name' => 'users',
        'instance_name_field' => 'name',
        'profession_level_name_field' => null,
        'eager_loading_fields' => ['teachers', 'groups'],
        'other_lesson_participant' => null,
        'other_lesson_participant_name' => null,
        'boolean_attributes' => ['is_moderator', 'is_admin'],
        'many_to_many_attributes' => [
            'teacher_id' => 'teachers', 
            'group_id' => 'groups'
        ],
    ];

    public function getAccountMain(User $user) {

        $teacher_names = [];
        foreach ($user->teachers as $teacher) {
            $teacher_names[] = $teacher->profession_level_name;
        }
        $group_names = [];
        foreach ($user->groups as $group) {
            $group_names[] = $group->name;
        }
        
        return [
            'id' => $user->id,
            'phone' => $user->phone,
            'email' => $user->email,
            'level' => $user->is_admin ? __('content.administrator') : ($user->is_moderator ? __('content.moderator') : __('content.ordinary_user')),
            'teacher_names' => $teacher_names,
            'group_names' => $group_names,
        ];
    }
    
}