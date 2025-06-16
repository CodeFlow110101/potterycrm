<?php

use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\PaymentController;
use App\Mail\BookingCapacityExceededMail;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;


Volt::route('/', 'landing-page')->name('home');
Volt::route('/log-in', 'landing-page')->name('log-in')->middleware('not-auth');
Volt::route('/book-table', 'landing-page')->name('book-table');
Volt::route('/about-us', 'landing-page')->name('about-us');
Volt::route('/contact-us', 'landing-page')->name('contact-us');
Volt::route('/how-it-works', 'landing-page')->name('how-it-works');
Volt::route('/shop', 'landing-page')->name('shop');
Volt::route('/product/{id}', 'landing-page')->name('product')->middleware('check-product-availability');
Volt::route('/cart', 'landing-page')->name('cart');
Volt::route('/checkout', 'landing-page')->name('checkout');
Volt::route('/faq', 'landing-page')->name('faq');
Volt::route('/classes', 'landing-page')->name('classes');
Volt::route('/private-groups', 'landing-page')->name('private-groups');

Route::middleware(['auth'])->group(function () {
    Volt::route('/register', 'landing-page')->name('register')->can('register-user');
    Volt::route('/manage-coupon', 'landing-page')->name('manage-coupon')->can('create-product')->can('update-product');
    Volt::route('/manage-booking', 'landing-page')->name('manage-booking')->can('create-date')->can('update-date');
    Volt::route('/manage-product/{id?}', 'landing-page')->name('manage-product')->can('create-product')->can('update-product');
    Volt::route('/user', 'landing-page')->name('user')->can('update-product');
    Volt::route('/purchase', 'landing-page')->name('purchase');
    Volt::route('/order', 'landing-page')->name('order');
    Volt::route('/coupon', 'landing-page')->name('coupon');
    Volt::route('/booking', 'landing-page')->name('booking');
    Volt::route('/process-payment', 'landing-page')->name('process-payment')->middleware('validate-payment');

    // File Uploads
    Route::post('/upload-file', [FileUploadController::class, 'store']);
});

Volt::route('/test', 'test')->name('test');

// Square Webhook
Route::post('/square-webhook', [PaymentController::class, 'webhook']);

// OTP Page
// Volt::route('/otp', 'otp')->name('otp');
