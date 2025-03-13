<?php

use App\Models\Booking;
use App\Models\Checkout;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use function Livewire\Volt\{state, layout, mount, title, on, uses, with};

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
    Product::where('id', array_keys($this->cart))->onlyTrashed()->get()->pluck('id')->each(function ($id) {
        unset($this->cart[$id]);
        session(['cart' => $this->cart]);
    });

    $this->path = request()->path();
    $this->url = request()->url();
    $this->auth = Auth::user();
    $this->routeName = Route::currentRouteName();
});
?>

<div class="h-dvh @if($this->routeName == 'home') @endif flex flex-col bg-no-repeat bg-cover bg-black" style="background-image: url('{{ asset('/images/home.jpeg')}}');">
    <livewire:toastr />
    <livewire:home.header :routeName="$routeName" :cart="$cart" :path="$path" :auth="$auth" />
    <div class="grow flex flex-col">
        @if($path == '/')
        <livewire:home.home />
        @elseif($path == 'classes')
        <livewire:home.classes />
        @elseif($path == 'private-groups')
        <livewire:home.private-groups />
        @elseif($path == 'log-in')
        <livewire:log-in />
        @elseif($path == 'book-table')
        <livewire:booking.book-table />
        @elseif($path == 'register')
        <livewire:register />
        @elseif($path == 'about-us')
        <livewire:home.about-us />
        @elseif($path == 'contact-us')
        <livewire:contact-us />
        @elseif($path == 'user')
        <livewire:user />
        @elseif($routeName == 'shop')
        <livewire:shop :auth="$auth" />
        @elseif($path == 'faq')
        <livewire:faq />
        @elseif($this->routeName == 'product')
        <livewire:product />
        @elseif($routeName == 'checkout')
        <livewire:checkout :cart="$cart" />
        @elseif($routeName == 'cart')
        <livewire:cart :cart="$cart" />
        @elseif($routeName == 'how-it-works')
        <livewire:how-it-works />
        @elseif($path == 'booking')
        <livewire:booking.booking :url="$url" :auth="$auth" />
        @elseif($path == 'product' || Route::currentRouteName() == 'product-booking-id')
        <livewire:product.shop />
        @elseif($routeName == 'manage-product')
        <livewire:product.manage-product />
        @elseif($path == 'purchase')
        <livewire:purchase :auth="$auth" />
        @elseif($path == 'order')
        <livewire:order :auth="$auth" />
        @elseif($path == 'manage-coupon')
        <livewire:manage-coupon :auth="$auth" />
        @elseif($path == 'process-payment')
        <livewire:process-payment />
        @elseif($path == 'coupon')
        <livewire:coupon :auth="$auth" />
        @elseif($path == 'manage-booking')
        <livewire:manage-booking :auth="$auth" />
        @endif
    </div>
</div>