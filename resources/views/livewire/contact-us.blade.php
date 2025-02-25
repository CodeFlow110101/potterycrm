<?php

use App\Models\User;
use App\Notifications\ContactUsNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

use function Livewire\Volt\{state, rules, computed};

state(['first_name', 'last_name', 'email', 'phoneno', 'email', 'otp', 'messsage', 'generatedOtp', 'form' => 'contact']);

rules(fn() => [
    'first_name' => ['required'],
    'last_name' => ['required'],
    'phoneno' => [
        'required',
        function ($attribute, $value, $fail) {
            (Str::startsWith(trim($this->phoneno), env('TWILIO_PHONE_COUNTRY_CODE')) && strlen(trim(Str::replaceFirst(env('TWILIO_PHONE_COUNTRY_CODE'), '', trim($this->phoneno)))) === 10) || $fail('The :attribute must be in this format ' . env('TWILIO_PHONE_COUNTRY_CODE') . ' XXXXXXXXXX.');
        }
    ],
    'email' => ['required', 'email'],
    'message' => ['required'],
]);

$trimmed_phoneno = computed(function () {
    return trim(Str::replaceFirst(env('TWILIO_PHONE_COUNTRY_CODE'), '', $this->phoneno));
});

$verifyOtp = function () {
    $this->validate();
    $this->resetValidation();

    if (!App::call([SmsController::class, 'verifyOtp'], ['id' => $this->generatedOtp->id, 'userOtp' => $this->otp])) {
        $this->addError('otp', 'Confirmation Code is invalid');
        return;
    }

    $user = User::firstOrCreate(
        ['phoneno' => $this->trimmed_phoneno],
        [
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'role_id' => 2,
            'password' => Hash::make('12345678'),
        ]
    );

    $admin = User::where('role_id', '1')->get();
    Notification::send($admin, new ContactUsNotification($user, $this->messsage));
    $this->form = 'summary';
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
    $this->dispatch('show-toastr', type: 'success', message: 'A code has been sent to this number');
    $this->dispatch('start-countdown');
};
?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="text-5xl lg:text-7xl font-avenir-next-bold">Contact Us</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-y-auto hidden-scrollbar absolute inset-x-0 backdrop-blur-xl flex border border-white rounded-lg" :style="'height: ' + height + 'px;'">
            @if($form == 'contact')
            <form x-data="otp" x-on:start-countdown.window="startCountdown()" wire:submit="submit" class="w-full">
                <div class="w-4/5 mx-auto grid grid-cols-1 gap-8 font-avenir-next-rounded-light py-12">
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">First Name</label>
                        <input wire:model="first_name" class="w-full bg-black/5 outline-none p-3" placeholder="First Name">
                        <div>
                            @error('first_name')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Last Name</label>
                        <input wire:model="last_name" class="w-full bg-black/5 outline-none p-3" placeholder="Last Name">
                        <div>
                            @error('last_name')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Email</label>
                        <input wire:model="email" class="w-full bg-black/5 outline-none p-3" placeholder="Email">
                        <div>
                            @error('email')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Phone Number</label>
                        <input x-mask="{{ env('TWILIO_PHONE_COUNTRY_CODE') }} 9999999999" wire:model="phoneno" type="text" class="w-full bg-black/5 outline-none p-3" placeholder="eg {{ env('TWILIO_PHONE_COUNTRY_CODE') }} XXXXXXXXXX">
                        @error('phoneno')
                        <div wire:transition.in.scale.origin.top.duration.1000ms>
                            <span class="error">{{ $message }}</span>
                        </div>
                        @enderror
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Message</label>
                        <textarea wire:model="messsage" class="w-full bg-black/5 outline-none p-3" placeholder="Message"></textarea>
                        <div>
                            @error('messsage')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Confirmation Code</label>
                        <input @input="verifyOtp" wire:model="otp" x-mask="999999" class="@if(!$generatedOtp) pointer-events-none opacity-50 @endif w-full bg-black/5 outline-none p-3" placeholder="Confirmation Code">
                        <div class="w-1/2 mx-auto">
                            @error('otp')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                        @if($generatedOtp)
                        <div :class="formattedTime == '00:00' && 'text-white'" class="mx-auto w-1/2" x-text="formattedTime == '00:00' ? 'Otp Timed Out' : formattedTime"></div>
                        @endif
                    </div>
                    <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight w-min mx-auto whitespace-nowrap">{{$generatedOtp ? 'Resend Code' : 'Send Code'}}</button>
                </div>
            </form>
            @else
            <div class="m-auto w-4/5 mx-auto">Thank you for contacting us we will get back to you shortly.</div>
            @endif
        </div>
    </div>
</div>