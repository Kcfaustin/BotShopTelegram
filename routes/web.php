<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');
Route::view('/payment/success', 'payments.success')->name('payment.success');
Route::view('/payment/failed', 'payments.failed')->name('payment.failed');
