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
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\LeadLogsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/leads-login', [LeadPortalController::class, 'showLoginForm'])->name('leads.login');
Route::post('/admin/leads-login', [LeadPortalController::class, 'login'])->name('leads.login.submit');
Route::get('/admin/leads-dashboard', [LeadPortalController::class, 'dashboard'])->name('leads.dashboard');
Route::get('/admin/leads-logout', [LeadPortalController::class, 'logout'])->name('leads.logout');
Route::get('/admin/leads', [LeadController::class, 'getAllLeads']);
Route::post('/admin/lead-action', [LeadController::class, 'handleAction']);
Route::get('/admin/leads/{id}', [LeadController::class, 'get']);

Route::put('/admin/add-lead', [LeadController::class, 'addLead']);
Route::put('/admin/leads/{id}', [LeadController::class, 'update']);
Route::put('/admin/leads/{id}/book', [LeadController::class, 'book']);
Route::post('/admin/leads/{id}/unbook', [LeadController::class, 'unbook']);
Route::post('/admin/leads/{id}/cancel', [LeadController::class, 'cancel']);
Route::post('/admin/leads/{id}/uncancel', [LeadController::class, 'uncancel']);
Route::post('/admin/{id}/payment',[PaymentController::class, 'getPayments']); 
Route::post('/admin/addpayment',[LeadController::class, 'savePayments']); 
Route::post('/admin/{id}/call', [CallLogsController::class, 'callListById']);
Route::post('/admin/{id}/logs', [LeadLogsController::class, 'getLeadLogs']);
Route::post('/admin/{id}/update-lead-status', [LeadController::class, 'updateStatus']);
Route::get('/admin/treks', [LeadController::class, 'allTreks']);