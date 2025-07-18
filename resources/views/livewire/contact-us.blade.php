<?php

use App\Models\User;
use App\Notifications\ContactUsNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

use function Livewire\Volt\{state, rules, computed};

state(['first_name', 'last_name', 'email', 'phoneno', 'email', 'otp', 'message', 'generatedOtp', 'form' => 'contact']);

rules(fn() => [
    'first_name' => ['required'],
    'last_name' => ['required'],
    'phoneno' => [
        'required',
        function ($attribute, $value, $fail) {
            Gate::allows('valid-phone-number', $this->phoneno) || $fail('The :attribute must be in this format ' . env('TWILIO_PHONE_COUNTRY_CODE') . ' ' . Str::replace('9', 'X', env('PHONE_NUMBER_VALIDATION_PATTERN')));
        }
    ],
    'email' => [
        'required',
        'email',
        fn(string $attribute, mixed $value, Closure $fail) =>
        Gate::allows('valid-phone-number', $this->phoneno) && ((User::where('phoneno',  $this->trimmed_phoneno)->where('email', $this->email)->exists() || User::where('email', $this->email)->doesntExist()) || $fail('The email is already been taken.')),
    ],
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

    $credentials = collect([
        'email' => $this->email,
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'role_id' => 2,
        'password' => Hash::make('12345678'),
    ]);
    $user = App::call([UserController::class, 'upsert'], ['phoneno' => $this->trimmed_phoneno, 'credentials' => $credentials]);

    $admin = User::where('role_id', '1')->get();
    Notification::send($admin, new ContactUsNotification($user, $this->message));
    $this->form = 'summary';
};

$submit = function () {
    $this->validate();

    $this->generatedOtp = App::call([SmsController::class, 'generateOtp'], ['phoneno' => $this->trimmed_phoneno]);
    $this->dispatch('start-countdown');
};
?>

<div class="grow flex flex-col text-white w-11/12 mx-auto">
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-y-auto hidden-scrollbar absolute inset-x-0 flex flex-col gap-4 lg:gap-8 py-4 lg:py-8" :style="'height: ' + height + 'px;'">
            <div class="text-5xl lg:text-7xl font-avenir-next-bold">We Are Here</div>
            <div class="backdrop-blur-xl flex border border-white rounded-lg p-4">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3270.9811900077307!2d138.5608098!3d-34.93200869999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ab0cf7db64f4bc7%3A0x32419dacdaddc12c!2s188%20Sir%20Donald%20Bradman%20Dr%2C%20Cowandilla%20SA%205033%2C%20Australia!5e0!3m2!1sen!2sin!4v1741190608536!5m2!1sen!2sin" class="w-full rounded-lg" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="flex flex-col items-end gap-2 *:gap-2">
                <div class="flex justify-start gap-2 *:bg-white *:rounded-full *:p-1">
                    <a href="{{ env('INSTAGRAM_URL') }}" target="_blank">
                        <svg class="size-8 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path fill="currentColor" fill-rule="evenodd" d="M3 8a5 5 0 0 1 5-5h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8Zm5-3a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H8Zm7.597 2.214a1 1 0 0 1 1-1h.01a1 1 0 1 1 0 2h-.01a1 1 0 0 1-1-1ZM12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm-5 3a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="{{ env('FACEBOOK_URL') }}" target="_blank">
                        <svg class="size-8 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M13.135 6H15V3h-1.865a4.147 4.147 0 0 0-4.142 4.142V9H7v3h2v9.938h3V12h2.021l.592-3H12V6.591A.6.6 0 0 1 12.592 6h.543Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="{{ env('TIKTOK_URL') }}" target="_blank">
                        <svg class="size-8" fill="#000000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">
                            <path d="M19.589 6.686a4.793 4.793 0 0 1-3.77-4.245V2h-3.445v13.672a2.896 2.896 0 0 1-5.201 1.743l-.002-.001.002.001a2.895 2.895 0 0 1 3.183-4.51v-3.5a6.329 6.329 0 0 0-5.394 10.692 6.33 6.33 0 0 0 10.857-4.424V8.687a8.182 8.182 0 0 0 4.773 1.526V6.79a4.831 4.831 0 0 1-1.003-.104z" />
                        </svg>
                    </a>
                    <a href="{{ env('YOUTUBE_URL') }}" target="_blank">
                        <svg class="size-8 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M21.7 8.037a4.26 4.26 0 0 0-.789-1.964 2.84 2.84 0 0 0-1.984-.839c-2.767-.2-6.926-.2-6.926-.2s-4.157 0-6.928.2a2.836 2.836 0 0 0-1.983.839 4.225 4.225 0 0 0-.79 1.965 30.146 30.146 0 0 0-.2 3.206v1.5a30.12 30.12 0 0 0 .2 3.206c.094.712.364 1.39.784 1.972.604.536 1.38.837 2.187.848 1.583.151 6.731.2 6.731.2s4.161 0 6.928-.2a2.844 2.844 0 0 0 1.985-.84 4.27 4.27 0 0 0 .787-1.965 30.12 30.12 0 0 0 .2-3.206v-1.516a30.672 30.672 0 0 0-.202-3.206Zm-11.692 6.554v-5.62l5.4 2.819-5.4 2.801Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <div class="flex">
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7.978 4a2.553 2.553 0 0 0-1.926.877C4.233 6.7 3.699 8.751 4.153 10.814c.44 1.995 1.778 3.893 3.456 5.572 1.68 1.679 3.577 3.018 5.57 3.459 2.062.456 4.115-.073 5.94-1.885a2.556 2.556 0 0 0 .001-3.861l-1.21-1.21a2.689 2.689 0 0 0-3.802 0l-.617.618a.806.806 0 0 1-1.14 0l-1.854-1.855a.807.807 0 0 1 0-1.14l.618-.62a2.692 2.692 0 0 0 0-3.803l-1.21-1.211A2.555 2.555 0 0 0 7.978 4Z" />
                    </svg>
                    <div>{{ env('TWILIO_PHONE_COUNTRY_CODE') . ' ' . env('ADMIN_PHONE_NO') }}</div>
                </div>
            </div>
            <div class="text-5xl lg:text-7xl font-avenir-next-bold">Contact Us</div>
            <div class="backdrop-blur-xl flex border border-white rounded-lg">
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
                            <textarea wire:model="message" class="w-full bg-black/5 outline-none p-3" placeholder="Message"></textarea>
                            <div>
                                @error('message')
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
                <div class="m-auto w-4/5 text-center py-12">Thank you for contacting us we will get back to you shortly.</div>
                @endif
            </div>
        </div>
    </div>
</div>