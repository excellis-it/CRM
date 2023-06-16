<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
/**
 *  @user list
 */
class UserController extends Controller
{
    
    /* 
    *  @user list
    *  @response 200 {
    * "data": [
    * {
    * "id": 1,
    * "name": "create user",
    * "guard_name": "web",
    * "created_at": "2021-01-01T00:00:00.000000Z",
    * "updated_at": "2021-01-01T00:00:00.000000Z"
    * },
    * },
    */
    public function list()
    {
        $user = User::orderBy('id','desc')->get();
        foreach ($user as $key => $value) {
            $user[$key]['role'] = $value->roles()->pluck('name');
        }
        $data['details'] = $user;
        
        try {
            $count = $user->count();
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

    public function create()
    {
         
    }
}
