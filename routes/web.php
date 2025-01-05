<?php

use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TerminalController;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;


Volt::route('/', 'landing-page')->name('home');
Volt::route('/sign-in', 'landing-page')->name('sign-in');
Volt::route('/booking', 'landing-page')->name('booking');
Volt::route('/book-table', 'landing-page')->name('book-table');
Volt::route('/product', 'landing-page')->name('product');
Volt::route('/product/{bookingId}', 'landing-page')->name('product-booking-id');
Volt::route('/manage-product', 'landing-page')->name('manage-product');
Volt::route('/setting', 'landing-page')->name('setting');
Volt::route('/purchase', 'landing-page')->name('purchase');
Volt::route('/order', 'landing-page')->name('order');

// File Uploads
Route::post('/upload-file', [FileUploadController::class, 'store']);

// Square Webhook
Route::post('/square-webhook', [PaymentController::class, 'webhook']);

// OTP Page
Volt::route('/otp', 'otp')->name('otp');
