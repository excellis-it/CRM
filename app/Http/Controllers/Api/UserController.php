<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Mail\UserRegistrationMail;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Mail;

/**
 * @group Users APIs
 *
 * APIs for Users management
 */
class UserController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:User list', ['only' => ['userList']]);
        $this->middleware('permission:User create', ['only' => ['userCreate']]);
        $this->middleware('permission:User edit', ['only' => ['userEdit']]);
        $this->middleware('permission:User by-role', ['only' => ['usersByRole']]);
        
    }
    
    /* 
    *  @user list
    *  @response 200 {
    *   "success": true,
    *   "message": "User list find successfully"
    *   "data": [
        *  {
            "id": 1,
            "name": "ADMIN",
            "guard_name": "web",
            "users": [
                {
                    "id": 1,
                    "name": "Super Admin",
                    "email": "admin@yopmail.com",
                    "pivot": {
                        "role_id": 1,
                        "model_id": 1,
                        "model_type": "App\\Models\\User"
                    }
                }
            ]
        },
        {
            "id": 2,
            "name": "MANAGER",
            "guard_name": "web",
            "users": [
                {
                    "id": 2,
                    "name": "Swarna Manager",
                    "email": "swarna@gmail.ocm",
                    "pivot": {
                        "role_id": 2,
                        "model_id": 2,
                        "model_type": "App\\Models\\User"
                    }
                },
                {
                    "id": 4,
                    "name": "Swarna Manager",
                    "email": "sri@gmail.ocm",
                    "pivot": {
                        "role_id": 2,
                        "model_id": 4,
                        "model_type": "App\\Models\\User"
                    }
                }
            ]
        },
    * },
    */
    public function userList()
    {
        $users = Role::select('id','name','guard_name')->with('users:users.id,name,email')->get();
        $data['details'] = $users;
        
        try {
            $count = $users->count();
            if ($count > 0) {
                return response()->json([
                    'data' => $data,
                    'success' => true,
                    'message' => 'User list find successfully'
                ]);
            } else {
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => 'User list empty'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'data' => [],
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    /*
    *  @user create
    *  @response 200 {
    *    "name": "Swarna Manager",
    *    "email": "sri@gmail.ocm",
    *    "status": 1,
    *    "updated_at": "2023-06-20T10:44:03.000000Z",
    *    "created_at": "2023-06-20T10:44:03.000000Z",
    *    "id": 4,
    *    "roles": [
    *           {
    *               "id": 2,
    *               "name": "MANAGER",
    *               "guard_name": "web",
    *               "created_at": "2023-06-16T04:32:56.000000Z",
    *               "updated_at": "2023-06-16T04:32:56.000000Z",
    *               "pivot": {
    *                   "model_id": 4,
    *                   "role_id": 2,
    *                   "model_type": "App\\Models\\User"
    *               }
    *           }
    *       ]
    *    },
    *    "status": true,
    *    "statusCode": 200,
    *    "message": "User created successfully"
    * },
    */

    public function userCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'password' => 'required|min:8',
            'user_type' => 'required|exists:roles,name',
            'designation' => 'required'
        ]);

        if ($validator->fails()) {
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                // return $data[$i];
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['status' => false, 'statusCode' => 401,'error' => $errors], 401);
        }

        try {
            $check_user = User::where('email', $request->email)->first();
            if ($check_user) {
                return response()->json(['status' => false, 'statusCode' => 401,'error' => 'Email already exists'], 401);
            }
            $user = new User();
            $user->name = $request->name;
            $user->email    = $request->email;
            $user->password = bcrypt($request->password);
            $user->status = 1;
            $user->save();
            $user->assignRole($request->user_type);

            $maildata = [
                'user' => $user,
            ];
            Mail::to($user->email)->send(new UserRegistrationMail($maildata));

            return response()->json(['data' => $user ,'status' => true, 'statusCode' => 200, 'message' => 'User created successfully'], 200);
        }catch (\Throwable $th) {
            return response()->json(['status' => false, 'statusCode' => 401, 'error' => $th->getMessage()], 401);
        }
    }

    /*
    *  @user edit
    *  @response 200 {
    *    "data": {
    *        "id": 2,
    *        "name": "Swarna Manager",
    *        "email": "swarna@gmail.com",
    *        "email_verified_at": null,
    *        "status": 1,
    *        "created_at": "2023-06-23T08:57:28.000000Z",
    *        "updated_at": "2023-06-23T08:57:28.000000Z"
    *    },
    *    "status": true,
    *    "statusCode": 200,
    *    "message": "User details found successfully"
    * },
    */

    public function userEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|exists:roles,name',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                // return $data[$i];
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['status' => false, 'statusCode' => 401,'error' => $errors], 401);
        }
        try {
            $user_details = User::where('id', $request->user_id)->role($request->user_type)->first();
            if ($user_details) {
                return response()->json(['data' => $user_details ,'status' => true, 'statusCode' => 200, 'message' => 'User details found successfully'], 200);
            }else{
                return response()->json(['status' => false, 'statusCode' => 401, 'message' => 'User not found'], 401);
            } 
        }catch (\Throwable $th) {
            return response()->json(['status' => false, 'statusCode' => 401, 'error' => $th->getMessage()], 401);
        }
    }

    /*
    *  @user list by role
    *  @response 200 {
    *    "data": [
    *            {
    *    "id": 3,
    *    "name": "Swarna Designer",
    *    "email": "Swarna@yopmail.com",
    *    "email_verified_at": null,
    *    "status": 1,
    *    "created_at": "2023-07-10T10:18:37.000000Z",
    *    "updated_at": "2023-07-10T10:18:37.000000Z"
    *    }
    *    ],
    *    "status": true,
    *    "statusCode": 200,
    *    "message": "Users found successfully"
    * },
    *    @response 401 {
    *    "status": false,
    *    "statusCode": 401,
    *    "message": "Users not found"
    * },
    *  
    */          

    public function usersByRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }
        try {
            $users = User::role($request->user_type)->get();
            if ($users) {
                return response()->json(['data' => $users ,'status' => true, 'statusCode' => 200, 'message' => 'Users found successfully'], 200);
            }else{
                return response()->json(['status' => false, 'statusCode' => 401, 'message' => 'Users not found'], 401);
            }
        }catch (\Throwable $th) {
            return response()->json(['status' => false, 'statusCode' => 401, 'error' => $th->getMessage()], 401);
        }    
    }

}
