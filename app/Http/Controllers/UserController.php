<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelpers;
use App\Http\Requests\user\DeleteUserRequest;
use App\Http\Requests\user\AdminStoreUserRequest;
use App\Http\Requests\user\FilterUserRequest;
use App\Http\Requests\user\SelfStoreUserRequest;
use App\Instances\UserInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function getUsers (FilterUserRequest $request)
    {
        $request->validated();
        $data = (new UserInstance())->getInstances(request()->all());

        return view("user.users")->with('data', $data);
    }

    public function addUserForm (Request $request)
    {
        $data = (new UserInstance())->getInstanceFormData($request->all());
        
        if (isset($data['updating_instance'])) {
            $data = (new UserInstance())->getManyToManyData($data);
        }

        return view("user.add_user_form")->with('data', $data);
    }

    public function adminUpdateUser (AdminStoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated = (new UserInstance())->preparingBooleans($validated);
        
        $user = (new UserInstance())->addOrUpdateInstance($validated);
        (new UserInstance())->addOrUpdateManyToManyAttributes($validated, $user['id']);

        $response_content = ResponseHelpers::getContent($user, 'user');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function selfUpdateUser (SelfStoreUserRequest $request)
    {
        $validated = $request->validated();
        
        $user = (new UserInstance())->addOrUpdateInstance($validated);
        
        $response_content = ResponseHelpers::getContent($user, 'user');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function deleteUser (DeleteUserRequest $request)
    {
        (new UserInstance())->deleteManyToManyAttributes($request->validated()['deleting_id']);
        
        $deleted_instance = (new UserInstance())->deleteInstance($request->validated()['deleting_id']);
        $response_content = ResponseHelpers::getContent($deleted_instance, 'user');
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    public function getAccountMain (Request $request)
    {
        $data = (new UserInstance())->getAccountMain(Auth::user());

        return view("user.account_main")->with('data', $data);
    }

    public function setLocate (Request $request)
    {
        if (in_array($request->lang, config('enum.languages'))) {
            Session::put('applocale', $request->lang);
        }

        if (isset($request->prev_replace_rules)) {
            return redirect()->route("lesson-replacement", ResponseHelpers::getLessonReplacementBackData($request->all()));
        }
        
        if (isset($request->previous_route)) {
            return redirect()->route($request->previous_route, $request->all());
        }
     
        return redirect()->back();
    }

}
