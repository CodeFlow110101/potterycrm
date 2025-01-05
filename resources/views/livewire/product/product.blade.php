<?php

use App\Events\PaymentSuccessEvent;
use App\Http\Controllers\PaymentController;
use App\Models\PostalCode;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Square\SquareClient;
use Square\Models\Money;
use Illuminate\Support\Facades\App;
use Square\Models\QuickPay;

use Square\Models\Order;

use function Livewire\Volt\{state, with, mount, computed, on};

state(['user' => User::with(['addresses'])->find(Auth::user()->id), 'cart' => [], 'shippingType', 'search', 'address', 'booking_id', 'modal' => false, 'terminal_status' => "Please complete the payment through the device."]);

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

on(['echo-private:payment-user-{user.id},TerminalPaymentEvent' => function ($request) {
    $this->terminal_status = $request['request']['data']['object']['checkout']['status'];
}]);

$increaseQuantity = function ($id) {
    $this->cart[$id]++;
};

$decreaseQuantity = function ($id) {
    if ($this->cart[$id] - 1 == 0) {
        unset($this->cart[$id]);
    } else {
        $this->cart[$id]--;
    }
};

$addProduct = function ($id) {
    $this->cart[$id] = 1;
};

$removeProduct = function ($id) {
    unset($this->cart[$id]);
};

$totals = computed(function () {
    $subTotal = 0;
    foreach (Product::whereIn('id', array_keys($this->cart))->get() as $product) {
        $subTotal += $product->price * $this->cart[$product->id];
    }
    $discount = $subTotal * 0.05;
    $salesTax = $subTotal * 0.02;
    $total = ($subTotal + $salesTax) - $discount;

    return ['subTotal' => $subTotal, 'discount' => $discount, 'salesTax' => $salesTax, 'total' => $total];
});

$proceedToPayment = function () {
    if ($this->booking_id) {
        App::call([PaymentController::class, 'terminalPayment'], ['cart' => $this->cart, 'user' => $this->user]);
        $this->modal = true;
    } else {
        App::call([PaymentController::class, 'onlinePayment'], ['cart' => $this->cart, 'user' => $this->user]);
    }
};

mount(function ($id) {
    $this->booking_id = $id;
});
?>

<div x-data="{ show : 'cart' }" class="grow flex justify-between bg-black/5">
    <div class="h-full w-4/6 flex flex-col p-4">
        <div class="py-12 flex justify-between items-center">
            <div class="w-full flex flex-col gap-2">
                <div class="text-2xl font-medium text-black/80">
                    Welcome, {{$user->first_name}}
                </div>
                <div class="text-black/40 text-sm font-medium">Discover Whatever you need easily</div>
            </div>
            <div class="w-full flex justify-end">
                <div class="w-4/5 relative">
                    <input wire:model.live="search" class="w-full bg-white rounded-md outline-none pl-12 py-3 pr-3" placeholder="Search Product ..." />
                    <div class="absolute inset-y-0 flex items-center px-3">
                        <svg class="w-6 h-6 text-black/40" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-6 py-4 overflow-y-auto max-h-[75vh] mb-auto">
            <a href="/manage-product" wire:navigate class="w-full h-[40vh] flex flex-col rounded-lg border shadow-lg border-black/30 p-6">
                <div class="m-auto text-right text-2xl">
                    <svg class="w-12 h-12 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                    </svg>
                </div>
            </a>
            @foreach($products as $product)
            <div class="w-full h-[40vh] bg-cover bg-center flex flex-col gap-2 rounded-lg border shadow-lg border-black/30 p-3">
                <div class="w-full h-1/2 relative">
                    <img class="size-full rounded-lg" src="{{asset('storage/'.$product->thumbnail_path)}}">
                    <div x-show="show == 'cart'" class="absolute inset-0 p-2 flex justify-end">
                        @if(array_key_exists($product->id, $cart))
                        <button wire:click="removeProduct({{$product->id}})" class="size-min rounded-md bg-white p-1 opacity-60">
                            <svg class="w-6 h-6 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                            </svg>
                        </button>
                        @else
                        <button wire:click="addProduct({{$product->id}})" class="size-min rounded-md bg-white p-1 opacity-60">
                            <svg class="w-6 h-6 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
                <div class="grow flex flex-col py-2 text-xl">
                    <div class="text-black/80">
                        {{$product->name}}
                    </div>
                    <div class="mt-auto text-primary font-medium">${{$product->price}}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="w-2/6 h-full flex flex-col gap-4 bg-white px-8 pb-8 pt-16">
        <div>
            <div x-show="show == 'cart'" class="text-2xl font-medium text-black/60">Cart</div>
            <div x-show="show == 'address'" class="text-2xl font-medium text-black/60">Select Shipping Preference</div>
        </div>
        <div x-show="show == 'cart'" class="max-h-[42vh] overflow-y-auto my-auto rounded-xl p-2">
            <div class="flex flex-col justify-start gap-5">
                @if(count($cart) == 0)
                <div class="text-center font-semibold text-black/40 text-sm">There are no items in items in your Cart.</div>
                @else
                @foreach($products as $product)
                @if(array_key_exists($product->id, $cart))
                <div class="flex justify-between items-center gap-2 h-[10vh]">
                    <div class="w-1/4 h-full">
                        <img class="h-full w-full rounded-lg" src="{{asset('storage/'.$product->thumbnail_path)}}">
                    </div>
                    <div class="w-3/4 h-full flex flex-col">
                        <div class="text-lg text-black/60">{{$product->name}}</div>
                        <div class="flex justify-between mt-auto">
                            <div class="text-primary text-lg font-medium">${{$product->price}}</div>
                            <div class="w-min flex justify-evenly items-center gap-3">
                                <button wire:click="increaseQuantity({{$product->id}})" class="bg-primary rounded-md aspect-square py-1 px-2 text-white text-sm">+</button>
                                <div class="text-black/60">{{$cart[$product->id]}}</div>
                                <button wire:click="decreaseQuantity({{$product->id}})" class="bg-primary rounded-md aspect-square py-1 px-2 text-white text-sm">-</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
                @endif
            </div>
        </div>
        <div x-show="show == 'address'" class="mb-auto">
            <div class="flex justify-between items-center gap-4">
                <button @click="$refs.pickupRadioButton.click(); $wire.address = null" :class="$wire.shippingType == 0 ? 'border-blue-500' : 'border-black/60'" class="w-full flex justify-start items-center gap-4 border rounded-lg py-2 px-3">
                    <input class="hidden" x-ref="pickupRadioButton" type="radio" value="0" wire:model="shippingType">
                    <div class="p-0.5 rounded-full border border-black/30">
                        <div :class="$wire.shippingType == 0 && 'bg-blue-500'" class="rounded-full size-3"></div>
                    </div>
                    <div class="font-semibold text-black/60">Pickup</div>
                </button>
                <button @click="$refs.deliverRadioButton.click()" :class="$wire.shippingType == 1 ? 'border-blue-500' : 'border-black/60'" class="w-full flex justify-start items-center gap-4 border rounded-lg py-2 px-3">
                    <input class="hidden" x-ref="deliverRadioButton" type="radio" value="1" wire:model="shippingType">
                    <div class="p-0.5 rounded-full border border-black/30">
                        <div :class="$wire.shippingType == 1 && 'bg-blue-500'" class="rounded-full  size-3"></div>
                    </div>
                    <div class="font-semibold text-black/60">Deliver</div>
                </button>
            </div>
            <div class="py-6">
                <div x-show="$wire.shippingType == 1" class="flex flex-col gap-2">
                    <div class="text-black/60 font-semibold">Select an Address</div>
                    <div class="overflow-y-auto max-h-[50vh] flex flex-col gap-4">
                        @foreach($user->addresses as $address)
                        <button @click="$refs.{{$address->name}}.click()" :class="$wire.address == {{$address->id}} ? 'border-blue-500' : 'border-black/30'" class="border-2 rounded-md flex justify-start gap-2 items-center p-2">
                            <div class="p-0.5 rounded-full border border-black/30">
                                <div :class="$wire.address == {{$address->id}} && 'bg-blue-500'" class="rounded-full  size-3"></div>
                            </div>
                            <div class="flex flex-col ">
                                <div class="font-medium flex justify-start gap-2">
                                    <div>{{$address->name}}</div>
                                </div>
                                <div class="text-sm text-black/60">{{Str::limit($address->address, 50)}}</div>
                            </div>
                            <input class="hidden" x-ref="{{$address->name}}" type="radio" value="{{$address->id}}" wire:model="address">
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div x-show="show == 'cart'" class="mt-auto flex flex-col gap-4">
            <div class="bg-black/5 rounded-lg">
                <div class="flex flex-col gap-3 p-6 capitalize">
                    <div class="flex justify-between items-center">
                        <div class="text-black/50">Subotal</div>
                        <div>${{$this->totals['subTotal']}}</div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="text-black/50">discount</div>
                        <div>-${{$this->totals['discount']}}</div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="text-black/50">sales tax</div>
                        <div>${{$this->totals['salesTax']}}</div>
                    </div>
                </div>
                <div class="relative h-6 flex items-center">
                    <div class="absolute inset-0 flex justify-between">
                        <div class="bg-white h-full aspect-square -translate-x-1/2 rounded-full"></div>
                        <div class="bg-white h-full aspect-square translate-x-1/2 rounded-full"></div>
                    </div>
                    <div class="border border-dashed border-black/50 w-full h-0"></div>
                </div>
                <div class="flex justify-between items-center p-4 text-xl">
                    <div>Total</div>
                    <div>${{$this->totals['total']}}</div>
                </div>
            </div>
        </div>
        <div class="flex justify-between items-center gap-4">
            <button x-cloak x-show="show == 'address'" @click="show = 'cart'; $wire.address = null" class="bg-primary font-medium w-min text-center p-3 text-lg rounded-md text-white">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4" />
                </svg>
            </button>
            <button x-show="show == 'cart'" @click="show = 'address'" :class="$wire.cart.length == 0 && 'pointer-events-none opacity-60'" class="bg-primary font-medium w-full text-center p-3 text-lg rounded-md text-white">Select Delivery Preference</button>
            <button x-show="show == 'address'" wire:click="proceedToPayment" :class="$wire.cart.length == 0 && 'pointer-events-none opacity-60'" wire:loading.class="pointer-events-none py-2.5" wire:loading.class.remove="py-3" wire:target="proceedToPayment" class="bg-primary font-medium w-full text-center py-3 text-lg rounded-md text-white flex items-center justify-center">
                <div wire:target="proceedToPayment" wire:loading.remove>Proceed to Payment</div>
                <div wire:target="proceedToPayment" wire:loading class="mx-auto">
                    <svg aria-hidden="true" class="w-8 h-8 text-transparent animate-spin fill-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                    </svg>
                </div>
            </button>
        </div>
    </div>

    @if($this->modal)
    <div class="fixed inset-0 flex flex-col">
        <div class="bg-white m-auto p-10 rounded-lg relative shadow-lg">
            <div class="text-black/50 text-lg capitalize">{{ str_replace('_' , ' ' , $terminal_status) }}</div>
            <div class="absolute top-0 inset-0 flex justify-center">
                <div class="flex justify-center bg-white rounded-full size-14 p-1 -translate-y-1/2 shadow-lg">
                    <svg class="size-full text-black/50" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M7 6a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-2v-4a3 3 0 0 0-3-3H7V6Z" clip-rule="evenodd" />
                        <path fill-rule="evenodd" d="M2 11a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-7Zm7.5 1a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z" clip-rule="evenodd" />
                        <path d="M10.5 14.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>