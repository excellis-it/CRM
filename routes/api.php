<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;

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
    Route::group(['prefix' => 'role','middleware' => 'auth:api'], function () {
        Route::get('list',[RoleController::class,'roleList']); // listing api
        Route::post('create',[RoleController::class,'roleCreate']); // create api
        Route::post('edit',[RoleController::class,'roleEdit']); // edit api
    });
});
