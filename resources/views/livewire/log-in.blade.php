<?php

use App\Http\Controllers\SmsController;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ConfirmationCode;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

use function Livewire\Volt\{state, rules, computed, mount};

state(['phoneno', 'otp', 'generatedOtp', 'device_id']);

rules(fn() => [
    'phoneno' => [
        'required',
        function ($attribute, $value, $fail) {
            Gate::allows('valid-phone-number', $this->phoneno) || $fail('The :attribute must be in this format ' . env('TWILIO_PHONE_COUNTRY_CODE') . ' ' . Str::replace('9', 'X', env('PHONE_NUMBER_VALIDATION_PATTERN')));
        },
        function ($attribute, $value, $fail) {
            $user = User::where('phoneno', $this->trimmed_phoneno)->first();

            if (!$user || $user->role->name !== 'staff') {
                return;
            }

            if ($user->role->name === 'staff' && $this->device_id !== env('POS_DEVICE_ID')) {
                $fail('Staff users are restricted to login only from the authorized POS device.');
            }
        }
    ],
])->messages([
    'phoneno.required' => 'The phone number is required.',
]);


$verifyOtp = function (Request $request) {
    $this->validate();
    $this->resetValidation();

    if (App::call([SmsController::class, 'verifyOtp'], ['id' => $this->generatedOtp->id, 'userOtp' => $this->otp])) {

        if (User::where('phoneno', $this->trimmed_phoneno)->exists()) {
            $user = User::where('phoneno', $this->trimmed_phoneno)->first();
            if (
                Auth::attempt([
                    'email' => $user->email,
                    'password' => '12345678',
                ], $this->device_id !== env('POS_DEVICE_ID'))
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

$trimmed_phoneno = computed(function () {
    return trim(Str::replaceFirst(env('TWILIO_PHONE_COUNTRY_CODE'), '', $this->phoneno));
});

$submit = function () {
    $this->validate();
    $this->generatedOtp = App::call([SmsController::class, 'generateOtp'], ['phoneno' => $this->trimmed_phoneno]);
    $this->dispatch('start-countdown');
};

?>

<div x-data="otp" x-on:start-countdown.window="startCountdown()" class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="text-5xl lg:text-7xl font-avenir-next-bold text-white">Log In</div>
    <form wire:submit="submit" class="border border-white rounded-lg backdrop-blur-xl py-12 my-auto">
        <div class="w-4/5 mx-auto grid grid-cols-1 gap-8 font-avenir-next-rounded-light">
            <div>
                <label class="font-avenir-next-rounded-semibold text-xl">Phone Number</label>
                <input x-mask="{{ env('TWILIO_PHONE_COUNTRY_CODE') }} {{ env('PHONE_NUMBER_VALIDATION_PATTERN') }}" wire:model="phoneno" type="text" class="w-full bg-black/20 outline-none p-3 placeholder:text-white/80" placeholder="eg {{ env('TWILIO_PHONE_COUNTRY_CODE') }} {{Str::replace('9', 'X', env('PHONE_NUMBER_VALIDATION_PATTERN'))}}">
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

@script
<script>
    FingerprintJS.load().then(fp => {
        fp.get().then(result => {
            $wire.device_id = result.visitorId;
        });
    });
</script>
@endscript