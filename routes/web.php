<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;

Route::view('/', 'landing')->name('home');
Route::view('/payment/success', 'payments.success')->name('payment.success');
Route::view('/payment/failed', 'payments.failed')->name('payment.failed');

// Auth Routes
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('products', ProductController::class);
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('promocodes', App\Http\Controllers\Admin\PromoCodeController::class);
    
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::post('orders/{order}/resend', [OrderController::class, 'resend'])->name('orders.resend');

    Route::get('broadcast', [App\Http\Controllers\Admin\BroadcastController::class, 'create'])->name('broadcast.create');
    Route::post('broadcast', [App\Http\Controllers\Admin\BroadcastController::class, 'send'])->name('broadcast.send');
});
