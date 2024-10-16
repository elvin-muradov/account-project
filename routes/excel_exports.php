<?php

use App\Http\Controllers\Api\V1\Excel\AttendanceLogExcelController;
use App\Http\Controllers\Api\V1\Excel\ImportCostExcelController;
use App\Http\Controllers\Api\V1\Excel\ImportQueryExcelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Excel Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/import-queries/export', [ImportQueryExcelController::class, 'exportImportQueryExcel']);
Route::get('/import-queries-g/export', [ImportQueryExcelController::class, 'exportImportQueryGExcel']);
Route::get('/import-costs/export', [ImportCostExcelController::class, 'exportImportCostsExcel']);
Route::get('/import-costs-vn/export', [ImportCostExcelController::class, 'exportImportCostsVNExcel']);
Route::get('/attendance-logs/export', [AttendanceLogExcelController::class, 'exportAttendanceLogExcel'])
    ->withoutMiddleware(['auth']);

