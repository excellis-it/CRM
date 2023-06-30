<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;


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
    Route::group(['middleware' => 'auth:api'], function () {
        Route::group(['prefix' => 'roles'], function () {
            Route::post('list',[RoleController::class,'roleList']); // listing api
            Route::post('create',[RoleController::class,'roleCreate']); // create api
            Route::post('edit',[RoleController::class,'roleEdit']); // edit api
            Route::post('update',[RoleController::class,'roleUpdate']); // update api
            Route::post('delete',[RoleController::class,'roleDelete']); // delete api
            Route::post('assign-permission',[RoleController::class,'assignPermission']); // assign permission api
        });

        Route::group(['prefix' => 'permissions'], function () {
            Route::post('list',[PermissionController::class,'permissionList']); // listing api
            Route::post('list-by-role',[PermissionController::class,'permissionsByRole']); // listing by role api
            Route::post('create',[PermissionController::class,'permissionCreate']); // create api
            Route::post('edit',[PermissionController::class,'permissionEdit']); // edit api
            Route::post('update',[PermissionController::class,'permissionUpdate']); // update api
            Route::post('delete',[PermissionController::class,'permissionDelete']); // delete api
        });

        Route::group(['prefix' => 'users'], function () {
            Route::post('list', [UserController::class, 'userList']);  // list api
            Route::post('create', [UserController::class, 'userCreate']);  // create api
            Route::post('edit', [UserController::class, 'userEdit']);  // edit api
            Route::post('update', [UserController::class, 'userUpdate']);  // edit api                                
        });

        Route::group(['prefix' => 'projects'], function () {
            Route::post('list',[ProjectController::class,'projectList']); // listing api
            Route::post('create',[ProjectController::class,'projectCreate']); // create api
            Route::post('edit',[ProjectController::class,'projectEdit']); // edit api
            Route::post('assign-project',[ProjectController::class,'assignProject']); // assign project api
            Route::post('update',[ProjectController::class,'projectUpdate']); // update api
            // Route::post('delete',[PermissionController::class,'permissionDelete']); // delete api
        });

        Route::group(['prefix' => 'tasks'], function () {
            Route::post('list-by-project',[TaskController::class,'taskListByProject']); // listing api
            Route::post('create',[TaskController::class,'taskCreate']); // create api
            Route::post('edit',[TaskController::class,'taskEdit']); // edit api
            Route::post('assign-task',[TaskController::class,'assignTask']); // assign project api
            Route::post('update',[TaskController::class,'taskUpdate']); // update api
            Route::post('delete',[TaskController::class,'taskDelete']); // delete api
        });
    });    
});
