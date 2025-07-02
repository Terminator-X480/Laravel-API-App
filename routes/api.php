<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\WhatsappController;
use App\Http\Controllers\Api\AudioUploadController;
use App\Http\Controllers\Api\CallLogsController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\LeadPortalController;
use App\Http\Controllers\Api\TrekController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\GearRentalController;

Route::middleware(['auth.basic.woo'])->get('/get-lead', [LeadController::class, 'getLeadByPhone']);

    // Public login
    Route::post('/madtrek/v1/login', [AuthController::class, 'login']);

    // Protected routes with custom token auth middleware
    Route::middleware('auth.token')->group(function () {
    
    // Session Expiry
    Route::get('/madtrek/v1/session-expiry', [SessionController::class, 'check']);

    // Vendors Api's
    Route::get('/madtrek/v1/vendors', [VendorController::class, 'index']);
    Route::post('/madtrek/v1/vendors', [VendorController::class, 'store']);
    Route::get('/madtrek/v1/vendors/b2b', [VendorController::class, 'b2bVendors']);
    Route::get('/madtrek/v1/vendors/non-b2b', [VendorController::class, 'nonB2bVendors']);

    // Location Api's
    Route::get('/madtrek/v1/locations', [LocationController::class, 'index']);
    Route::post('/madtrek/v1/locations', [LocationController::class, 'store']);

    // Expense Api's
    Route::get('/madtrek/v1/expense', [ExpenseController::class, 'index']);
    Route::post('/madtrek/v1/expense', [ExpenseController::class, 'store']);

    // Leads & Trek Detals Api
    Route::post('/madtrek/v1/get-lead-data', [LeadController::class, 'getLeadData']);
    Route::get('/madtrek/v1/trek-name', [TrekController::class, 'getName']);

    // Payments Api's
    Route::get('/madtrek/v1/payments', [PaymentController::class, 'index']);
    Route::post('/madtrek/v1/add-payment', [PaymentController::class, 'addPayment']);

    // Equipments Api's
    Route::get('/madtrek/v1/equipments', [EquipmentController::class, 'index']);
    Route::post('/madtrek/v1/equipments', [EquipmentController::class, 'store']);

    // Rental Api's
    Route::get('/madtrek/v1/rentals', [GearRentalController::class, 'index']);
    Route::post('/madtrek/v1/rentals', [GearRentalController::class, 'store']);

});

// WooCommerce Basic Auth routes
Route::middleware('basic.auth.woo')->group(function () {
    Route::get('/madtrek/v1/whatsapp', [WhatsappController::class, 'handleWhatsappMessage']);
    Route::get('/madtrek/v1/get-lead', [LeadController::class, 'getLeadByPhone']);
    Route::post('/madtrek/v1/upload-audio', [AudioUploadController::class, 'uploadAudio']);
    Route::match(['get', 'post'], '/madtrek/v1/call', [CallLogsController::class, 'handleCallLogs']);
});


// Route::get('/madtrek/v1/product-price', [LeadController::class, 'getProductPrice']);
// Route::get('/madtrek/v1/payments-by-lead', [LeadController::class, 'getPaymentsByLead']);

// Products Data
// Route::get('/madtrek/v1/products', [LeadController::class, 'index']);
// Route::get('/madtrek/v1/product/{id}', [LeadController::class, 'getProductDetails']);
// Route::get('/madtrek/v1/expense', [ExpenseController::class, 'index']);
