<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
/**
 *  @group Permission management
 */
class PermissionController extends Controller
{
   
    /**
     * Permission list
     * @response 200 {
     * "data": [
     * {
     * "id": 1,
     * "name": "create user",
     * "guard_name": "web",
     * "created_at": "2021-01-01T00:00:00.000000Z",
     * "updated_at": "2021-01-01T00:00:00.000000Z"
     * },
     * },
     * "success": true,
     * "message": "Permission list successfully"
     * }
     * @response 200 {
     * "data": [],
     * "success": false,
     * "message": "Permission list empty"
     * }
     * 
     */

    public function permissionList()
    {
        $permission = Permission::latest()->get();
        try {
            if(Auth::user()->hasPermissionTo('Permission list')){
                $count = $permission->count();
                if ($count > 0) {
                    return response()->json([
                        'data' => $permission,
                        'success' => true,
                        'message' => 'Permission list found successfully'
                    ]);
                } else {
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'message' => 'Permission list empty'
                    ]);
                }

            }else{
                return response()->json([
                    'statusCode' => 401,
                    'success' => false,
                    'message' => 'Permission denied'
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

    /**
     * Permission create
     * @bodyParam name string required The name of the permission. Example: create permission
     * @response 200 {
     * "success": true,
     * "message": "Permission created successfully"
     * }
     * @response 200 {
     * "success": false,
     * "message": "Something went wrong"
     * }
     * 
     */

    public function permissionCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }

        try {
            if(Auth::user()->hasPermissionTo('Permission create')){   
                $permission = new Permission;
                $permission->name = $request->input('name');
                $permission->guard_name = 'web';
                $permission->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Permission created successfully'
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied'
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
    *    @Permission by role
    *    @bodyParam role_id int required The id of the role. Example: 1
    *    @response 200 {
    *    "data": [
    *    {
    *    "id": 1,
    *    "name": "create user",
    *    "guard_name": "web",
    *    "created_at": "2021-01-01T00:00:00.000000Z",
    *    "updated_at": "2021-01-01T00:00:00.000000Z"
    *    },
    *    },
    *    "success": true,
    *    "message": "Permission list successfully"
    *    }
    *    @ response 401 {
    *    "data": [],
    *    "success": false,
    *    "message": "Permission list empty"
    *    }
    */

    public function permissionsByRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|numeric|exists:roles,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }
        $role = Role::select('id','name')->with('permissions:id,name')->where('id',$request->role_id)->get();
        try {
            if(Auth::user()->hasPermissionTo('Permission list-by-role')){  
                $count = $role->count();
                if ($count > 0) {
                    return response()->json([
                        'data' => $role,
                        'success' => true,
                        'message' => 'Permission list found successfully'
                    ]);
                } else {
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'message' => 'Permission list empty'
                    ]);
                }
            }else{
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => 'Permission denied'
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
    *    @Permission edit
    *    @bodyParam permission_id int required The id of the permission. Example: 1
    *    @response 200 {
    *    "data": {
    *    "id": 1,
    *    "name": "create user",
    *    "guard_name": "web",
    *    "created_at": "2021-01-01T00:00:00.000000Z",
    *    "updated_at": "2021-01-01T00:00:00.000000Z"
    *    },
    *    "success": true,
    *    "message": "Permission found successfully"
    *    }
    *    @ response 401 {
    *    "data": [],
    *    "success": false,
    *    "message": "Permission not found"
    *    }
    */

    public function permissionEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required|numeric|exists:permissions,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }
        $permission = Permission::findOrFail($request->permission_id);
        try {
            if(Auth::user()->hasPermissionTo('Permission edit')){   
                $count = $permission->count();
                if ($count > 0) {
                    return response()->json([
                        'data' => $permission,
                        'success' => true,
                        'message' => 'Permission found successfully'
                    ]);
                } else {
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'message' => 'Permission not found'
                    ]);
                }
            }else{
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => 'Permission denied'
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
    *    @Permission update
    *    @bodyParam permission_id int required The id of the permission. Example: 1
    *    @bodyParam name string required The name of the permission. Example: create user
    *    @response 200 {
    *    "success": true,
    *    "message": "Permission updated successfully"
    *    }
    *    @ response 401 {
    *    "success": false,
    *    "message": "Permission not found"
    *    }
    */

    public function permissionUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required|numeric|exists:permissions,id',
            'name' => 'required|unique:permissions,name,'.$request->permission_id,
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }
        $permission = Permission::findOrFail($request->permission_id);
        try {
            if(Auth::user()->hasPermissionTo('Permission update')){ 
                $permission->name = $request->input('name');
                $permission->guard_name = 'web';
                $permission->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Permission updated successfully'
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied'
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

    public function permissionDelete()
    {
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required|numeric|exists:permissions,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'statusCode' => 401, 'message' => $validator->errors()->first()], 401);
        }
        $permission = Permission::findOrFail($request->permission_id);
        try {
            if(Auth::user()->hasPermissionTo('Permission delete')){ 
                $permission->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Permission deleted successfully'
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied'
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
