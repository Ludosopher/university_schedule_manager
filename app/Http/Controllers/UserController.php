<?php

namespace App\Http\Controllers;

use App\ClassPeriod;
use App\Group;
use App\Helpers\DocExportHelpers;
use App\Helpers\FilterHelpers;
use App\Helpers\LessonHelpers;
use App\Helpers\ModelHelpers;
use App\Helpers\TeacherHelpers;
use App\Helpers\UniversalHelpers;
use App\Helpers\ValidationHelpers;
use App\Http\Requests\teacher\ExportScheduleToDocTeacherRequest;
use App\Http\Requests\teacher\FilterTeacherRequest;
use App\Http\Requests\teacher\RescheduleTeacherRequest;
use App\Http\Requests\teacher\ScheduleTeacherRequest;
use App\Http\Requests\teacher\StoreTeacherRequest;
use App\Http\Requests\user\FilterUserRequest;
use App\Http\Requests\user\StoreUserRequest;
use App\Lesson;
use App\Teacher;
use App\WeekDay;
use App\WeeklyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public $config = [
        'model_name' => 'App\User',
        'instance_name' => 'user',
        'instance_plural_name' => 'users',
        'instance_name_field' => 'name',
        'profession_level_name_field' => null,
        'eager_loading_fields' => ['teachers', 'groups'],
        'other_lesson_participant' => null,
        'other_lesson_participant_name' => null,
        'boolean_attridutes' => ['is_moderator', 'is_admin'],
    ];

    public function getUsers (FilterUserRequest $request)
    {
        $request->validated();
        $data = ModelHelpers::getInstances(request()->all(), $this->config);

        return view("user.users")->with('data', $data);
    }

    public function addUserForm (Request $request)
    {
        $data = ModelHelpers::getInstanceFormData($request->all(), $this->config);
        
        if (isset($data['updating_instance'])) {
            $attributes = [
                'teacher_id' => 'teachers', 
                'group_id' => 'groups'
            ];
            $data = ModelHelpers::getManyToManyData($data, $attributes);
        }

        return view("user.add_user_form")->with('data', $data);
    }

    public function updateUser (StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated = UniversalHelpers::preparingBooleans($validated, $this->config['boolean_attridutes']);
        $user = ModelHelpers::addOrUpdateInstance($validated, $this->config);
        $attributes = [
            'teacher_id' => 'teachers', 
            'group_id' => 'groups'
        ];
        ModelHelpers::addOrUpdateManyToManyAttributes($validated, $user['id'], $this->config['model_name'], $attributes);

        return redirect()->route("users", ['updated_instance_name' => $user['updated_instance_name']]);
    }

    public function deleteUser (Request $request)
    {
        $attributes = ['teachers', 'groups'];
        $relations_deleted_result = ModelHelpers::deleteManyToManyAttributes($request->deleting_id, $this->config['model_name'], $attributes);
        if (!$relations_deleted_result) {
            return redirect()->route("users", ['deleting_instance_not_found' => true]);
        }
        $deleted_instance = ModelHelpers::deleteInstance($request->deleting_id, $this->config['model_name']);
        $instance_name_field = $this->config['instance_name_field'];
        return redirect()->route("users", ['deleted_instance_name' => $deleted_instance->$instance_name_field]);
    }

}
