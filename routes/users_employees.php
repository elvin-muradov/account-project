<?php

use App\Http\Controllers\Api\V1\Employee\EmployeeController;
use App\Http\Controllers\Api\V1\Users\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Users Employees Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth:user'])->group(function () {
    // Users Routes
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users/{user}', [UserController::class, 'update']);
    Route::patch('/users/{user}/account-status', [UserController::class, 'changeStatusOfUser']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    // Employees Routes
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/{employee}', [EmployeeController::class, 'show']);
    Route::post('/employees/{employee}', [EmployeeController::class, 'update']);
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy']);
});

