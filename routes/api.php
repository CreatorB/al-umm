<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('signin', [AuthController::class, 'signin']);
    Route::post('signup', [AuthController::class, 'signup']);
    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::post('signout', [AuthController::class, 'signout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh-token', [AuthController::class, 'refreshToken']);
        Route::apiResource('users', UserController::class);
        Route::middleware('check.local')->group(function () {
            Route::post('attendance/check-in', [AttendanceController::class, 'checkIn']);
            Route::post('attendance/check-out', [AttendanceController::class, 'checkOut']);
        });
        // Route::post('attendance/check-in', [AttendanceController::class, 'checkIn']);
        // Route::post('attendance/check-out', [AttendanceController::class, 'checkOut']);
        Route::get('attendance/status', [AttendanceController::class, 'status']);
        Route::get('announcements/active', [AnnouncementController::class, 'getActive']);
    });
});