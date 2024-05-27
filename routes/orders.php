<?php

use App\Http\Controllers\Api\V1\Orders\AwardOrderController;
use App\Http\Controllers\Api\V1\Orders\BusinessTripOrderController;
use App\Http\Controllers\Api\V1\Orders\DefaultHolidayOrderController;
use App\Http\Controllers\Api\V1\Orders\HiringOrderController;
use App\Http\Controllers\Api\V1\Orders\IllnessOrderController;
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
    Route::get('/hiring-orders', [HiringOrderController::class, 'index']);
    Route::post('/hiring-orders', [HiringOrderController::class, 'store']);
    Route::get('/hiring-orders/{hiringOrder}/download', [OrderController::class, 'downloadHiringOrderFile']);
    Route::get('/hiring-orders/{hiringOrder}/get-file', [OrderController::class, 'getHiringOrderFile']);
    Route::get('/hiring-orders/{hiringOrder}', [HiringOrderController::class, 'show']);
    Route::post('/hiring-orders/{hiringOrder}', [HiringOrderController::class, 'update'])
        ->middleware('check_role_update:App\Models\Orders\HiringOrder,hiringOrder');
    Route::post('/hiring-orders/{hiringOrder}/delete', [HiringOrderController::class, 'destroy']);

    Route::get('/business-trip-orders', [BusinessTripOrderController::class, 'index']);
    Route::post('/business-trip-orders', [BusinessTripOrderController::class, 'store']);
    Route::get('/business-trip-orders/{businessTripOrder}/download',
        [OrderController::class, 'downloadBusinessTripOrderFile']);
    Route::get('/business-trip-orders/{businessTripOrder}/get-file',
        [OrderController::class, 'getBusinessTripOrderFile']);
    Route::get('/business-trip-orders/{businessTripOrder}', [BusinessTripOrderController::class, 'show']);
    Route::post('/business-trip-orders/{businessTripOrder}', [BusinessTripOrderController::class, 'update'])
        ->middleware('check_role_update:App\Models\Orders\BusinessTripOrder,businessTripOrder');
    Route::post('/business-trip-orders/{businessTripOrder}/delete', [BusinessTripOrderController::class, 'destroy']);

    Route::get('/termination-orders', [TerminationOrderController::class, 'index']);
    Route::post('/termination-orders', [TerminationOrderController::class, 'store']);
    Route::get('/termination-orders/{terminationOrder}/download', [OrderController::class,
        'downloadTerminationOrderFile']);
    Route::get('/termination-orders/{terminationOrder}/get-file', [OrderController::class,
        'getTerminationOrderFile']);
    Route::get('/termination-orders/{terminationOrder}', [TerminationOrderController::class, 'show']);
    Route::post('/termination-orders/{terminationOrder}', [TerminationOrderController::class, 'update'])
        ->middleware('check_role_update:App\Models\Orders\TerminationOrder,terminationOrder');
    Route::post('/termination-orders/{terminationOrder}/delete', [TerminationOrderController::class, 'destroy']);

    Route::get('/pregnant-orders', [PregnantOrderController::class, 'index']);
    Route::post('/pregnant-orders', [PregnantOrderController::class, 'store']);
    Route::get('/pregnant-orders/{pregnantOrder}/download', [OrderController::class, 'downloadPregnantOrderFile']);
    Route::get('/pregnant-orders/{pregnantOrder}/get-file', [OrderController::class, 'getPregnantOrderFile']);
    Route::get('/pregnant-orders/{pregnantOrder}', [PregnantOrderController::class, 'show']);
    Route::post('/pregnant-orders/{pregnantOrder}', [PregnantOrderController::class, 'update'])
        ->middleware('check_role_update:App\Models\Orders\PregnantOrder,pregnantOrder');
    Route::post('/pregnant-orders/{pregnantOrder}/delete', [PregnantOrderController::class, 'destroy']);

    Route::get('/illness-orders', [IllnessOrderController::class, 'index']);
    Route::post('/illness-orders', [IllnessOrderController::class, 'store']);
    Route::get('/illness-orders/{illnessOrder}/download', [OrderController::class, 'downloadIllnessOrderFile']);
    Route::get('/illness-orders/{illnessOrder}/get-file', [OrderController::class, 'getIllnessOrderFile']);
    Route::get('/illness-orders/{illnessOrder}', [IllnessOrderController::class, 'show']);
    Route::post('/illness-orders/{illnessOrder}', [IllnessOrderController::class, 'update'])
        ->middleware('check_role_update:App\Models\Orders\IllnessOrder,illnessOrder');
    Route::post('/illness-orders/{illnessOrder}/delete', [IllnessOrderController::class, 'destroy']);

    Route::get('/default-holiday-orders', [DefaultHolidayOrderController::class, 'index']);
    Route::post('/default-holiday-orders', [DefaultHolidayOrderController::class, 'store']);
    Route::get('/default-holiday-orders/{defaultHolidayOrder}/download',
        [OrderController::class, 'downloadDefaultHolidayOrderFile']);
    Route::get('/default-holiday-orders/{defaultHolidayOrder}/get-file',
        [OrderController::class, 'getDefaultHolidayOrderFile']);
    Route::get('/default-holiday-orders/{defaultHolidayOrder}', [DefaultHolidayOrderController::class, 'show']);
    Route::post('/default-holiday-orders/{defaultHolidayOrder}', [DefaultHolidayOrderController::class, 'update'])
        ->middleware('check_role_update:App\Models\Orders\DefaultHolidayOrder,defaultHolidayOrder');
    Route::post('/default-holiday-orders/{defaultHolidayOrder}/delete',
        [DefaultHolidayOrderController::class, 'destroy']);

    Route::get('/motherhood-holiday-orders', [MotherhoodOrderController::class, 'index']);
    Route::post('/motherhood-holiday-orders', [MotherhoodOrderController::class, 'store']);
    Route::get('/motherhood-holiday-orders/{motherhoodHolidayOrder}/download',
        [OrderController::class, 'downloadMotherhoodHolidayOrderFile']);
    Route::get('/motherhood-holiday-orders/{motherhoodHolidayOrder}/get-file',
        [OrderController::class, 'getMotherhoodHolidayOrderFile']);
    Route::get('/motherhood-holiday-orders/{motherhoodHolidayOrder}', [MotherhoodOrderController::class, 'show']);
    Route::post('/motherhood-holiday-orders/{motherhoodHolidayOrder}', [MotherhoodOrderController::class, 'update'])
        ->middleware('check_role_update:App\Models\Orders\MotherhoodHolidayOrder,motherhoodHolidayOrder');
    Route::post('/motherhood-holiday-orders/{motherhoodHolidayOrder}/delete',
        [MotherhoodOrderController::class, 'destroy']);

    Route::get('/award-orders', [AwardOrderController::class, 'index']);
    Route::post('/award-orders', [AwardOrderController::class, 'store']);
    Route::get('/award-orders/{awardOrder}/download',
        [OrderController::class, 'downloadAwardOrderFile']);
    Route::get('/award-orders/{awardOrder}/get-file',
        [OrderController::class, 'getAwardOrderFile']);
    Route::get('/award-orders/{awardOrder}', [AwardOrderController::class, 'show']);
    Route::post('/award-orders/{awardOrder}', [AwardOrderController::class, 'update'])
        ->middleware('check_role_update:App\Models\Orders\AwardOrder,awardOrder');
    Route::post('/award-orders/{awardOrder}/delete',
        [AwardOrderController::class, 'destroy']);
});
