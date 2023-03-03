<?php

namespace App\Http\Controllers;

use App\Helpers\ModelHelpers;
use App\Helpers\DateHelpers;
use App\Helpers\ResponseHelpers;
use App\Helpers\UserHelpers;
use App\Http\Requests\user\DeleteUserRequest;
use App\Http\Requests\user\AdminStoreUserRequest;
use App\Http\Requests\user\FilterUserRequest;
use App\Http\Requests\user\SelfStoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
        'boolean_attributes' => ['is_moderator', 'is_admin'],
        'many_to_many_attributes' => [
            'teacher_id' => 'teachers', 
            'group_id' => 'groups'
        ],
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
            $data = ModelHelpers::getManyToManyData($data, $this->config['many_to_many_attributes']);
        }

        return view("user.add_user_form")->with('data', $data);
    }

    public function adminUpdateUser (AdminStoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated = DateHelpers::preparingBooleans($validated, $this->config['boolean_attributes']);
        
        $user = ModelHelpers::addOrUpdateInstance($validated, $this->config);
        ModelHelpers::addOrUpdateManyToManyAttributes($validated, $user['id'], $this->config['model_name'], $this->config['many_to_many_attributes']);

        $response_content = ResponseHelpers::getContent($user, $this->config['instance_name']);
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function selfUpdateUser (SelfStoreUserRequest $request)
    {
        $validated = $request->validated();
        
        $user = ModelHelpers::addOrUpdateInstance($validated, $this->config);
        
        $response_content = ResponseHelpers::getContent($user, $this->config['instance_name']);
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function deleteUser (DeleteUserRequest $request)
    {
        $attributes = array_values($this->config['many_to_many_attributes']);
        ModelHelpers::deleteManyToManyAttributes($request->validated()['deleting_id'], $this->config['model_name'], $attributes);
        
        $deleted_instance = ModelHelpers::deleteInstance($request->validated()['deleting_id'], $this->config);
        $response_content = ResponseHelpers::getContent($deleted_instance, $this->config['instance_name']);
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function getAccountMain (Request $request)
    {
        $data = UserHelpers::getAccountMain(Auth::user());

        return view("user.account_main")->with('data', $data);
    }

}
