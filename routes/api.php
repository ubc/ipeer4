<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CourseRoleController;
use App\Http\Controllers\Api\CourseUserController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::namespace('App\Http\Controllers\Api')->group(function() {
    Route::get('/version', 'GuestActionsController@getVersion');
    Route::post('/register', 'GuestActionsController@registerUser');
    Route::post('/login', 'LoginController@authenticate');
    Route::post('/logout', 'LoginController@logout');
    // login required routes
    Route::middleware('auth:sanctum')->group(function() {
        Route::apiResource('course', CourseController::class);
        Route::apiResource('course.assignment', AssignmentController::class)
             ->scoped();
        Route::apiResource('course.role', CourseRoleController::class)
             ->scoped();
        Route::apiResource('course.user', CourseUserController::class)
             ->scoped();
        Route::apiResource('user', UserController::class);
    });
});
