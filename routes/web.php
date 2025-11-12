<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MedicalCenterController;
use App\Http\Controllers\MedicalTestController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferencesController;
use App\Http\Controllers\UserManagmentController;
use App\Http\Controllers\WaffedController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Dashboard Data
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Contacts
    Route::resource('contact',ContactController::class)->names('contact');
    Route::resource('medical-test',MedicalTestController::class)->names('medical-test');
    Route::resource('medical-center',MedicalCenterController::class)->names('medical-center');
    Route::resource('reference',ReferencesController::class)->names('reference');
    Route::resource('patient',PatientController::class)->names('patient');
    Route::get('/without-invoce', [PatientController::class, 'withoutInvoce'])->name('without-invoce');
    Route::get('/medi', [WaffedController::class, 'searchSlip'])->name('medi');
    Route::get('/due-invoice', [InvoiceController::class, 'dueInvoice'])->name('due-invoice');
    Route::get('/paid-invoice', [InvoiceController::class, 'paidInvoice'])->name('paid-invoice');
    Route::resource('invoices',InvoiceController::class)->names('invoice');
    Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoice.print');
    // User Managment
    Route::get('/wafid-slip', [WaffedController::class, 'index'])->name('wafid-slip.index');
    Route::get('/wafid-slip-create', [WaffedController::class, 'create'])->name('wafid-slip.create');
    Route::post('/wafid-slip-check', [WaffedController::class, 'fetchMedicalStatus'])->name('wafid-slip.store');
    Route::get('/check', [WaffedController::class, 'fetchMedicalStatus'])->name('check');
    Route::get('/wafid-print/{id}', [WaffedController::class, 'pdf'])->name('wafid-print');


});

Route::middleware(['auth', 'isAdmin:1'])->group(function () {
    Route::resource('user-list',UserManagmentController::class)->names('user-list');
});
require __DIR__ . '/auth.php';
