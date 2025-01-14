<?php

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use function Livewire\Volt\{state, layout, mount, title};

layout('components.layouts.app');

state(['path', 'url', 'auth']);

title('Icona');

mount(function () {
    $this->path = request()->path();
    $this->url = request()->url();
    $this->auth = Auth::check();
});
?>

<div class="h-dvh">
    <livewire:toastr />
    <livewire:modal.modal />
    @if($path == '/')
    <livewire:home.home />
    @elseif($path == 'sign-in')
    <livewire:sign-in />
    @elseif($path == 'book-table')
    <livewire:book-table />
    @elseif($path == 'register')
    <livewire:register />
    @elseif($path == 'about-us')
    <livewire:home.about-us />
    @endif
    @if($this->auth)
    <div class="flex justify-between h-full">
        <div class="w-min h-full">
            <livewire:side-bar :path="$path" />
        </div>
        <div class="w-full flex flex-col">
            @if($path == 'booking')
            <livewire:booking :url="$url" />
            @elseif($path == 'product' || Route::currentRouteName() == 'product-booking-id')
            <livewire:product.product />
            @elseif($path == 'manage-product')
            <livewire:product.manage-product />
            @elseif($path == 'setting')
            <livewire:setting.setting />
            @elseif($path == 'purchase')
            <livewire:purchase />
            @elseif($path == 'order')
            <livewire:order />
            @elseif($path == 'coupon')
            <livewire:coupon />
            @endif
        </div>
    </div>
    @endif
</div>