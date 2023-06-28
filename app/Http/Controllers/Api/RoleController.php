<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:Role list', ['only' => ['roleList']]);
        //  $this->middleware('permission:Role create', ['only' => ['roleCreate']]);
        //  $this->middleware('permission:Role edit', ['only' => ['roleEdit','roleUpdate']]);
        //  $this->middleware('permission:Role delete', ['only' => ['roleDelete']]);
        //  $this->middleware('permission:Permission assign', ['only' => ['assignPermission']]);
    }

    /* 
    *  @role list
    *  @response 200 {
    *   "success": true,
    *   "message": "Role list find successfully"
    *   "data": [
    *    {
    *        "id": 1,
    *        "name": "ADMIN",
    *        "guard_name": "web",
    *        "created_at": "2023-06-16T04:32:56.000000Z",
    *        "updated_at": "2023-06-16T06:18:00.000000Z"
    *    },
    *    {
    *        "id": 2,
    *        "name": "MANAGER",
    *        "guard_name": "web",
    *        "created_at": "2023-06-16T04:32:56.000000Z",
    *        "updated_at": "2023-06-16T04:32:56.000000Z"
    *    },
    *    {
    *        "id": 3,
    *        "name": "TEAMLEAD",
    *        "guard_name": "web",
    *        "created_at": "2023-06-16T04:32:56.000000Z",
    *        "updated_at": "2023-06-16T04:32:56.000000Z"
    *    }
    *   ]
    *  }
    * @response 401 {
    *   "status": false,
    *   "statusCode": 401,
    *   "message": "Unauthenticated."
    *  }
    */
            

    public function roleList()
    {
        // if(Auth::user()->hasPermissionTo('Role list')){
            $role= Role::latest()->get();
            try {
                $count = $role->count();
                if($count > 0){
                    return response()->json([
                        'data' => $role,
                        'success' => true,
                        'message' => 'Role list successfully'
                    ]);
                }else{
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'message' => 'Role list empty'
                    ]);
                }
            } catch (\Throwable $th) {
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => 'Something went wrong'
                ]);
            }
        // }else{
        //     return response()->json([
        //         'data' => [],
        //         'success' => false,
        //         'message' => 'You have not permission to access'
        //     ]);
        // }
     
        
    }

    /*
    *  @role create
    *  @response 200 {
    *   "data": {
    *        "id": 5,
    *        "guard_name": "web",
    *        "name": "EMPLOYEE",
    *        "updated_at": "2023-06-20T11:01:06.000000Z",
    *        "created_at": "2023-06-20T11:01:06.000000Z",  
    *    },
    *   "success": true,
    *   "message": "Role created successfully"
    *  }
    * @response 401 {
    *   "status": false,
    *   "statusCode": 401,
    *   "message": "The name has already been taken."
    *  }
    */
    public function roleCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }
    
        try {
            if(Auth::user()->hasPermissionTo('Role create')){
                $role = new Role;
                $role->name = $request->input('name');
                $role->guard_name = 'web';
                $role->save();
            
                return response()->json([
                    'data' => $role,
                    'success' => true,
                    'message' => 'Role created successfully'
                ]);
            }else{
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => 'You have not permission to access'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    /*
    *  @role edit
    *  @param role_id
    *  @response 200 {
    *   "data": {
    *        "id": 5,
    *        "guard_name": "web",
    *        "name": "EMPLOYEE",
    *        "updated_at": "2023-06-20T11:01:06.000000Z",
    *        "created_at": "2023-06-20T11:01:06.000000Z",
    *    },
    *   "success": true,
    *   "message": "Role detail find successfully"
    *  }
    * @response 401 {
    *   "status": false,
    *   "statusCode": 401,
    *   "message": "The name has already been taken."
    *  }
    */

    public function roleEdit(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|numeric|exists:roles,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }
        try {
            if(Auth::user()->hasPermissionTo('Role edit')){
                $role = Role::where('id', $request->role_id)->first();
                return response()->json([
                    'data' => $role,
                    'success' => true,
                    'message' => 'Role detail find successfully'
                ]);
            }else{
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => 'You have not permission to access'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }  
    }

    /*
    *  @role update
    *  @param role_id
    *  @response 200 {
    *   "data": {
    *        "id": 5,
    *        "guard_name": "web",
    *        "name": "EMPLOYEE",
    *        "updated_at": "2023-06-20T11:01:06.000000Z",
    *        "created_at": "2023-06-20T11:01:06.000000Z",
    *    },
    *   "success": true,
    *   "message": "Role updated successfully"
    *  }
    * @response 401 {
    *   "status": false,
    *   "statusCode": 401,
    *   "message": "The name has already been taken."
    *  }
    */

    public function roleUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            $errors['status_code'] = 401;
            $errors['message'] = [];
            $data = explode(',', $validator->errors());

            for ($i = 0; $i < count($validator->errors()); $i++) {
                $dk = explode('["', $data[$i]);
                $ck = explode('"]', $dk[1]);
                $errors['message'][$i] = $ck[0];
            }
            return response()->json(['error' => $errors, 'status' => false], 401);
        }
        
        try {
            if(Auth::user()->hasPermissionTo('Role update')){
                $check_role = Role::where('id', $request->role_id)->first();
                $check_role->name = $request->name;
                $check_role->guard_name = 'web';
                $check_role->update();
                
                return response()->json([
                    'data' => $check_role,
                    'success' => true,
                    'message' => 'Role updated successfully'
                ]);
            }else{
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => 'You have not permission to access'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    /*
    *  @role delete
    *  @param role_id
    *  @response 200 {
    *   "data": {
    *        "id": 5,
    *        "guard_name": "web",
    *        "name": "EMPLOYEE",
    *        "updated_at": "2023-06-20T11:01:06.000000Z",
    *        "created_at": "2023-06-20T11:01:06.000000Z",
    *    },
    *   "success": true,
    *   "message": "Role deleted successfully",
    *  }
    * @response 401 {
    *   "status": false,
    *   "statusCode": 401,
    *   "message": "The name has already been taken."
    *  }
    */

    public function roleDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|numeric|exists:roles,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }
        
        try {
            if(Auth::user()->hasPermissionTo('Role update')){
                $role = Role::where('id', $request->role_id)->first();
                $role->delete();
                
                return response()->json([
                    'data' => $role,
                    'success' => true,
                    'message' => 'Role deleted successfully'
                ]);
            }else{
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => 'You have not permission to access'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    /*
    *  @role assign permission
    *  @param role_id, permission_id
    *  @response 200 {
    *   "data": {
    *    "id": 4,
    *    "name": "abc",
    *    "guard_name": "web",
    *    "created_at": "2023-06-23T06:24:53.000000Z",
    *    "updated_at": "2023-06-23T06:24:53.000000Z",
    *    "permissions": [
    *        {
    *            "id": 1,
    *            "name": "Role list",
    *            "guard_name": "web",
    *            "created_at": "2023-06-23T06:23:13.000000Z",
    *            "updated_at": "2023-06-23T06:23:13.000000Z",
    *            "pivot": {
    *                "role_id": 4,
    *                "permission_id": 1
    *            }
    *        },
    *        {
    *            "id": 3,
    *            "name": "Role update",
    *            "guard_name": "web",
    *            "created_at": "2023-06-23T06:23:13.000000Z",
    *            "updated_at": "2023-06-23T06:23:13.000000Z",
    *            "pivot": {
    *                "role_id": 4,
    *                "permission_id": 3
    *            }
    *        },
    *    ]
    *   "success": true,
    *   "message": "Permission assign successfully",
    *  }
    * @response 401 {
    *   "status": false,
    *   "statusCode": 401,
    *   "message": "The name has already been taken."
    *  }
    */

    public function assignPermission(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|numeric|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }

        try {
            if(Auth::user()->hasPermissionTo('Permission assign')){
                $role = Role::where('id', $request->role_id)->first();
                $role->givePermissionTo([
                    $request->permission_id
                ]);
                
                return response()->json([
                    'data' => $role,
                    'success' => true,
                    'message' => 'Permission assign successfully'
                ]);
            }else{
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => 'You have not permission to access'
                ]);
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

}
