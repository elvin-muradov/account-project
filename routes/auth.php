<?php

use App\Http\Controllers\Api\V1\Employee\EmployeeAuthController;
use App\Http\Controllers\Api\V1\Users\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Login/Register Routes
|--------------------------------------------------------------------------
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/generate-otp', [AuthController::class, 'sendOTP']);

Route::post('/login-employee', [EmployeeAuthController::class, 'login']);

Route::middleware(['auth:employee'])->group(function () {
    Route::post('/logout-employee', [EmployeeAuthController::class, 'logout']);
});

Route::middleware(['auth:user'])->group(function () {
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

