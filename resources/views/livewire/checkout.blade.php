<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SmsController;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use function Livewire\Volt\{state, with, computed, rules, mount, on};

state(['cart'])->reactive();

state(['first_name', 'last_name', 'email', 'phoneno', 'otp', 'generatedOtp', 'auth', 'address_name', 'shipping_preference', 'address_name', 'address', 'postal_code', 'coupon_code', 'booking_id', 'coupon', 'terminal_status' => 'Submit & Pay']);

with(fn() => ['products' => Product::whereIn('id', $this->cart ? array_keys($this->cart) : [])->when($this->booking_id, function ($query) {
    $query->whereHas('type', function ($typeQuery) {
        $typeQuery->where('name', 'in store');
    });
}, function ($query) {
    $query->whereHas('type', function ($typeQuery) {
        $typeQuery->where('name', 'online');
    });
})->get()]);

on([
    (Auth::user() ? 'echo-private:payment-user-' . Auth::user()->id . ',TerminalPaymentEvent' : '') => function ($request) {
        $this->terminal_status = $request['request']['data']['object']['checkout']['status'];
    },
]);

rules(fn() => [
    'first_name' => $this->auth ? ['exclude'] : ['required'],
    'last_name' => $this->auth ? ['exclude'] : ['required'],
    'email' => $this->auth ? ['exclude'] : ['required', 'email'],
    'phoneno' => $this->auth ? ['exclude'] : ['required'],
    'shipping_preference' => $this->auth ? ['required'] : ['exclude'],
    'address_name' => $this->auth && $this->shipping_preference == 2 ? ['required'] : ['exclude'],
    'address' => $this->auth && $this->shipping_preference == 2 ? ['required'] : ['exclude'],
    'postal_code' => $this->auth && $this->shipping_preference == 2 ? ['required', 'exists:postal_codes,postcode'] : ['exclude'],
])->messages([
    'address_name.required_if' => 'The :attribute is required.',
    'address.required_if' => 'The :attribute is required.',
    'postal_code.required_if' => 'The :attribute is required.',
]);

$verifyOtp = function (Request $request) {
    $this->validate();
    $this->resetValidation();

    if (App::call([SmsController::class, 'verifyOtp'], ['id' => $this->generatedOtp->id, 'userOtp' => $this->otp])) {

        if (User::where('phoneno', $this->phoneno)->doesntExist()) {
            $user = User::Create(
                [
                    'email' => $this->email,
                    'phoneno' => $this->phoneno,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'password' => Hash::make('12345678'),
                ]
            );
        }
        if (
            Auth::attempt([
                'email' => $this->email,
                'password' => '12345678',
            ])
        ) {
            $request->session()->regenerate();
            $this->dispatch('reload');
        }
    } else {
        $this->addError('otp', 'Confirmation Code is invalid');
    }
    $this->reset(['first_name', 'last_name', 'email', 'phoneno', 'otp', 'generatedOtp']);
    $this->dispatch('reset');
};

$submit = function () {
    $this->validate();

    if (User::where('phoneno', $this->phoneno)->exists() && User::where('phoneno', $this->phoneno)->first()->email != $this->email) {
        $this->addError('phoneno', 'This phone no is already taken with another email.');
        return;
    } elseif (User::where('email', $this->email)->exists() && User::where('email', $this->email)->first()->phoneno != $this->phoneno) {
        $this->addError('email', 'This email is already taken with another phone no.');
        return;
    }

    $this->generatedOtp = App::call([SmsController::class, 'generateOtp']);
    App::call([SmsController::class, 'send'], ['phoneno' => env('TWILIO_PHONE_COUNTRY_CODE') . $this->phoneno, 'message' => 'Your otp is ' . $this->generatedOtp->otp . '.']);
    $this->dispatch('show-toastr', type: 'success', message: 'A code has been sent to this number');
    $this->dispatch('start-countdown');
};

$submitAndPay = function () {
    $this->validate();

    if ($this->booking_id) {
        $this->terminal_status = 'processing';
        App::call([PaymentController::class, 'terminalPayment'], ['cart' => $this->cart, 'user' => $this->auth, 'coupon' => $this->coupon]);
    } else {
        App::call([PaymentController::class, 'onlinePayment'], ['cart' => $this->cart, 'user' => $this->auth, 'coupon' => $this->coupon]);
    }
};

$total = computed(function () {
    return Product::whereIn('id', $this->cart ? array_keys($this->cart) : [])->when($this->booking_id, function ($query) {
        $query->whereHas('type', function ($typeQuery) {
            $typeQuery->where('name', 'in store');
        });
    }, function ($query) {
        $query->whereHas('type', function ($typeQuery) {
            $typeQuery->where('name', 'online');
        });
    })->get()->map(function ($item) {
        return $item->price * $this->cart[$item->id];
    })->sum();
});

$discount = computed(function () {
    return $this->coupon ? ((100 - $this->coupon->discount_value) * 0.01) : null;
});

$validateCouponCode = function () {
    $total = $this->total;
    $coupon = $this->coupon_code;
    return $this->auth->issuedcoupons()
        ->whereHas('coupon', function (Builder $query) use ($coupon, $total) {
            $query->where('status', true)->where('name', $coupon)->where('min_cart_value', '<=', $total);
        })
        ->where('is_used', false)
        ->when(Coupon::where('name', $coupon)->exists(), function ($query) use ($coupon) {
            $query->where('created_at', '>=', now()->subDays(Coupon::where('name', $coupon)->first()->validity));
        })->exists();
};

$submitCoupon = function () {
    $this->validate(['coupon_code' => ['nullable', function (string $attribute, mixed $value, Closure $fail) {
        if (!$this->validateCouponCode()) {
            $fail("The :attribute is invalid.");
        }
    },]]);

    $this->coupon = Coupon::where('name', $this->coupon_code)->first();
};

mount(function () {
    $this->auth = Auth::user();
    $this->booking_id = request()->route('booking_id');
});
?>

<div class="grow flex flex-col gap-8 py-8 text-white w-11/12 mx-auto">
    <div class="text-7xl font-avenir-next-bold text-white">Cart</div>
    <div class="flex justify-between gap-12 grow">
        <div class="w-full py-12 flex flex-col backdrop-blur-xl border border-white rounded-lg">
            <div class="w-4/5 mx-auto relative grow" x-data="{ height: 0 }" x-resize="height = $height">
                <div class="overflow-y-auto absolute inset-x-0" :style="'height: ' + height + 'px;'">
                    @if(!$auth)
                    <form x-data="otp" x-on:reset="reset()" x-on:start-countdown.window="startCountdown()" wire:submit="submit" class="h-min grid grid-cols-1 gap-8 mx-auto font-avenir-next-rounded-light">
                        <div>
                            <label class="font-avenir-next-rounded-semibold text-xl">First Name</label>
                            <input wire:model="first_name" class="w-full bg-black/5 outline-none p-3" placeholder="First Name">
                            <div>
                                @error('first_name')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="font-avenir-next-rounded-semibold text-xl">Last Name</label>
                            <input wire:model="last_name" class="w-full bg-black/5 outline-none p-3" placeholder="Last Name">
                            <div>
                                @error('last_name')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="font-avenir-next-rounded-semibold text-xl">Email</label>
                            <input wire:model="email" class="w-full bg-black/5 outline-none p-3" placeholder="Email">
                            <div>
                                @error('email')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="font-avenir-next-rounded-semibold text-xl">Phone No</label>
                            <input wire:model="phoneno" x-mask="9999999999" class="w-full bg-black/5 outline-none p-3" placeholder="Phone No">
                            <div>
                                @error('phoneno')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="font-avenir-next-rounded-semibold text-xl">Confirmation Code</label>
                            <input @input="verifyOtp" wire:model="otp" x-mask="999999" class="@if(!$generatedOtp) pointer-events-none opacity-50 @endif w-full bg-black/5 outline-none p-3" placeholder="Confirmation Code">
                            <div class="w-1/2 mx-auto">
                                @error('otp')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                                @enderror
                            </div>
                            @if($generatedOtp)
                            <div :class="formattedTime == '00:00' && 'text-red-500'" class="w-1/2" x-text="formattedTime == '00:00' ? 'Otp Timed Out' : formattedTime"></div>
                            @endif
                        </div>
                        <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight w-min mx-auto whitespace-nowrap">{{$generatedOtp ? 'Resend Code' : 'Send Code'}}</button>
                    </form>
                    @else
                    <form wire:submit="submitAndPay" class="h-min grid grid-cols-1 gap-8 w-4/5 mx-auto py-12 font-avenir-next-rounded-light">
                        <div class="font-avenir-next-rounded-regular text-lg">Shipping Preference</div>
                        <div class="flex justify-evenly">
                            <div :class="$wire.shipping_preference == 1 && 'border-blue-500'" @click="$wire.shipping_preference = 1;" class="cursor-pointer flex items-center gap-4 border rounded-md py-2 px-4">
                                <div>
                                    <input type="radio" value="1" wire:model="shipping_preference">
                                </div>
                                <div>Pickup</div>
                            </div>
                            <div :class="$wire.shipping_preference == 2 && 'border-blue-500'" @click="$wire.shipping_preference = 2;" class="cursor-pointer flex items-center gap-4 border rounded-md py-2 px-4">
                                <div>
                                    <input x-ref="shipping_preference-deliver" type="radio" value="2" wire:model="shipping_preference">
                                </div>
                                <div>Deliver</div>
                            </div>
                        </div>
                        @error('shipping_preference')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                        @enderror
                        <div x-show="$wire.shipping_preference == 2">
                            <label class="font-avenir-next-rounded-semibold text-xl">Address Name</label>
                            <input wire:model="address_name" class="w-full bg-black/5 outline-none p-3" placeholder="Address Name">
                            <div>
                                @error('address_name')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div x-show="$wire.shipping_preference == 2">
                            <label class="font-avenir-next-rounded-semibold text-xl">Address</label>
                            <textarea wire:model="address" class="w-full bg-black/5 outline-none p-3" placeholder="Address"></textarea>
                            <div>
                                @error('address')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div x-show="$wire.shipping_preference == 2">
                            <label class="font-avenir-next-rounded-semibold text-xl">Postal Code</label>
                            <input wire:model="postal_code" x-mask="999999" class="w-full bg-black/5 outline-none p-3" placeholder="Postal Code">
                            <div>
                                @error('postal_code')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" :class="$wire.terminal_status == 'processing' && 'pointer-events-none'" class="relative uppercase text-center py-2 px-4 bg-white mx-auto text-black rounded-lg">
                            <div wire:loading.class="invisible" wire:target="submitAndPay">{{ str_replace("_", " ", $terminal_status) }}</div>
                            <div wire:loading.class.remove="invisible" wire:target="submitAndPay" class="invisible absolute inset-0 p-2">
                                <svg aria-hidden="true" class="size-full text-transparent animate-spin fill-black" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                                </svg>
                            </div>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2 p-12 font-avenir-next-rounded-semibold w-full backdrop-blur-xl border border-white rounded-lg">
            <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
                <div class="overflow-y-auto absolute inset-x-0 py-8 flex flex-col gap-12" :style="'height: ' + height + 'px;'">
                    @foreach($products as $product)
                    <div class="flex">
                        <div class="flex flex-1 gap-4">
                            <div class="w-16 aspect-square relative">
                                <img class="size-full rounded-lg" src="{{asset('storage/'.$product->thumbnail_path)}}">
                                <div class="absolute -top-4 -right-4 text-black bg-white rounded-full text-xs size-6 aspect-square flex justify-center items-center">{{ $cart[$product->id] }}</div>
                            </div>
                            <div class="flex flex-col">
                                <div>{{ $product->name }}</div>
                                <div>{{ $product->description }}</div>
                            </div>
                        </div>
                        <div>
                            $ {{ $product->price * $cart[$product->id]}}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="flex flex-col gap-2">
                @if($auth)
                <form wire:submit="submitCoupon" class="flex gap-4">
                    <input wire:model="coupon_code" class="w-full bg-black/5 outline-none p-3" placeholder="Coupon Code">
                    <button class="bg-black/5 outline-none p-3 font-avenir-next-rounded-semibold">Apply</button>
                </form>
                <div>
                    @error('coupon_code')
                    <span wire:transition.in.duration.500ms="scale-y-100"
                        wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                    @enderror
                </div>
                @endif
                <div class="flex justify-between text-xl">
                    <div>Total</div>
                    <div class="flex justify-end"> {{ $this->discount ? ('$ ' . $this->total . ' * ' . $this->coupon->discount_value . '%  =  $ ' . $this->total * $this->discount ) : '$ ' . $this->total}}</div>
                </div>
            </div>
        </div>
    </div>
</div>