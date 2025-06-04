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

Route::middleware(['auth.basic.woo'])->get('/get-lead', [LeadController::class, 'getLeadByPhone']);

// Public login route (no auth required)
Route::post('/madtrek/v1/login', [AuthController::class, 'login']);

// Protected routes with custom token auth middleware
Route::middleware('auth.token')->group(function () {
    Route::get('/madtrek/v1/vendors', [VendorController::class, 'index']);
    Route::post('/madtrek/v1/vendors', [VendorController::class, 'store']);
    Route::post('/madtrek/v1/expense', [ExpenseController::class, 'store']);
    Route::get('/madtrek/v1/locations', [LocationController::class, 'index']);
    Route::post('/madtrek/v1/locations', [LocationController::class, 'store']);
    Route::get('/madtrek/v1/session-expiry', [SessionController::class, 'check']);
});

// WooCommerce Basic Auth routes
Route::middleware('basic.auth.woo')->group(function () {
    Route::get('/madtrek/v1/whatsapp', [WhatsappController::class, 'handleWhatsappMessage']);
    Route::post('/madtrek/v1/upload-audio', [AudioUploadController::class, 'uploadAudio']);
    Route::match(['get', 'post'], '/madtrek/v1/call', [CallLogsController::class, 'handleCallLogs']);
    Route::get('/madtrek/v1/get-lead', [LeadController::class, 'getLeadByPhone']);
});


Route::middleware('web')->group(function () {
    Route::get('/madtrek/v1/leads-login', [LeadPortalController::class, 'showLoginForm'])->name('leads.login');
    Route::post('/madtrek/v1/leads-login', [LeadPortalController::class, 'login'])->name('leads.login.submit');
    Route::get('/madtrek/v1/leads-dashboard', [LeadPortalController::class, 'dashboard'])->name('leads.dashboard');
    Route::get('/madtrek/v1/leads-logout', [LeadPortalController::class, 'logout'])->name('leads.logout');
    Route::get('/madtrek/v1/leads', [LeadController::class, 'getAllLeads']);
    Route::post('/madtrek/v1/lead-action', [LeadController::class, 'handleAction']);
    Route::get('/madtrek/v1/leads/{id}', [LeadController::class, 'get']);
});

Route::put('/madtrek/v1/leads/{id}', [LeadController::class, 'update']);
Route::post('/madtrek/v1/leads/{id}/book', [LeadController::class, 'book'])->name('leads.book');
Route::post('/madtrek/v1/leads/{id}/cancel', [LeadController::class, 'cancel']);
Route::post('/madtrek/v1/leads/{id}/unbook', [LeadController::class, 'unbook']);
Route::post('/madtrek/v1/leads/{id}/uncancel', [LeadController::class, 'uncancel']);

Route::post('/madtrek/v1/get-lead-data', [LeadController::class, 'getLeadData']);

Route::get('/madtrek/v1/vendors', [VendorController::class, 'index']);
Route::get('/madtrek/v1/vendors/b2b', [VendorController::class, 'b2bVendors']); // Only B2B
Route::get('/madtrek/v1/vendors/non-b2b', [VendorController::class, 'nonB2bVendors']);

Route::get('/madtrek/v1/trek-name', [TrekController::class, 'getName']);
Route::post('/madtrek/v1/add-payment', [PaymentController::class, 'addPayment']);

Route::get('/madtrek/v1/product-price', [LeadController::class, 'getProductPrice']);
Route::get('/madtrek/v1/payments-by-lead', [LeadController::class, 'getPaymentsByLead']);
