<?php

use App\Http\Controllers\PaymentController;
use App\Models\PostalCode;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\App;
use Square\Models\Order;

use function Livewire\Volt\{state, with};

with(fn() => [
    'products' => Product::when(!Gate::allows('view-any-product'), function ($query) {
        $query->whereHas('type', function ($typeQuery) {
            $typeQuery->where('name', 'online');
        });
    })->get(),
]);

?>

<div class="w-11/12 mx-auto grow gap-4 lg:gap-8 py-4 lg:py-8 flex flex-col">
    <div class="flex justify-between items-center">
        <div class="text-5xl lg:text-7xl font-avenir-next-bold text-white">Shop</div>
        @canany(['create-product','update-product'])
        <a href="manage-product" wire:navigate class="text-black py-3 max-sm:hidden uppercase px-6 font-normal bg-white rounded-lg tracking-tight w-min whitespace-nowrap">Manage Product</a>
        @endcanany
    </div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 text-white overflow-y-auto hidden-scrollbar absolute inset-x-0 items-stretch" :style="'height: ' + height + 'px;'">
            @foreach($products as $product)
            <div class="flex flex-col gap-4 h-full">
                <a href="/product/{{ $product->id }}" wire:navigate class="grid h-full backdrop-blur-xl border border-white p-4 rounded-lg gap-2">
                    <div class="font-avenir-next-rounded-bold text-center">
                        {{$product->name}}
                    </div>
                    <div class="w-full">
                        <img class="size-full rounded-lg aspect-square" src="{{ asset('storage/'.$product->thumbnail_path) }}">
                    </div>
                    <div class="flex flex-col mt-auto">
                        <div class="font-avenir-next-rounded-regular">
                            {{Str::limit($product->description, 20)}}
                        </div>
                        <div class="mt-auto">${{$product->price / 100}}</div>
                    </div>
                </a>
                @can('update-product')
                <a href="/manage-product/{{ $product->id }}" wire:navigate class="text-black py-3 bg-white rounded-lg tracking-tight text-center">Update</a>
                @endcan
            </div>
            @endforeach
        </div>
    </div>
</div>