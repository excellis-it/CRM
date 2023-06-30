<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
/**
 * @group Permission APIs
 *
 * APIs for Permissions
 */
class PermissionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:Permission list', ['only' => ['permissionList']]);
        $this->middleware('permission:Permission list-by-role', ['only' => ['permissionsByRole']]);
        $this->middleware('permission:Permission create', ['only' => ['permissionCreate']]);
        $this->middleware('permission:Permission edit', ['only' => ['permissionEdit','permissionUpdate']]);
        $this->middleware('permission:Permission delete', ['only' => ['permissionDelete']]);
        $this->middleware('permission:Permission assign', ['only' => ['assignPermission']]);
    }
   
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
     * @response 401 {
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
            $permission = new Permission;
            $permission->name = $request->input('name');
            $permission->guard_name = 'web';
            $permission->save();

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully'
            ]);
            
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
    *    "message": "Something went wrong"
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
            $permission->name = $request->input('name');
            $permission->guard_name = 'web';
            $permission->save();

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    /*
    *    @Permission delete
    *    @bodyParam permission_id int required The id of the permission. Example: 1
    *    @response 200 {
    *    "success": true,
    *    "message": "Permission deleted successfully"
    *    }
    *    @ response 401 {
    *    "success": false,
    *    "message": "Something went wrong"
    *    }
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
            $permission->delete();
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

}
