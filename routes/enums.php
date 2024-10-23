<?php

use App\Http\Controllers\Api\V1\EnumsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Enums(Categories etc. as array with label,value) Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth:user', 'lang'])->group(function () {
    Route::get('/company-categories', [EnumsController::class, 'companyCategories']);
    Route::get('/transport-types', [EnumsController::class, 'transportTypes']);
    Route::get('/education-types', [EnumsController::class, 'educationTypes']);
});

