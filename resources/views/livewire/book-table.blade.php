<?php

use App\Http\Controllers\SmsController;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use function Livewire\Volt\{state, rules, with};

state(['name', 'email', 'phoneno', 'country', 'otp', 'generatedOtp', 'noofpeople']);

rules(['name' => 'required', 'email' => 'required|email', 'country' => 'required', 'phoneno' => 'required']);

with(fn() => ['codes' => json_decode(File::get('phoneCountryCodes/CountryCodes.json'))]);

$verifyOtp = function (Request $request) {

    $this->validate();
    $this->resetValidation();

    if (App::call([SmsController::class, 'verifyOtp'], ['id' => $this->generatedOtp->id, 'userOtp' => $this->otp])) {

        User::updateOrCreate(
            [
                'email' => $this->email,
                'phoneno' => $this->phoneno
            ],
            [
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make('12345678'),
            ]
        );
        if (
            Auth::attempt([
                'email' => $this->email,
                'password' => '12345678',
            ])
        ) {
            $request->session()->regenerate();
            $this->redirectRoute('dashboard', navigate: true);
        }
    } else {
        $this->addError('otp', 'Otp is invalid');
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
    App::call([SmsController::class, 'send'], ['phoneno' => $this->country . $this->phoneno, 'message' => 'Your otp is ' . $this->generatedOtp->otp . '.']);
    $this->dispatch('start-countdown');
};

?>

<div x-data="otp" x-on:start-countdown="startCountdown()" class="h-screen bg-black flex justify-center items-center">
    <div class="bg-white text-white w-1/2 rounded-xl">
        <form wire:submit="submit" class="bg-black/80 rounded-xl py-10">
            <div class="h-min grid grid-cols-1 gap-5 w-4/5 mx-auto">
                <div class="text-4xl text-center">Book a Table</div>
                <div class="flex justify-between gap-4">
                    <div class="h-min grid grid-cols-1 gap-2 w-full">
                        <input wire:model="name" class="bg-white/20 p-2 w-full rounded-md outline-none" placeholder="Name">
                        <div>
                            @error('name')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="h-min grid grid-cols-1 gap-2 w-full">
                        <input wire:model="email" class="bg-white/20 p-2 w-full rounded-md outline-none" placeholder="Email">
                        <div>
                            @error('email')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="flex justify-between gap-4">
                    <div class="w-full">
                        <div>
                            <input wire:model="phoneno" x-mask="9999999999" class="bg-white/20 p-2 w-full rounded-md outline-none" placeholder="Phone No">
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
                            <input wire:model="noofpeople" x-mask="99" class="bg-white/20 p-2 w-full rounded-md outline-none" placeholder="No of People">
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
                    <select wire:model="country" class="bg-white/20 p-2 w-full rounded-md outline-none">
                        <option value="">Select a Country</option>
                        @foreach($codes as $code)
                        <option value="{{$code->dial_code}}">
                            {{$code->name}}
                        </option>
                        @endforeach
                    </select>
                    <div>
                        @error('country')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="h-min grid grid-cols-1 gap-2">
                    <input @input="verifyOtp" wire:model="otp" x-mask="999999" class="@if(!$generatedOtp) pointer-events-none opacity-50 @endif bg-white/20 p-2 w-1/2 mx-auto rounded-md outline-none" placeholder="OTP">
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
                <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="rounded-md text-center p-2 w-1/2 bg-violet-600 mx-auto text-xl">{{$generatedOtp ? 'Resend OTP' : 'Send OTP'}}</button>
            </div>
        </form>
    </div>
</div>