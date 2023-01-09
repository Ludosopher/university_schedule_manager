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
            'phone' => $user->phone,
            'email' => $user->email,
            'level' => $user->is_admin ? 'Администратор' : ($user->is_moderator ? 'Модератор' : 'Обычный пользователь'),
            'teacher_names' => $teacher_names,
            'group_names' => $group_names,
        ];
    }


}
