<?php

use App\Http\Controllers\Api\V1\Companies\ActivityCodeController;
use App\Http\Controllers\Api\V1\Companies\AttendanceLogConfigController;
use App\Http\Controllers\Api\V1\Companies\AttendanceLogController;
use App\Http\Controllers\Api\V1\Companies\CompanyController;
use App\Http\Controllers\Api\V1\Companies\MainDocumentController;
use App\Http\Controllers\Api\V1\Companies\MaterialController;
use App\Http\Controllers\Api\V1\Companies\MaterialGroupController;
use App\Http\Controllers\Api\V1\Companies\MeasureController;
use App\Http\Controllers\Api\V1\Companies\PositionController;
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

    // Measures Routes
    Route::get('/measures', [MeasureController::class, 'index']);
    Route::post('/measures', [MeasureController::class, 'store']);
    Route::post('/measures/{measure}', [MeasureController::class, 'update']);
    Route::get('/measures/{measure}', [MeasureController::class, 'show']);
    Route::delete('/measures/{measure}', [MeasureController::class, 'destroy']);

    // Positions Routes
    Route::get('/positions', [PositionController::class, 'index']);
    Route::post('/positions', [PositionController::class, 'store']);
    Route::post('/positions/{position}', [PositionController::class, 'update']);
    Route::get('/positions/{position}', [PositionController::class, 'show']);
    Route::delete('/positions/{position}', [PositionController::class, 'destroy']);

    // Company Positions
    Route::get('/company-positions', [PositionController::class, 'showPositionsByCompany']);

    // AttendanceLogConfig Routes
    Route::get('/attendance-logs-config', [AttendanceLogConfigController::class, 'index']);
    Route::post('/attendance-logs-config', [AttendanceLogConfigController::class, 'store']);
    Route::post('/attendance-logs-config/{attendanceLogConfig}', [AttendanceLogConfigController::class, 'update']);
    Route::get('/attendance-logs-config/{attendanceLogConfig}', [AttendanceLogConfigController::class, 'show']);
    Route::delete('/attendance-logs-config/{attendanceLogConfig}', [AttendanceLogConfigController::class, 'destroy']);

    // AttendanceLog Routes
    Route::get('/attendance-logs', [AttendanceLogController::class, 'index']);
    Route::post('/attendance-logs', [AttendanceLogController::class, 'store']);
    Route::post('/attendance-logs/{attendanceLog}', [AttendanceLogController::class, 'update']);
    Route::get('/attendance-logs/{attendanceLog}', [AttendanceLogController::class, 'show']);
    Route::delete('/attendance-logs/{attendanceLog}', [AttendanceLogController::class, 'destroy']);
});

