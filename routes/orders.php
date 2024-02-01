<?php

use App\Http\Controllers\Api\V1\Orders\AwardOrderController;
use App\Http\Controllers\Api\V1\Orders\BusinessTripOrderController;
use App\Http\Controllers\Api\V1\Orders\DefaultHolidayOrderController;
use App\Http\Controllers\Api\V1\Orders\HiringOrderController;
use App\Http\Controllers\Api\V1\Orders\MotherhoodOrderController;
use App\Http\Controllers\Api\V1\Orders\OrderController;
use App\Http\Controllers\Api\V1\Orders\PregnantOrderController;
use App\Http\Controllers\Api\V1\Orders\TerminationOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Orders Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['lang'])->group(function () {
    Route::post('/hiring-orders', [HiringOrderController::class, 'store']);
    Route::get('/hiring-orders/{hiringOrder}/download', [OrderController::class, 'downloadHiringOrderFile']);
    Route::get('/hiring-orders/{hiringOrder}', [HiringOrderController::class, 'show']);
    Route::post('/hiring-orders/{hiringOrder}', [HiringOrderController::class, 'update']);
    Route::post('/hiring-orders/{hiringOrder}/delete', [HiringOrderController::class, 'destroy']);

    Route::post('/business-trip-orders', [BusinessTripOrderController::class, 'store']);
    Route::get('/business-trip-orders/{businessTripOrder}/download',
        [OrderController::class, 'downloadBusinessTripOrderFile']);
    Route::get('/business-trip-orders/{businessTripOrder}', [BusinessTripOrderController::class, 'show']);
    Route::post('/business-trip-orders/{businessTripOrder}', [BusinessTripOrderController::class, 'update']);
    Route::post('/business-trip-orders/{businessTripOrder}/delete', [BusinessTripOrderController::class, 'destroy']);

    Route::post('/termination-orders', [TerminationOrderController::class, 'store']);
    Route::get('/termination-orders/{terminationOrder}/download', [OrderController::class, 'downloadTerminationOrderFile']);
    Route::get('/termination-orders/{terminationOrder}', [TerminationOrderController::class, 'show']);
    Route::post('/termination-orders/{terminationOrder}', [TerminationOrderController::class, 'update']);
    Route::post('/termination-orders/{terminationOrder}/delete', [TerminationOrderController::class, 'destroy']);

    Route::post('/pregnant-orders', [PregnantOrderController::class, 'store']);
    Route::get('/pregnant-orders/{pregnantOrder}/download', [OrderController::class, 'downloadPregnantOrderFile']);
    Route::get('/pregnant-orders/{pregnantOrder}', [PregnantOrderController::class, 'show']);
    Route::post('/pregnant-orders/{pregnantOrder}', [PregnantOrderController::class, 'update']);
    Route::post('/pregnant-orders/{pregnantOrder}/delete', [PregnantOrderController::class, 'destroy']);

    Route::post('/default-holiday-orders', [DefaultHolidayOrderController::class, 'store']);
    Route::get('/default-holiday-orders/{defaultHolidayOrder}/download',
        [OrderController::class, 'downloadDefaultHolidayOrderFile']);
    Route::get('/default-holiday-orders/{defaultHolidayOrder}', [PregnantOrderController::class, 'show']);
    Route::post('/default-holiday-orders/{defaultHolidayOrder}', [PregnantOrderController::class, 'update']);
    Route::post('/default-holiday-orders/{defaultHolidayOrder}/delete', [PregnantOrderController::class, 'destroy']);

    Route::post('/motherhood-holiday-orders', [MotherhoodOrderController::class, 'store']);
    Route::get('/motherhood-holiday-orders/{motherhoodHolidayOrder}/download',
        [OrderController::class, 'downloadMotherhoodHolidayOrderFile']);
    Route::get('/motherhood-holiday-orders/{motherhoodHolidayOrder}', [MotherhoodOrderController::class, 'show']);
    Route::post('/motherhood-holiday-orders/{motherhoodHolidayOrder}', [MotherhoodOrderController::class, 'update']);
    Route::post('/motherhood-holiday-orders/{motherhoodHolidayOrder}/delete',
        [MotherhoodOrderController::class, 'destroy']);

    Route::post('/award-orders', [AwardOrderController::class, 'store']);
    Route::get('/award-orders/{awardOrder}/download',
        [OrderController::class, 'downloadAwardOrderFile']);
    Route::get('/award-orders/{awardOrder}', [AwardOrderController::class, 'show']);
    Route::post('/award-orders/{awardOrder}', [AwardOrderController::class, 'update']);
    Route::post('/award-orders/{awardOrder}/delete',
        [AwardOrderController::class, 'destroy']);
});
