<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Okipa\LaravelBootstrapTableList\TableList;

class UserController extends Controller
{
   
    private $userService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
               try {
            $table = $this->userService->getAllUsers();
        } catch (\ErrorException $exception) {
            return redirect('/users')->with('errorMessage',
                __('frontendMessages.EXCEPTION_MESSAGES.SHOW_USERS_LIST'));
        }
        return view('admin.users.usersList', ['table' => $table]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.user');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $result = $this->userService->createUser($request);
        if (!$result) {
            return redirect('/users')->with('errorMessage',
                __('frontendMessages.EXCEPTION_MESSAGES.CREATE_USER_MESSAGE'));
        }
        return redirect('/users')->with('successMessage', __('frontendMessages.SUCCESS_MESSAGES.USER_CREATED'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->userService->getUser($id);
        if ($user == null) {
            return redirect('/users')->with('errorMessage',
                __('frontendMessages.EXCEPTION_MESSAGES.FIND_USER_MESSAGE'));
        }
        return view('admin.users.user', ['user' => $user]);
    }

    
    public function update(UpdateUserRequest $request, $id)
    {
        $result = $this->userService->updateUser($request, $id);
        if ($result == null) {
            return redirect('/users/edit')->with('errorMessage',
                config('frontendMessages.EXCEPTION_MESSAGES.UPDATE_USER_MESSAGE'));
        }
        return redirect('/users')->with('successMessage', __('frontendMessages.SUCCESS_MESSAGES.USER_UPDATED'));
    }

    
    public function destroy($id)
    {
        $result = $this->userService->deleteUser($id);
        if (!$result) {
            return redirect('/users')->with('errorMessage',
                __('frontendMessages.EXCEPTION_MESSAGES.DELETE_USER_MESSAGE'));
        }
        return redirect('/users')->with('successMessage', __('frontendMessages.SUCCESS_MESSAGES.USER_DELETED'));
    }
}
