<?php

use App\Http\Controllers\Api\V1\Queries\ImportQueryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Import Query Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:user', 'lang'])->group(function () {
    Route::get('/import-queries', [ImportQueryController::class, 'index']);
    Route::post('/import-queries', [ImportQueryController::class, 'store']);
    Route::get('/import-queries/{importQuery}', [ImportQueryController::class, 'show']);
});
