<?php

namespace App\Helpers;

use App\User;

class UserHelpers
{
    public static function getAccountMain(User $user) {

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
