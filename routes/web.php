<?php

use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\PaymentController;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;


Volt::route('/', 'landing-page')->name('home');
Volt::route('/sign-in', 'landing-page')->name('sign-in')->middleware('not-auth');
Volt::route('/booking', 'landing-page')->name('booking');
Volt::route('/book-table', 'landing-page')->name('book-table');
Volt::route('/register', 'landing-page')->name('register');

Route::middleware(['auth'])->group(function () {
    Volt::route('/product', 'landing-page')->name('product');
    Volt::route('/product/{booking_id}', 'landing-page')->name('product-booking-id')->middleware('validate-booking');
    Volt::route('/manage-product', 'landing-page')->name('manage-product');
    Volt::route('/setting', 'landing-page')->name('setting');
    Volt::route('/purchase', 'landing-page')->name('purchase');
    Volt::route('/order', 'landing-page')->name('order');
    Volt::route('/coupon', 'landing-page')->name('coupon');

    // File Uploads
    Route::post('/upload-file', [FileUploadController::class, 'store']);
});


// Square Webhook
Route::post('/square-webhook', [PaymentController::class, 'webhook']);

// OTP Page
Volt::route('/otp', 'otp')->name('otp');
