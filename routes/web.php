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
Volt::route('/about-us', 'landing-page')->name('about-us');
Volt::route('/contact-us', 'landing-page')->name('contact-us');
Volt::route('/shop', 'landing-page')->name('shop');
Volt::route('/product/{id}', 'landing-page')->name('product')->middleware('check-product-availability');
Volt::route('/cart', 'landing-page')->name('cart');
Volt::route('/checkout', 'landing-page')->name('checkout');
Volt::route('/faq', 'landing-page')->name('faq');

Route::middleware(['auth'])->group(function () {
    Route::middleware(['validate-booking'])->group(function () {
        Volt::route('/product/{id}/{booking_id}', 'landing-page')->name('product')->middleware('check-product-availability');
        Volt::route('/shop/{booking_id}', 'landing-page')->name('shop');
        Volt::route('/cart/{booking_id}', 'landing-page')->name('cart');
        Volt::route('/checkout/{booking_id}', 'landing-page')->name('checkout');
    });
    Volt::route('/manage-product', 'landing-page')->name('manage-product');
    Volt::route('/purchase', 'landing-page')->name('purchase');
    Volt::route('/order', 'landing-page')->name('order');
    Volt::route('/coupon', 'landing-page')->name('coupon');
    Volt::route('/manage-coupon', 'landing-page')->name('manage-coupon')->middleware('admin');

    // File Uploads
    Route::post('/upload-file', [FileUploadController::class, 'store']);
});


// Square Webhook
Route::post('/square-webhook', [PaymentController::class, 'webhook']);

// OTP Page
Volt::route('/otp', 'otp')->name('otp');
