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

<div class="w-3/4 mx-auto">
    @if($role == 'administrator')
    <div class="flex justify-end py-4">
        <a href="/manage-product" wire:navigate class="bg-primary bg-opacity-90 hover:bg-opacity-100 text-white py-3 px-6 uppercase font-avenir-next-rounded-extra-light tracking-wider">Manage Product</a>
    </div>
    @endif
    <div class="my-12">
        <div class="grid grid-cols-5 gap-6">
            @foreach($products as $product)
            <a href="/product/{{ $product->id . ($booking_id ? '/' . $booking_id : '')}}" wire:navigate class="flex flex-col gap-2 group">
                <div class="w-full aspect-square">
                    <img class="size-full group-hover:opacity-85" src="{{asset('storage/'.$product->thumbnail_path)}}">
                </div>
                <div class="flex flex-col">
                    <div class="font-avenir-next-rounded-light text-primary group-hover:underline underline-offset-4">
                        {{$product->name}}
                    </div>
                    <div class="font-avenir-next-rounded-light text-primary group-hover:underline underline-offset-4">
                        {{$product->description}}
                    </div>
                    <div class="mt-auto text-primary font-extralight">${{$product->price}}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>