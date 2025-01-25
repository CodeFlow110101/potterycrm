<?php

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use function Livewire\Volt\{state, layout, mount, title, on, uses};

layout('components.layouts.app');


state(['path', 'url', 'auth', 'routeName', 'cart' => session('cart', [])]);

title('Icona');

on([
    'add-cart' => function ($id, $quantity) {
        if (isset($this->cart[$id])) {
            $this->cart[$id] += $quantity;
        } else {
            $this->cart[$id] = $quantity;
        }

        session(['cart' => $this->cart]);
    },
    'update-cart' => function ($id, $quantity) {
        if ($quantity == 0) {
            unset($this->cart[$id]);
        } else {
            $this->cart[$id] = $quantity;
        }
        session(['cart' => $this->cart]);
    },
    'remove-cart' => function ($id) {
        unset($this->cart[$id]);
        session(['cart' => $this->cart]);
    }
]);

mount(function () {
    $this->path = request()->path();
    $this->url = request()->url();
    $this->auth = Auth::user();
    $this->routeName = Route::currentRouteName();
});
?>

<div class="h-dvh flex flex-col">
    <livewire:toastr />
    <livewire:modal.modal />
    <livewire:home.header :routeName="$routeName" :cart="$cart" :path="$path" :auth="$auth" />
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
    @elseif($path == 'contact-us')
    <livewire:contact-us />
    @elseif($routeName == 'shop')
    <livewire:shop :auth="$auth"/>
    @elseif($path == 'faq')
    <livewire:faq />
    @elseif($this->routeName == 'product')
    <livewire:product />
    @elseif($routeName == 'checkout')
    <livewire:checkout :cart="$cart" />
    @elseif($routeName == 'cart')
    <livewire:cart :cart="$cart" />
    @endif
    @if($auth)
    <div class="w-3/4 mx-auto grow flex flex-col">
        @if($path == 'booking')
        <livewire:booking :url="$url" :auth="$auth"/>
        @elseif($path == 'product' || Route::currentRouteName() == 'product-booking-id')
        <livewire:product.shop />
        @elseif($path == 'manage-product')
        <livewire:product.manage-product />
        @elseif($path == 'setting')
        <livewire:setting.setting />
        @elseif($path == 'purchase')
        <livewire:purchase :auth="$auth" />
        @elseif($path == 'order')
        <livewire:order :auth="$auth" />
        @elseif($path == 'manage-coupon')
        <livewire:manage-coupon :auth="$auth" />
        @elseif($path == 'coupon')
        <livewire:coupon :auth="$auth"/>
        @endif
    </div>
    @endif
    <livewire:home.footer :routeName="$routeName" :auth="$auth" />
</div>