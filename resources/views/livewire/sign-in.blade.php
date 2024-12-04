<?php

use App\Http\Controllers\SmsController;
use App\Models\User;
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
                $this->redirectRoute('product', navigate: true);
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
    $this->generatedOtp = App::call([SmsController::class, 'generateOtp']);
    App::call([SmsController::class, 'send'], ['phoneno' => env('TWILIO_PHONE_COUNTRY_CODE') . $this->phoneno, 'message' => 'Your otp is ' . $this->generatedOtp->otp . '.']);
    $this->dispatch('show-toastr', type: 'success', message: 'A code has been sent to this number');
    $this->dispatch('start-countdown');
};

?>

<div x-data="otp" x-on:start-countdown="startCountdown()" class="h-full flex flex-col">
    <form wire:submit="submit" class="w-2/5 m-auto border border-amber-500 rounded-2xl h-min px-4">
        <div class="w-11/12 mx-auto grid grid-cols-1 gap-8 py-4">
            <div class="text-center text-amber-500 py-6 text-3xl">
                Sign in
            </div>
            <div>
                <input x-mask="9999999999" wire:model="phoneno" type="text" class="border w-full rounded-lg border-amber-500 outline-none p-3" placeholder="Phone No">
                @error('phoneno')
                <div wire:transition.in.scale.origin.top.duration.1000ms class="text-red-500 text-sm">
                    <span class="error">{{ $message }}</span>
                </div>
                @enderror
                @if($generatedOtp)
                A Confirmation Code has been sent to this phone no.
                @endif
            </div>
            <div>
                <input @input="verifyOtp" x-mask="999999" wire:model="otp" class="@if(!$generatedOtp) pointer-events-none opacity-50 @endif border w-full rounded-lg border-amber-500 outline-none p-3" placeholder="Confirmation Code">
                @error('otp')
                <div wire:transition.in.scale.origin.top.duration.1000ms class="text-red-500 text-sm">
                    <span class="error">{{ $message }}</span>
                </div>
                @enderror
                @if($generatedOtp)
                <div :class="formattedTime == '00:00' && 'text-red-500'" class="" x-text="formattedTime == '00:00' ? 'Otp Timed Out' : formattedTime"></div>
                @endif
            </div>

            <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="rounded-md text-center py-2 px-4 bg-amber-500 mx-auto text-white text-xl">{{$generatedOtp ? 'Resend' : 'Send'}}</button>
        </div>
    </form>
</div>