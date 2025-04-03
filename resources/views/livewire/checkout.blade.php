<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SmsController;
use App\Models\Booking;
use App\Models\Checkout;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

use function Livewire\Volt\{state, with, computed, rules, mount, on, updated};

state(['cart'])->reactive();

state(['first_name', 'last_name', 'email', 'phoneno', 'otp', 'generatedOtp', 'auth', 'coupon_code', 'booking_id', 'coupon', 'checkout_for', 'checkout_no']);

with(fn() => [
    'products' => Product::whereIn('id', $this->cart ? array_keys($this->cart) : [])->get(),
    'bookings' => Booking::with(['user'])->where('status_id', 3)->whereHas('timeSlot.date', function (Builder $query) {
        $query->where('date', Carbon::today()->format('Y-m-d'));
    })->get(),
]);

rules(fn() => [
    'first_name' => $this->auth ? ['exclude'] : ['required'],
    'last_name' => $this->auth ? ['exclude'] : ['required'],
    'email' => $this->auth ? ['exclude'] : ['required', 'email'],
    'phoneno' => $this->auth ? ['exclude'] : ['required', function ($attribute, $value, $fail) {
        Gate::allows('valid-phone-number', $this->phoneno) || $fail('The :attribute must be in this format ' . env('TWILIO_PHONE_COUNTRY_CODE') . ' ' . Str::replace('9', 'X', env('PHONE_NUMBER_VALIDATION_PATTERN')));
    }],
    'checkout_for' => $this->auth && Gate::allows('hardware-checkout-user') ? ['required'] : ['exclude'],
    'booking_id' => $this->auth && $this->checkout_for == 1 && Gate::allows('hardware-checkout-user') ? ['required'] : ['exclude'],
    'cart' => [
        'required',
        'array',
        'min:1',
        function ($attribute, $value, $fail) {
            Product::where('id', array_keys($this->cart))->onlyTrashed()->exists() && $fail('Some of the items are out of stock in your :attribute');
        },
    ],
])->messages([
    'checkout_for.required' => 'Please select an option.',
]);

updated(['checkout_for' => fn() => $this->checkout_for == "1" && $this->reset('booking_id')]);

$url = computed(function () {

    if (collect($this->cart)->isNotEmpty() && ($this->checkout_for == 2 || ($this->checkout_for == 1 && $this->booking_id)) && Gate::allows('hardware-checkout-user')) {

        $this->checkout_no = Checkout::updateOrCreate(
            ['id' => $this->checkout_no], // Find by this condition
            [
                'user_id' => $this->booking_id ? Booking::find($this->booking_id)->user->id : $this->auth->id,
                'coupon_id' => $this->coupon ? $this->coupon->id : 0,
                'cart' => json_encode($this->cart),
            ]
        )->id;

        return App::call([PaymentController::class, 'hardwarePayment'], ['amount' => $this->coupon ? $this->total * $this->discount : $this->total, 'checkout_id' => $this->checkout_no]);
    }
});

$trimmed_phoneno = computed(function () {
    return trim(Str::replaceFirst(env('TWILIO_PHONE_COUNTRY_CODE'), '', $this->phoneno));
});

$verifyOtp = function (Request $request) {
    $this->validate();
    $this->resetValidation();

    if (App::call([SmsController::class, 'verifyOtp'], ['id' => $this->generatedOtp->id, 'userOtp' => $this->otp])) {

        if (User::where('phoneno', $this->trimmed_phoneno)->doesntExist()) {
            $user = User::Create(
                [
                    'email' => $this->email,
                    'phoneno' => $this->trimmed_phoneno,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'role_id' => 2,
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

    if (User::where('phoneno', $this->trimmed_phoneno)->exists() && User::where('phoneno', $this->trimmed_phoneno)->first()->email != $this->email) {
        $this->addError('phoneno', 'This phone no is already taken with another email.');
        return;
    } elseif (User::where('email', $this->email)->exists() && User::where('email', $this->email)->first()->phoneno != $this->trimmed_phoneno) {
        $this->addError('email', 'This email is already taken with another phone no.');
        return;
    }

    $this->generatedOtp = App::call([SmsController::class, 'generateOtp'], ['phoneno' => $this->trimmed_phoneno]);
    $this->dispatch('start-countdown');
};

$submitAndPay = function () {
    $this->validate();
    App::call([PaymentController::class, 'onlinePayment'], ['cart' => $this->cart, 'user' => $this->auth, 'coupon' => $this->coupon]);
};

$total = computed(function () {
    return Product::whereIn('id', $this->cart ? array_keys($this->cart) : [])->get()->map(function ($item) {
        return $item->price * $this->cart[$item->id] / 100;
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

mount(function (Request $request) {
    $this->auth = Auth::user();
});
?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="flex justify-between items-center">
        <div class="text-5xl lg:text-7xl font-avenir-next-bold">Checkout</div>
        <a href="cart" wire:navigate class="text-black py-2 sm:py-3 uppercase px-4 sm:px-6 bg-white rounded-lg tracking-tight flex items-center gap-2 max-sm:text-sm sm:gap-4">
            <div>
                <svg class="w-6 h-6 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4" />
                </svg>
            </div>
            <div>
                cart
            </div>
        </a>
    </div>
    <div class="flex max-sm:flex-col sm:justify-between gap-4 sm:gap-12 grow">
        <div class="w-full py-12 grow flex flex-col backdrop-blur-xl border border-white rounded-lg">
            <div class="w-4/5 mx-auto relative grow" x-data="{ height: 0 }" x-resize="height = $height">
                <div class="overflow-y-auto absolute inset-x-0 hidden-scrollbar" :style="'height: ' + height + 'px;'">
                    @if(!$auth)
                    <form x-data="otp" x-on:reset="reset()" x-on:start-countdown.window="startCountdown()" wire:submit="submit" class="h-min grid grid-cols-1 gap-8 mx-auto font-avenir-next-rounded-light">
                        <div>
                            <label class="font-avenir-next-rounded-semibold text-xl">First Name</label>
                            <input wire:model="first_name" class="w-full bg-black/5 outline-none p-3" placeholder="First Name">
                            <div>
                                @error('first_name')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="font-avenir-next-rounded-semibold text-xl">Last Name</label>
                            <input wire:model="last_name" class="w-full bg-black/5 outline-none p-3" placeholder="Last Name">
                            <div>
                                @error('last_name')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="font-avenir-next-rounded-semibold text-xl">Email</label>
                            <input wire:model="email" class="w-full bg-black/5 outline-none p-3" placeholder="Email">
                            <div>
                                @error('email')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="font-avenir-next-rounded-semibold text-xl">Phone No</label>
                            <input wire:model="phoneno" x-mask="{{ env('TWILIO_PHONE_COUNTRY_CODE') }} {{ env('PHONE_NUMBER_VALIDATION_PATTERN') }}" class="w-full bg-black/5 outline-none p-3" placeholder="eg {{ env('TWILIO_PHONE_COUNTRY_CODE') }} {{ Str::replace('9', 'X', env('PHONE_NUMBER_VALIDATION_PATTERN')) }}">
                            <div>
                                @error('phoneno')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="font-avenir-next-rounded-semibold text-xl">Confirmation Code</label>
                            <input @input="verifyOtp" wire:model="otp" x-mask="999999" class="@if(!$generatedOtp) pointer-events-none opacity-50 @endif w-full bg-black/5 outline-none p-3" placeholder="Confirmation Code">
                            <div class="w-1/2 mx-auto">
                                @error('otp')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                                @enderror
                            </div>
                            @if($generatedOtp)
                            <div :class="formattedTime == '00:00' && 'text-red-500'" class="w-1/2" x-text="formattedTime == '00:00' ? 'Otp Timed Out' : formattedTime"></div>
                            @endif
                        </div>
                        <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight w-min mx-auto whitespace-nowrap">{{$generatedOtp ? 'Resend Code' : 'Send Code'}}</button>
                    </form>
                    @else
                    <form wire:submit="submitAndPay" class="h-min flex flex-col grow gap-8 mx-auto font-avenir-next-rounded-light">
                        @can('hardware-checkout-user')
                        <div class="flex justify-around items-center">
                            <div @click="$refs.selectbooking.click()" class="cursor-pointer rounded-md border border-white flex items-center gap-4 p-2">
                                <input x-ref="selectbooking" wire:model.live="checkout_for" value="1" class="size-4 outline-none" type="radio">
                                <div class="font-bold">Select Booking</div>
                            </div>
                            <div @click="$refs.checkoutforself.click()" class="cursor-pointer rounded-md border border-white flex items-center gap-4 p-2">
                                <input x-ref="checkoutforself" wire:model.live="checkout_for" value="2" class="size-4 outline-none" type="radio">
                                <div class="font-bold">Checkout for Self</div>
                            </div>
                        </div>
                        <div>
                            @error('checkout_for')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                            @enderror
                        </div>
                        @if($checkout_for == 1)
                        <div class="flex flex-col gap-2">
                            <label class="font-avenir-next-rounded-semibold text-xl">Select Booking</label>
                            <select wire:model.live="booking_id" class="w-full bg-black/5 outline-none p-3">
                                <option value="">Select a Booking by Customer Name</option>
                                @foreach($bookings as $booking)
                                <option value="{{ $booking->id }}">{{ $booking->user->first_name . ' ' . $booking->user->last_name }}</option>
                                @endforeach
                            </select>
                            <div>
                                @error('booking_id')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @endif
                        @endcan
                        @can('online-checkout-user')
                        <div class="flex flex-col gap-2">
                            <div>
                                Thank you for choosing Icona Pottery Cafe! We can’t wait to see your creativity come to life.
                            </div>
                            <div>
                                Please make sure to call {{ env('TWILIO_PHONE_COUNTRY_CODE') . ' ' . env('ADMIN_PHONE_NO') }} to book a time to pick up your package.
                            </div>
                        </div>
                        <button type="submit" class="relative uppercase text-center py-2 px-4 bg-white mx-auto text-black rounded-lg mt-auto">
                            <div wire:loading.class="invisible" wire:target="submitAndPay">Submit & Pay</div>
                            <div wire:loading.class.remove="invisible" wire:target="submitAndPay" class="invisible absolute inset-0 p-2">
                                <svg aria-hidden="true" class="size-full text-transparent animate-spin fill-black" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                                </svg>
                            </div>
                        </button>
                        @endcan
                        @can('hardware-checkout-user')
                        <a class="text-black py-3 uppercase px-4 mx-auto bg-white rounded-lg tracking-tight w-min whitespace-nowrap @if(!$this->url) opacity-50 pointer-events-none @endif" href="{{ $this->url }}">Submit & Pay</a>
                        @endcan
                    </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2 p-12 font-avenir-next-rounded-semibold w-full backdrop-blur-xl border border-white rounded-lg">
            <div class="max-sm:hidden grow relative" x-data="{ height: 0 }" x-resize="height = $height">
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
                            $ {{ $product->price * $cart[$product->id] / 100}}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                @error('cart')
                <span wire:transition.in.duration.500ms="scale-y-100"
                    wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                @enderror
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
                        wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
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