<?php

use App\Http\Controllers\SmsController;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use function Livewire\Volt\{state, rules, with};

state(['first_name', 'last_name', 'email', 'phoneno', 'otp', 'generatedOtp', 'noofpeople', 'booking_date_time']);

rules(['first_name' => 'required', 'last_name' => 'required', 'email' => 'required|email', 'phoneno' => 'required', 'booking_date_time' => "required"]);

$verifyOtp = function () {

    $this->validate();
    $this->resetValidation();

    if (App::call([SmsController::class, 'verifyOtp'], ['id' => $this->generatedOtp->id, 'userOtp' => $this->otp])) {

        if (User::where('phoneno', $this->phoneno)->exists()) {
            User::where('phoneno', $this->phoneno)->first()->bookings()->create([
                'status_id' => 1,
                'no_of_people' => $this->noofpeople,
                'booking_datetime' => Carbon::createFromFormat('h:i A | d-m-Y', $this->booking_date_time)->format('Y-m-d H:i:s'),
            ]);
        } else {
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

            $user->bookings()->create([
                'status_id' => 1,
                'no_of_people' => $this->noofpeople,
                'booking_datetime' => Carbon::createFromFormat('h:i A | d-m-Y', $this->booking_date_time)->format('Y-m-d H:i:s'),
            ]);
        }
        $this->dispatch('show-toastr', type: 'success', message: 'Your booking has been successfully registered');
        App::call([SmsController::class, 'send'], ['phoneno' => env('TWILIO_PHONE_COUNTRY_CODE') . $this->phoneno, 'message' => 'Your otp is ' . $this->generatedOtp->otp . '.']);
        $this->reset();
        $this->dispatch('reset');
    } else {
        $this->addError('otp', 'Confirmation Code is invalid');
    }
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

?>

<div x-data="otp" x-on:reset="reset()" x-on:start-countdown="startCountdown()" class="h-screen flex justify-center items-center">
    <div class="bg-white w-1/2 rounded-xl">
        <form wire:submit="submit" class="border border-primary rounded-xl py-10">
            <div class="h-min grid grid-cols-1 gap-5 w-4/5 mx-auto">
                <div class="text-4xl text-primary text-center">Book a Table</div>
                <div class="flex justify-between gap-4">
                    <div class="h-min grid grid-cols-1 gap-2 w-full">
                        <input wire:model="first_name" class="p-2 w-full rounded-md outline-none border border-primary" placeholder="First name">
                        <div>
                            @error('first_name')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="h-min grid grid-cols-1 gap-2 w-full">
                        <input wire:model="last_name" class="p-2 w-full rounded-md outline-none border border-primary" placeholder="Last name">
                        <div>
                            @error('last_name')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="h-min flex justify-between gap-4 w-full">
                    <div class="w-full">
                        <input wire:model="email" class="p-2 w-full rounded-md outline-none border border-primary" placeholder="Email">
                        <div>
                            @error('email')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div x-data x-init="flatpickr($refs.input, { dateFormat: 'H:i K d-m-Y', enableTime: true , time_24hr: false, })" class="w-full">
                        <input wire:model="booking_date_time" x-ref="input" type="text" class="p-2 w-full rounded-md outline-none border border-primary" placeholder="Booking Date & Time" />
                        @error('booking_date_time')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-between gap-4">
                    <div class="w-full">
                        <div>
                            <input wire:model="phoneno" x-mask="9999999999" class="p-2 w-full rounded-md outline-none border border-primary" placeholder="Phone No">
                        </div>
                        <div>
                            @error('phoneno')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="w-full">
                        <div>
                            <input wire:model="noofpeople" x-mask="99" class="p-2 w-full rounded-md outline-none border border-primary" placeholder="No of People">
                        </div>
                        <div>
                            @error('noofpeople')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="h-min grid grid-cols-1 gap-2">
                    <input @input="verifyOtp" wire:model="otp" x-mask="999999" class="@if(!$generatedOtp) pointer-events-none opacity-50 @endif p-2 w-1/2 mx-auto rounded-md outline-none border border-primary" placeholder="Confirmation Code">
                    <div class="w-1/2 mx-auto">
                        @error('otp')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                    @if($generatedOtp)
                    <div :class="formattedTime == '00:00' && 'text-red-500'" class="mx-auto w-1/2" x-text="formattedTime == '00:00' ? 'Otp Timed Out' : formattedTime"></div>
                    @endif
                </div>
                <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="rounded-md text-white text-center p-2 w-1/2 bg-primary mx-auto text-xl">{{$generatedOtp ? 'Resend Code' : 'Send Code'}}</button>
            </div>
        </form>
    </div>
</div>