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
    $this->generatedOtp = App::call([SmsController::class, 'generateOtp']);
    App::call([SmsController::class, 'send'], ['phoneno' => env('TWILIO_PHONE_COUNTRY_CODE') . $this->phoneno, 'message' => 'Your otp is ' . $this->generatedOtp->otp . '.']);
    $this->dispatch('show-toastr', type: 'success', message: 'A code has been sent to this number');
    $this->dispatch('start-countdown');
};

?>

<div>
    <div x-data="otp" x-on:start-countdown.window="startCountdown()" class="mt-12 mb-44">
        <div class="uppercase font-avenir-next-rounded-light text-center my-16 text-primary text-3xl">
            Sign In
        </div>
        <form wire:submit="submit" class="w-3/5 mx-auto border py-12">
            <div class="w-4/5 mx-auto grid grid-cols-1 gap-8 font-avenir-next-rounded-light text-primary">
                <div>
                    <label>Phone No</label>
                    <input x-mask="9999999999" wire:model="phoneno" type="text" class="w-full bg-black/5 outline-none p-3" placeholder="Phone No">
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
                    <label>Confirmation Code</label>
                    <input @input="verifyOtp" x-mask="999999" wire:model="otp" class="@if(!$generatedOtp) pointer-events-none opacity-50 @endif w-full bg-black/5 outline-none p-3" placeholder="Confirmation Code">
                    @error('otp')
                    <div wire:transition.in.scale.origin.top.duration.1000ms class="text-red-500 text-sm">
                        <span class="error">{{ $message }}</span>
                    </div>
                    @enderror
                    @if($generatedOtp)
                    <div :class="formattedTime == '00:00' && 'text-red-500'" class="" x-text="formattedTime == '00:00' ? 'Otp Timed Out' : formattedTime"></div>
                    @endif
                </div>

                <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="font-avenir-next-rounded-extra-light uppercase text-center py-2 px-4 bg-primary mx-auto text-white text-xl">{{$generatedOtp ? 'Resend' : 'Send'}}</button>
            </div>
        </form>
    </div>
</div>