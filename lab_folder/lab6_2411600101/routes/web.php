<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\DashboardApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products CRUD
    Route::resource('products', ProductController::class);

    // Inventory Transactions
    Route::resource('transactions', InventoryTransactionController::class)->except(['edit', 'update']);
    Route::get('/adjustments/create', [InventoryTransactionController::class, 'adjustmentForm'])->name('adjustments.create');
    Route::post('/adjustments', [InventoryTransactionController::class, 'processAdjustment'])->name('adjustments.store');

    // Mark notification as read
    Route::post('/notifications/{id}/read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back();
    })->name('notifications.read');

    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.readAll');
});

// API routes for real-time dashboard (also protected)
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);
    Route::get('/products/low-stock', [DashboardApiController::class, 'lowStock']);
    Route::get('/transactions/recent', [DashboardApiController::class, 'recentTransactions']);
    Route::get('/products/{product}/current-stock', [DashboardApiController::class, 'currentStock']);
});

require __DIR__.'/auth.php';
