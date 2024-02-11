<?php

use App\Http\Controllers\Api\V1\Companies\ActivityCodeController;
use App\Http\Controllers\Api\V1\Companies\CompanyController;
use App\Http\Controllers\Api\V1\Companies\MainDocumentController;
use App\Http\Controllers\Api\V1\Companies\MaterialController;
use App\Http\Controllers\Api\V1\Companies\MaterialGroupController;
use App\Http\Controllers\Api\V1\Companies\WarehouseController;
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
    Route::get('/individual-companies', [CompanyController::class, 'individualCompanies']);
    Route::get('/legal-companies', [CompanyController::class, 'legalCompanies']);
    Route::get('/has-not-accountant-companies', [CompanyController::class, 'hasNotAccountantCompanies']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::post('/companies/{company}', [CompanyController::class, 'update']);
    Route::get('/companies/{company}', [CompanyController::class, 'show']);
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);
    Route::get('/companies/{company}/main-documents', [MainDocumentController::class, 'companyMainDocuments']);
    Route::get('/companies/{company}/{type}/download-documents', [MainDocumentController::class, 'downloadCompanyMainDocument'])
        ->withoutMiddleware(['auth:user']);

    //Company Activity Codes Routes
    Route::get('/activity-codes', [ActivityCodeController::class, 'index']);
    Route::post('/activity-codes', [ActivityCodeController::class, 'store']);
    Route::get('/activity-codes/{activityCode}', [ActivityCodeController::class, 'show']);
    Route::post('/activity-codes/{activityCode}', [ActivityCodeController::class, 'update']);
    Route::delete('/activity-codes/{activityCode}', [ActivityCodeController::class, 'destroy']);

    //Company Activity Codes Routes
    Route::get('/company-activity-codes', [ActivityCodeController::class, 'indexForCompany']);
    Route::get('/company-activity-codes/{activityCode}', [ActivityCodeController::class, 'showForCompany']);

    //Company Warehouse Routes
    Route::get('/warehouses', [WarehouseController::class, 'index']);
    Route::post('/warehouses', [WarehouseController::class, 'store']);
    Route::post('/warehouses/{warehouse}', [WarehouseController::class, 'update']);
    Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show']);
    Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy']);

    //Company Material Group Routes
    Route::get('/material-groups', [MaterialGroupController::class, 'index']);
    Route::post('/material-groups', [MaterialGroupController::class, 'store']);
    Route::post('/material-groups/{materialGroup}', [MaterialGroupController::class, 'update']);
    Route::get('/material-groups/{materialGroup}', [MaterialGroupController::class, 'show']);
    Route::delete('/material-groups/{materialGroup}', [MaterialGroupController::class, 'destroy']);

    //Company Material Routes
    Route::get('/materials', [MaterialController::class, 'index']);
    Route::post('/materials', [MaterialController::class, 'store']);
    Route::post('/materials/{material}', [MaterialController::class, 'update']);
    Route::get('/materials/{material}', [MaterialController::class, 'show']);
    Route::delete('/materials/{material}', [MaterialController::class, 'destroy']);
});

