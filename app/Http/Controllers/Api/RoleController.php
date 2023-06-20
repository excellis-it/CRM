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
    //
    function __construct()
    {
         $this->middleware('permission:Role list|Role create|Role edit|Role delete', ['only' => ['roleList']]);
         $this->middleware('permission:Role create', ['only' => ['roleCreate']]);
         $this->middleware('permission:Role edit', ['only' => ['roleEdit','roleUpdate']]);
         $this->middleware('permission:Role delete', ['only' => ['roleDelete']]);
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
            $role = new Role;
            $role->name = $request->input('name');
            $role->guard_name = 'web';
            $role->save();
        
            return response()->json([
                'data' => $role,
                'success' => true,
                'message' => 'Role created successfully'
            ]);
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
            $get_role = Role::where('id', $request->role_id)->first();
            return response()->json([
                'data' => $get_role,
                'success' => true,
                'message' => 'Role detail find successfully'
            ]);
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
            $check_role = Role::where('id', $request->role_id)->first();
            $check_role->name = $request->name;
            $check_role->guard_name = 'web';
            $check_role->update();
            
            return response()->json([
                'success' => true,
                'message' => 'Role update successfully'
            ]);
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
            $role = Role::where('id', $request->role_id)->first();
            $role->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }
}
