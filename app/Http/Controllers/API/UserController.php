<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Okipa\LaravelBootstrapTableList\TableList;
use Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Http\Controllers\Controller;
use App\User; 
use Validator;
use App\Models\Passport\Token;

class UserController extends Controller
{
   
    private $userService;
    public $successStatus = 200;
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
        
         $user = User::paginate(5);
        
         return response()->json(['success'=>$user]);    
        
    }

    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    } 

    public function getSearchResults(Request $request) {
         
        
        $data = $request->get('search');

        $search_users = User::where('id', 'like', "%{$data}%")
                         ->orWhere('username', 'like', "%{$data}%")
                         ->orWhere('first_name', 'like', "%{$data}%")
                         ->orWhere('last_name', 'like', "%{$data}%")
                         ->orWhere('city', 'like', "%{$data}%")
                         ->orWhere('house_number', 'like', "%{$data}%")
                         ->get();

        return response()->json([
            'data' => $search_users
        ]);     
    }

    public function sortUsers(Request $request)
    {
        $order = $request->get('order');
        $column = $request->get('column');
        
        $sorted_users = User::orderBy($column,$order)->get();

        return response()->json([
            'data' => $sorted_users
        ]); 
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
        $validator = Validator::make($request->all(), [ 
            'username' => 'required', 
            'email' => 'required|email', 
            'first_name' => 'required', 
            'last_name'  => 'required', 
            'city' => 'required', 
            'address' => 'required', 
            'house_number' => 'required', 
            'postal_code' => 'required', 
            'telephone_number' => 'required', 
            'password' => 'required', 
            'password_confirmation' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        $success['username'] =  $user->username;
        return response()->json(['success'=>$success], $this-> successStatus); 
        
        
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
            $success = 'fail';
            $message = config('frontendMessages.EXCEPTION_MESSAGES.UPDATE_USER_MESSAGE');
        }
        else {
            $success = 'success';
            $message = "User updated successfuly";
        }
        return response()->json(['success'=>$success, 'message'=> $message]); 
    }

    public function delete(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message'=>'Deleted Successfuly']); 
    }

    public function home()
    {
        if(!empty(Auth::user()) && Auth::user()!=null) {
        return redirect('/users');
        }
        else
        {
        return view('auth.login');
        }
    }

    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json(['success' => $success], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    public function logout(Request $request)
    {
    $request->user()->token()->revoke();
    return response()->json([
    'message' => 'Successfully logged out',
    ]);
    }
}
