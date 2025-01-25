<?php

use App\Http\Controllers\SmsController;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use function Livewire\Volt\{state, rules, with};

state(['first_name', 'last_name', 'email', 'phoneno', 'otp', 'generatedOtp']);

rules(['first_name' => 'required', 'last_name' => 'required', 'email' => 'required|email', 'phoneno' => 'required']);

$verifyOtp = function () {

    $this->validate();
    $this->resetValidation();

    if (App::call([SmsController::class, 'verifyOtp'], ['id' => $this->generatedOtp->id, 'userOtp' => $this->otp])) {

        if (User::where('phoneno', $this->phoneno)->exists()) {
            User::where('phoneno', $this->phoneno)->first()->bookings()->create([
                'status_id' => 2,
                'no_of_people' => 1,
                'booking_datetime' => Carbon::now(),
            ]);
        } else {
            $user = User::Create(
                [
                    'email' => $this->email,
                    'phoneno' => $this->phoneno,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'role_id' => 2,
                    'password' => Hash::make('12345678'),
                ]
            );

            $user->bookings()->create([
                'status_id' => 2,
                'no_of_people' => 1,
                'booking_datetime' => Carbon::now(),
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

<div>
    <div class="mt-12 mb-44">
        <div class="">
            <div class="uppercase font-avenir-next-rounded-light text-center my-16 text-primary text-3xl">
                Register
            </div>
        </div>
        <form x-data="otp" x-on:reset="reset()" x-on:start-countdown.window="startCountdown()" wire:submit="submit" class="w-3/5 mx-auto border py-12">
            <div class="h-min grid grid-cols-1 gap-8 w-4/5 mx-auto font-avenir-next-rounded-light text-primary">
                <div>
                    <label>First Name</label>
                    <input wire:model="first_name" class="w-full bg-black/5 outline-none p-3" placeholder="First Name">
                    <div>
                        @error('first_name')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div>
                    <label>Last Name</label>
                    <input wire:model="last_name" class="w-full bg-black/5 outline-none p-3" placeholder="Last Name">
                    <div>
                        @error('last_name')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div>
                    <label>Email</label>
                    <input wire:model="email" class="w-full bg-black/5 outline-none p-3" placeholder="Email">
                    <div>
                        @error('email')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div>
                    <label>Phone No</label>
                    <input wire:model="phoneno" x-mask="9999999999" class="w-full bg-black/5 outline-none p-3" placeholder="Phone No">
                    <div>
                        @error('phoneno')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div>
                    <label>Confirmation Code</label>
                    <input @input="verifyOtp" wire:model="otp" x-mask="999999" class="@if(!$generatedOtp) pointer-events-none opacity-50 @endif w-full bg-black/5 outline-none p-3" placeholder="Confirmation Code">
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
                <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="font-avenir-next-rounded-extra-light uppercase text-center py-2 px-4 bg-primary mx-auto text-white text-xl">{{$generatedOtp ? 'Resend Code' : 'Send Code'}}</button>
            </div>
        </form>
    </div>
</div>