<?php

use App\Http\Controllers\SmsController;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ConfirmationCode;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{state, rules};

state(['phoneno', 'otp', 'generatedOtp']);

rules(['phoneno' => 'required'])->messages([
    'phoneno.required' => 'The phone no is required.',
]);

$verifyOtp = function (Request $request) {
    $this->validate();
    $this->resetValidation();

    if (App::call([SmsController::class, 'verifyOtp'], ['id' => $this->generatedOtp->id, 'userOtp' => $this->otp])) {

        if (User::where('phoneno', $this->phoneno)->exists()) {
            $user = User::where('phoneno', $this->phoneno)->first();
            if (
                Auth::attempt([
                    'email' => $user->email,
                    'password' => '12345678',
                ])
            ) {
                $request->session()->regenerate();
                $this->redirect('/shop', navigate: true);
            }
        } else {
            $this->addError('phoneno', 'Account does not exist with this phone no.');
        }
    } else {
        $this->addError('otp', 'Confirmation Code is invalid');
    }
};

$submit = function () {
    $this->validate();
    $this->generatedOtp = App::call([SmsController::class, 'generateOtp'], ['phoneno' => $this->phoneno]);
    $this->dispatch('show-toastr', type: 'success', message: 'A code has been sent to this number');
    $this->dispatch('start-countdown');
};

?>

<div x-data="otp" x-on:start-countdown.window="startCountdown()" class="grow flex flex-col gap-8 py-8 text-white w-11/12 mx-auto">
    <div class="text-7xl font-avenir-next-bold text-white">Log In</div>
    <form wire:submit="submit" class="w-3/5 mx-auto border border-white rounded-lg backdrop-blur-xl py-12 my-auto">
        <div class="w-4/5 mx-auto grid grid-cols-1 gap-8 font-avenir-next-rounded-light">
            <div>
                <label class="font-avenir-next-rounded-semibold text-xl">Phone Number</label>
                <input x-mask="9999999999" wire:model="phoneno" type="text" class="w-full bg-black/20 outline-none p-3 placeholder:text-white/80" placeholder="Phone No">
                @error('phoneno')
                <div wire:transition.in.scale.origin.top.duration.1000ms class="text-white text-sm">
                    <span class="error">{{ $message }}</span>
                </div>
                @enderror
                @if($generatedOtp)
                A Confirmation Code has been sent to this phone no.
                @endif
            </div>
            <div>
                <label class="font-avenir-next-rounded-semibold text-xl">Confirmation Code</label>
                <input @input="verifyOtp" x-mask="999999" wire:model="otp" class="@if(!$generatedOtp) pointer-events-none opacity-50 @endif w-full bg-black/20 outline-none p-3 placeholder:text-white/80" placeholder="Confirmation Code">
                @error('otp')
                <div wire:transition.in.scale.origin.top.duration.1000ms class="text-white text-sm">
                    <span class="error">{{ $message }}</span>
                </div>
                @enderror
                @if($generatedOtp)
                <div :class="formattedTime == '00:00' && 'text-red-500'" class="" x-text="formattedTime == '00:00' ? 'Otp Timed Out' : formattedTime"></div>
                @endif
            </div>

            <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight w-min mx-auto">{{$generatedOtp ? 'Resend' : 'Send'}}</button>
        </div>
    </form>
</div>