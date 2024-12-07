<?php

use App\Http\Controllers\FileUploadController;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;


Volt::route('/', 'landing-page')->name('home');
Volt::route('/sign-in', 'landing-page')->name('sign-in');
Volt::route('/booking', 'landing-page')->name('booking');
Volt::route('/book-table', 'landing-page')->name('book-table');
Volt::route('/product', 'landing-page')->name('product');
Volt::route('/manage-product', 'landing-page')->name('manage-product');
Volt::route('/setting', 'landing-page')->name('setting');

// File Uploads
Route::post('/upload-file', [FileUploadController::class, 'store']);

// OTP Page
Volt::route('/otp', 'otp')->name('otp');

