<?php

use App\Http\Controllers\SmsController;
use App\Models\Booking;
use App\Models\Date;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;


use function Livewire\Volt\{state, rules, computed};

state(['first_name', 'last_name', 'email', 'phoneno', 'otp', 'generatedOtp', 'people' => 1, 'date' => Carbon::today()->toDateString(), 'form' => 'register', 'summary']);

rules(fn() => [
    'first_name' => ['required'],
    'last_name' => ['required'],
    'email' => ['required', 'email'],
    'people' => ['integer', 'min:1'],
    'phoneno' => [
        'required',
        function ($attribute, $value, $fail) {
            (Str::startsWith(trim($this->phoneno), env('TWILIO_PHONE_COUNTRY_CODE')) && strlen(trim(Str::replaceFirst(env('TWILIO_PHONE_COUNTRY_CODE'), '', trim($this->phoneno)))) === 10) || $fail('The :attribute must be in this format ' . env('TWILIO_PHONE_COUNTRY_CODE') . ' XXXXXXXXXX.');
        },
        function ($attribute, $value, $fail) {
            Date::where('date', $this->date)->whereHas('timeSlots', function (Builder $query) {
                $query->where('start_time', Carbon::now()->copy()->startOfHour()->format('H:i:s'))->where('end_time', Carbon::now()->copy()->addHour()->startOfHour()->format('H:i:s'));
            })->doesntExist() && $fail('It seems the store is closed because there are no open time slots at this time.');
        }
    ],
]);

$trimmed_phoneno = computed(function () {
    return trim(Str::replaceFirst(env('TWILIO_PHONE_COUNTRY_CODE'), '', $this->phoneno));
});

$verifyOtp = function () {

    $this->validate();
    $this->resetValidation();

    if (App::call([SmsController::class, 'verifyOtp'], ['id' => $this->generatedOtp->id, 'userOtp' => $this->otp])) {

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

        $timeslot = TimeSlot::where('start_time', Carbon::now()->copy()->startOfHour()->format('H:i:s'))->where('end_time', Carbon::now()->copy()->addHour()->startOfHour()->format('H:i:s'))->wherehas('date', function (Builder $query) {
            $query->where('date', $this->date);
        })->first();

        $isBookingCreated = $user->whereHas('bookings.timeSlot.date', function ($query) {
            $query->where('date', $this->date);
        })->doesntExist();

        $isBookingCreated && $user->bookings()->create([
            'status_id' => 2,
            'no_of_people' => $this->people,
            'time_slot_id' => $timeslot->id,
        ]);

        $this->form = 'summary';
        $this->summary = $isBookingCreated ? 'Your booking has been successfully registered' : 'You already have a booking for this date';
    } else {
        $this->addError('otp', 'Confirmation Code is invalid');
    }
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

<div class="grow flex flex-col gap-8 py-8 text-white w-11/12 mx-auto">
    <div class="text-7xl font-avenir-next-bold">Register</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-y-auto hidden-scrollbar absolute inset-x-0 w-3/5 flex mx-auto backdrop-blur-xl border border-white rounded-lg" :style="'height: ' + height + 'px;'">
            @if($form == 'register')
            <form x-data="otp" x-on:reset="reset()" x-on:start-countdown.window="startCountdown()" wire:submit="submit" class="w-full">
                <div class="h-min grid grid-cols-1 gap-8 w-4/5 mx-auto font-avenir-next-rounded-light py-12">
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
                        <label class="font-avenir-next-rounded-semibold text-xl">Number of People</label>
                        <input wire:model="people" class="w-full bg-black/5 outline-none p-3" placeholder="Email">
                        <div>
                            @error('people')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Phone Number</label>
                        <input wire:model="phoneno" x-mask="{{ env('TWILIO_PHONE_COUNTRY_CODE') }} 9999999999" class="w-full bg-black/5 outline-none p-3" placeholder="eg {{ env('TWILIO_PHONE_COUNTRY_CODE') }} XXXXXXXXXX">
                        <div>
                            @error('phoneno')
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
                        <div :class="formattedTime == '00:00' && 'text-red-500'" class="mx-auto w-1/2" x-text="formattedTime == '00:00' ? 'Otp Timed Out' : formattedTime"></div>
                        @endif
                    </div>
                    <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="text-black py-3 uppercase px-6 mx-auto bg-white rounded-lg tracking-tight">{{$generatedOtp ? 'Resend Code' : 'Send Code'}}</button>
                </div>
            </form>
            @else
            <div id="summary" class="flex justify-center items-center grow">{{ $summary }}</div>
            @endif
        </div>
    </div>
</div>