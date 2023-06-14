<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    //
    public function storeRole(Request $request)
    {
        
        return response()->json([
            'success' => true,
            'message' => 'Role created successfully'
        ]);
    }
}
