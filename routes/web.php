<?php

use Livewire\Volt\Volt;


Volt::route('/', 'landing-page')->name('home');
Volt::route('/dashboard', 'landing-page')->name('dashboard');
