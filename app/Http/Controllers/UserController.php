<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelpers;
use App\Http\Requests\user\DeleteUserRequest;
use App\Http\Requests\user\AdminStoreUserRequest;
use App\Http\Requests\user\FilterUserRequest;
use App\Http\Requests\user\SelfStoreUserRequest;
use App\Services\UserService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /** @var UserService $userService */
    private $userService;
    
    public function __construct(
        UserService $userService
        )
    {
        $this->userService = $userService;
    }    

    /**
     * Display list of users.
     */
    public function getUsers (FilterUserRequest $request): Renderable
    {
        $request->validated();
        $data = $this->userService->getInstances(request()->all());

        return view("user.users")->with('data', $data);
    }

    /**
     * Display the form for updating a user.
     */
    public function addUserForm (Request $request): Renderable
    {
        $data = $this->userService->getInstanceFormData($request->all());
        
        if (isset($data['updating_instance'])) {
            $data = $this->userService->getManyToManyData($data);
        }

        return view("user.add_user_form")->with('data', $data);
    }

    /**
     * Update user by admin.
     */
    public function adminUpdateUser (AdminStoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated = $this->userService->preparingBooleans($validated);
        
        $user = $this->userService->addOrUpdateInstance($validated);
        $this->userService->addOrUpdateManyToManyAttributes($validated, $user['id']);

        $response_content = ResponseHelpers::getContent($user, 'user');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * User self-update.
     */
    public function selfUpdateUser (SelfStoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        $user = $this->userService->addOrUpdateInstance($validated);
        
        $response_content = ResponseHelpers::getContent($user, 'user');
        
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * Delete user.
     */
    public function deleteUser (DeleteUserRequest $request): RedirectResponse
    {
        $this->userService->deleteManyToManyAttributes($request->validated()['deleting_id']);
        
        $deleted_instance = $this->userService->deleteInstance($request->validated()['deleting_id']);
        $response_content = ResponseHelpers::getContent($deleted_instance, 'user');
        return redirect()->back()->with('response', [
            'success' => $response_content['success'],
            'message' => $response_content['message']
        ]);
    }

    /**
     * Display user account.
     */
    public function getAccountMain (Request $request): Renderable
    {
        $data = $this->userService->getAccountMain(Auth::user());

        return view("user.account_main")->with('data', $data);
    }

    /**
     * Change the language of the application for the user.
     */
    public function setLocate (Request $request): RedirectResponse
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
