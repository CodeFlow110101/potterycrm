<?php

use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{state, layout, mount};

layout('components.layouts.app');

state(['path']);

mount(function () {
    $this->path = request()->path();
    $isAuth = Auth::check();
    if ($isAuth && in_array($this->path, ['/', 'sign-in', 'book-table'])) {
        $this->redirectRoute('product', navigate: true);
    } elseif (!$isAuth && in_array($this->path, ['booking', 'product', 'manage-product'])) {
        $this->redirectRoute('home', navigate: true);
    }
});
?>

<div class="h-screen">
    <livewire:toastr />
    <livewire:modal.modal />
    @if(in_array($path , ['/' , 'sign-in', 'book-table']))
    @if($path == '/')
    <livewire:home />
    @elseif($path == 'sign-in')
    <livewire:sign-in />
    @elseif($path == 'book-table')
    <livewire:book-table />
    @endif
    @elseif(in_array($path , ['booking', 'product', 'manage-product']))
    <div class="flex justify-between h-full">
        <div class="w-1/12 h-full">
            <livewire:side-bar :path="$path" />
        </div>
        <div class="w-11/12 flex flex-col">
            @if($path == 'booking')
            <livewire:booking />
            @elseif($path == 'product')
            <livewire:product.product />
            @elseif($path == 'manage-product')
            <livewire:product.manage-product />
            @endif
        </div>
    </div>
    @endif
</div>