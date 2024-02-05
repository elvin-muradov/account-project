<?php

use App\Http\Controllers\Api\V1\Companies\ActivityCodeController;
use App\Http\Controllers\Api\V1\Companies\CompanyController;
use App\Http\Controllers\Api\V1\Companies\MainDocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Companies Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth:user', 'lang'])->group(function () {
    //Company Routes
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::post('/companies/{company}', [CompanyController::class, 'update']);
    Route::get('/companies/{company}', [CompanyController::class, 'show']);
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);
    Route::get('/companies/{company}/main-documents', [MainDocumentController::class, 'companyMainDocuments']);

    //Company Activity Codes Routes
    Route::get('/activity-codes', [ActivityCodeController::class, 'index']);
    Route::post('/activity-codes', [ActivityCodeController::class, 'store']);
    Route::get('/activity-codes/{activityCode}', [ActivityCodeController::class, 'show']);
    Route::post('/activity-codes/{activityCode}', [ActivityCodeController::class, 'update']);
    Route::delete('/activity-codes/{activityCode}', [ActivityCodeController::class, 'destroy']);

    //Company Activity Codes Routes
    Route::get('/company-activity-codes', [ActivityCodeController::class, 'indexForCompany']);
    Route::get('/company-activity-codes/{activityCode}', [ActivityCodeController::class, 'showForCompany']);
});

