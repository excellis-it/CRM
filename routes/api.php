<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);  // login api
    Route::group(['prefix' => 'roles','middleware' => 'auth:api'], function () {
        Route::post('list',[RoleController::class,'roleList']); // listing api
        Route::post('create',[RoleController::class,'roleCreate']); // create api
        Route::post('edit',[RoleController::class,'roleEdit']); // edit api
        Route::post('update',[RoleController::class,'roleUpdate']); // update api
        Route::post('delete',[RoleController::class,'roleDelete']); // delete api
        Route::post('assign-permission',[RoleController::class,'assignPermission']); // assign permission api
    });
    Route::group(['prefix' => 'permissions','middleware' => 'auth:api'], function () {
        Route::post('list',[PermissionController::class,'permissionList']); // listing api
        Route::post('list-by-role',[PermissionController::class,'permissionsByRole']); // listing by role api
        Route::post('create',[PermissionController::class,'permissionCreate']); // create api
        Route::post('edit',[PermissionController::class,'permissionEdit']); // edit api
        Route::post('update',[PermissionController::class,'permissionUpdate']); // update api
        Route::post('delete',[PermissionController::class,'permissionDelete']); // delete api
    });

    Route::group(['prefix' => 'users','middleware' => 'auth:api'], function () {
        Route::get('list', [UserController::class, 'userList']);  // list api
        Route::post('create', [UserController::class, 'userCreate']);  // create api
    });
});
