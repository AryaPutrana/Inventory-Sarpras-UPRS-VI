<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes (requires authentication)
Route::middleware('auth')->group(function () {
    // Redirect root to dashboard
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Items (Inventory Barang)
    Route::resource('items', ItemController::class);

    // Withdrawals (Pengambilan Barang)
    Route::resource('withdrawals', WithdrawalController::class);
    Route::get('/api/items/{id}', [WithdrawalController::class, 'getItemDetails'])->name('items.details');

    // Reports (Laporan)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
});
