<?php

use App\Http\Controllers\Api\V1\Employee\EmployeeAuthController;
use App\Http\Controllers\Api\V1\Envelopes\EnvelopeController;
use App\Http\Controllers\Api\V1\Users\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Envelopes Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth:user', 'lang'])->group(function () {
    // Company Routes
    Route::get('/envelopes', [EnvelopeController::class, 'index']);
    Route::post('/envelopes', [EnvelopeController::class, 'store']);
    Route::post('/envelopes/{envelope}', [EnvelopeController::class, 'update'])
        ->middleware('check_role_update:App\Models\Envelopes\Envelope,envelope');
    Route::get('/envelopes/{envelope}', [EnvelopeController::class, 'show']);
    Route::delete('/envelopes/{envelope}', [EnvelopeController::class, 'destroy']);
});
