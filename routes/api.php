<?php

use App\Http\Controllers\Api\V1\Companies\RentalContractController;
use App\Http\Controllers\Api\V1\Orders\OrderController;
use App\Http\Controllers\Api\V1\Users\RolePermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

require __DIR__ . '/auth.php'; // Login/Register routes - (User/Employee)
require __DIR__ . '/users_employees.php'; // Users/Employee routes
require __DIR__ . '/companies.php'; // Company routes
require __DIR__ . '/orders.php'; // Order routes
require __DIR__ . '/enums.php'; // ENUMs
require __DIR__ . '/envelopes.php'; // Envelopes

Route::middleware(['auth:user', 'lang'])->group(function () {
    //Roles
    Route::get('/roles', [RolePermissionController::class, 'getAllRoles']);
    Route::get('/company-director-or-main-user', [OrderController::class, 'companyDirectorOrMainUser']);

    // Rental Contracts Routes
    Route::get('/rental-contracts', [RentalContractController::class, 'indexAllRentalContracts']);
    Route::get('/shop-rental-contracts', [RentalContractController::class, 'indexShopRentalContracts']);
    Route::get('/warehouse-rental-contracts', [RentalContractController::class, 'indexWarehouseRentalContracts']);
    Route::get('/vehicle-rental-contracts', [RentalContractController::class, 'indexVehicleRentalContracts']);
    Route::post('/rental-contracts', [RentalContractController::class, 'store']);
    Route::get('/rental-contracts/{rentalContract}', [RentalContractController::class, 'show']);
    Route::post('/rental-contracts/{rentalContract}', [RentalContractController::class, 'update']);
    Route::delete('/rental-contracts/{rentalContract}', [RentalContractController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
