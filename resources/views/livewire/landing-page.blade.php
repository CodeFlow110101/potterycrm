<?php

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use function Livewire\Volt\{state, layout, mount};

layout('components.layouts.app');

state(['path', 'url', 'id']);

mount(function () {
    $this->path = request()->path();
    $this->url = request()->url();
    $isAuth = Auth::check();
    if ($isAuth && in_array($this->path, config('constants.non-auth-paths'))) {
        $this->redirectRoute('product', navigate: true);
    } elseif (!$isAuth && in_array($this->path, config('constants.auth-paths'))) {
        $this->redirectRoute('home', navigate: true);
    }

    if (in_array(Route::currentRouteName(), config('constants.auth-paths-dynamic')) && is_numeric(str_replace('product/', '', $this->path)) && Booking::where('id', str_replace('product/', '', $this->path))->exists()) {
        $this->id = str_replace('product/', '', $this->path);
    } elseif (in_array(Route::currentRouteName(), config('constants.auth-paths-dynamic')) && is_numeric(str_replace('product/', '', $this->path)) && Booking::where('id', str_replace('product/', '', $this->path))->doesntExist()) {
        $this->redirectRoute('product', navigate: true);
    }
});
?>

<div class="h-dvh">
    <livewire:toastr />
    <livewire:modal.modal />
    @if(in_array($path , config('constants.non-auth-paths')))
    @if($path == '/')
    <livewire:home />
    @elseif($path == 'sign-in')
    <livewire:sign-in />
    @elseif($path == 'book-table')
    <livewire:book-table />
    @endif
    @elseif(in_array($path , config('constants.auth-paths')) || in_array(Route::currentRouteName() , config('constants.auth-paths-dynamic')))
    <div class="flex justify-between h-full">
        <div class="w-min h-full">
            <livewire:side-bar :path="$path" />
        </div>
        <div class="w-full flex flex-col">
            @if($path == 'booking')
            <livewire:booking :url="$url" />
            @elseif($path == 'product' || Route::currentRouteName() == 'product-booking-id')
            <livewire:product.product :id="$id" />
            @elseif($path == 'manage-product')
            <livewire:product.manage-product />
            @elseif($path == 'setting')
            <livewire:setting.setting />
            @elseif($path == 'purchase')
            <livewire:purchase />
            @elseif($path == 'order')
            <livewire:order />
            @endif
        </div>
    </div>
    @endif
</div>