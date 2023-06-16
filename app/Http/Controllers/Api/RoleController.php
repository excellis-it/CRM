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
        $this->middleware('auth:api');
    }

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
    public function roleCreate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
        ]);
        
        try {
            $role = new Role;
            $role->name = $request->input('name');
            $role->guard_name = 'web';
            $role->save();
        
            return response()->json([
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

    public function roleEdit(Request $request)
    {
        
        $this->validate($request, [
            'role_id' => 'required|exists:roles,id',
        ]);
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

    public function roleUpdate(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required|exists:roles,id',
            'name' => 'required'
        ]);
        
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

    public function roleDelete(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required|exists:roles,id',
        ]);
        
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
