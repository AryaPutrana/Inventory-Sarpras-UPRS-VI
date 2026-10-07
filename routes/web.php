<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes (requires authentication)
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
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

    // Reports (Laporan)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Export routes dengan rate limiting lebih ketat
    Route::middleware('throttle:10,1')->group(function () {
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
            ->name('reports.pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])
            ->name('reports.excel');
    });
});
