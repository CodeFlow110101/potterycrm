<?php

use App\Http\Controllers\PaymentController;
use App\Models\PostalCode;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

use Square\Models\Order;

use function Livewire\Volt\{state, with, mount, computed, on};

state(['role', 'search', 'booking_id']);

with(fn() => [
    'products' => Product::whereRaw("LOWER(REPLACE(name, ' ', '')) LIKE ?", ['%' . strtolower(str_replace(' ', '', $this->search)) . '%'])->when($this->booking_id, function ($query) {
        $query->whereHas('type', function ($typeQuery) {
            $typeQuery->where('name', 'in store');
        });
    }, function ($query) {
        $query->whereHas('type', function ($typeQuery) {
            $typeQuery->where('name', 'online');
        });
    })->get(),
]);

$proceedToPayment = function () {
    if ($this->booking_id) {
        App::call([PaymentController::class, 'terminalPayment'], ['cart' => $this->cart, 'user' => $this->user]);
        $this->modal = true;
    } else {
        App::call([PaymentController::class, 'onlinePayment'], ['cart' => $this->cart, 'user' => $this->user]);
    }
};

mount(function ($auth) {
    $this->booking_id = request()->route('booking_id');
    if ($auth) {
        $this->role = $auth->role->name;
    }
});
?>

<div class="w-11/12 mx-auto grow py-4 flex flex-col">
    <div class="flex justify-between items-center py-4">
        <div class="text-7xl font-avenir-next-bold text-white">Shop</div>
        @if($role == 'administrator')
        <a href="/manage-product" wire:navigate class="text-black py-3 uppercase px-6 bg-white rounded-lg tracking-tight">Manage Product</a>
        @endif
    </div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="grid grid-cols-5 gap-6 text-white overflow-y-auto hidden-scrollbar absolute inset-x-0" :style="'height: ' + height + 'px;'">
            @foreach($products as $product)
            <a href=" /product/{{ $product->id . ($booking_id ? '/' . $booking_id : '')}}" wire:navigate class="flex flex-col h-min backdrop-blur-xl border border-white p-4 rounded-lg gap-2">
                <div class="font-avenir-next-rounded-bold text-center">
                    {{$product->name}}
                </div>
                <div class="w-full aspect-square">
                    <img class="size-full rounded-lg" src="{{asset('storage/'.$product->thumbnail_path)}}">
                </div>
                <div class="flex flex-col">
                    <div class="font-avenir-next-rounded-regular">
                        {{$product->description}}
                    </div>
                    <div class="mt-auto">${{$product->price}}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>