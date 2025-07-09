<?php

use App\Http\Controllers\SmsController;
use App\Models\Booking;
use App\Models\Date;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

use function Livewire\Volt\{state, rules, with, computed};

state(['first_name', 'last_name', 'email', 'phoneno', 'otp', 'generatedOtp', 'date' => null, 'people' => 1, 'selectedTimeSlot' => null, 'validateBooking', 'currentForm' => 'booking', 'summary']);

rules(fn() => [
    'first_name' => $this->currentForm == 'user' ?  ['required'] : ['exclude'],
    'last_name' => $this->currentForm == 'user' ?  ['required'] : ['exclude'],
    'email' => $this->currentForm == 'user' ?   ['required', 'email'] : ['exclude'],
    'phoneno' => $this->currentForm == 'user' ?  ['required', function ($attribute, $value, $fail) {
        Gate::allows('valid-phone-number', $this->phoneno) || $fail('The :attribute must be in this format ' . env('TWILIO_PHONE_COUNTRY_CODE') . ' ' . Str::replace('9', 'X', env('PHONE_NUMBER_VALIDATION_PATTERN')));
    }] : ['exclude'],
    'people' => $this->currentForm == 'booking' ? ['required', 'integer', 'min:1'] : ['exclude'],
    'date' => $this->currentForm == 'booking' ? ['required'] : ['exclude'],
    'selectedTimeSlot' => $this->currentForm == 'booking' ? ['required'] : ['exclude'],
])->attributes([
    'selectedTimeSlot' => 'time slot',
]);

with(fn() => [
    'allowedDates' => Date::whereDate('date', '>=', Carbon::today())->whereHas('timeslots')->get()->map(fn($date) => $date->date),
    'slots' => Date::when($this->date, fn($query) => $query->where('date', $this->date), fn($query) => $query->where('id', 0))->with(['timeSlots' => fn($query) => $query->when(Carbon::parse($this->date)->isToday(), fn($q) => $q->whereTime('start_time', '>', Carbon::now()->addHour()->format('H:i:s')))])?->first()?->timeSlots->mapWithKeys(fn($timeslot) => [$timeslot->id =>  $timeslot->start_time . ' - ' . $timeslot->end_time]) ?? collect([]),
]);

$trimmed_phoneno = computed(function () {
    return trim(Str::replaceFirst(env('TWILIO_PHONE_COUNTRY_CODE'), '', $this->phoneno));
});

$verifyOtp = function () {
    $this->validate();
    $this->resetValidation();

    $timeslot = TimeSlot::find($this->selectedTimeSlot);

    $status_id = ($timeslot->bookings->sum('no_of_people') + $this->people) > $timeslot->date->max_people ? 1 : 2;

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

    $isBookingCreated = $user->bookings()->whereHas('timeSlot.date', function ($query) {
        $query->where('date', $this->date);
    })->doesntExist() && $user->bookings()->create([
        'status_id' => $status_id,
        'no_of_people' => $this->people,
        'time_slot_id' => $timeslot->id,
    ]);

    $this->summary = $isBookingCreated ? 'Your booking has been successfully registered.' : 'You already have a booking for this date.';

    $this->currentForm = 'summary';
};

$submitBooking = function () {
    $this->validate();
    $this->currentForm = 'user';
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
    $this->dispatch('start-countdown');
};
?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="flex max-sm:flex-col sm:justify-between gap-2 items-center">
        <div class="text-5xl lg:text-7xl font-avenir-next-bold text-white">Book a Table</div>
        <livewire:audio-player />
    </div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-y-auto hidden-scrollbar absolute inset-x-0 flex flex-col grow backdrop-blur-xl border border-white rounded-lg p-6 gap-6" :style="'height: ' + height + 'px;'">
            <div class="flex items-center max-sm:justify-center gap-2 text-sm w-3/4 mx-auto">
                <div :class="$wire.currentForm != 'booking' && 'max-sm:hidden'" class="flex gap-2 items-center">
                    <div class="@if($currentForm == 'booking') bg-white @else bg-white/50 @endif text-black rounded-full size-6 flex justify-center items-center">1</div>
                    <div>Booking</div>
                </div>
                <div class="grow border-2 border-white rounded-full max-sm:hidden"></div>
                <div :class="$wire.currentForm != 'user' && 'max-sm:hidden'" class="flex gap-2 items-center ">
                    <div class="@if($currentForm == 'user') bg-white @else bg-white/50 @endif text-black rounded-full size-6 flex justify-center items-centeruser">2</div>
                    <div>Your Details</div>
                </div>
                <div class="grow border-2 border-white rounded-full max-sm:hidden"></div>
                <div :class="$wire.currentForm != 'summary' && 'max-sm:hidden'" class="flex gap-2 items-center">
                    <div class="@if($currentForm == 'summary') bg-white @else bg-white/50 @endif text-black rounded-full size-6 flex justify-center items-centeruser">3</div>
                    <div>Summary</div>
                </div>
            </div>
            @if($currentForm == 'booking')
            <div id="booking" class="grow flex flex-col">
                <form x-on:reset="reset()" wire:submit="submitBooking" class="grow flex flex-col gap-8">
                    <div x-data="flatpickrDate(null,'{{$allowedDates}}')" class="grow flex max-sm:flex-col gap-12">
                        <div class="flex-1">
                            <input type="text" x-ref="dateInput" wire:model.live="date" class="hidden" placeholder="Select a date">
                            <div class="w-full flex justify-center overflow-hidden rounded-lg sm:justify-end">
                                <div class="sm:border-y sm:border-l w-full rounded-l-lg flex">
                                    <div class="mx-auto w-4/5 h-4/5 my-auto max-sm:hidden">
                                        <div class="text-2xl" x-text="year('{{$date}}')"></div>
                                        <div x-text="date('{{$date}}')"></div>
                                    </div>
                                </div>
                                <div wire:ignore x-ref="calendarContainer" class="flex justify-center"></div>
                            </div>
                            @error('date')
                            <div>
                                <span class="error">{{ $message }}</span>
                            </div>
                            @enderror
                        </div>
                        <div class="flex-1 flex flex-col gap-4">
                            <div class="flex flex-col gap-4 bg-white text-black p-4 rounded-lg">
                                <div class="text-2xl" x-text="'No of people'"></div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <input wire:model="people" x-mask="99" @input="if ($event.target.value.trim() === '00' || $event.target.value.trim() === '0') $event.target.value = 1" class="bg-transparent text-xl text-center outline-none border border-black p-4 w-24" />
                                    </div>
                                </div>
                                @error('people')
                                <div>
                                    <span class="error">{{ $message }}</span>
                                </div>
                                @enderror
                            </div>
                            <div class="flex flex-col border p-4 bg-white gap-4 text-black rounded-lg">
                                <div class="text-2xl" x-text="'Time'"></div>
                                <div>
                                    <div class="flex flex-wrap justify-around gap-4 text-sm">
                                        @foreach($slots as $id => $slot)
                                        <button type="button" x-data="{ slot_id : {{$id}} }" @click="$wire.selectedTimeSlot = slot_id;" :class="$wire.selectedTimeSlot == slot_id && 'bg-black text-white'" class="border border-black py-1 px-4 rounded-full" x-text="timeSlot('{{$slot}}')"></button>
                                        @endforeach
                                    </div>
                                    @if(!$date)
                                    <div>
                                        Please select a date
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @error('selectedTimeSlot')
                            <div>
                                <span class="error">{{ $message }}</span>
                            </div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="text-black py-3 uppercase px-6 mt-auto bg-white rounded-lg tracking-tight mx-auto">Submit</button>
                </form>
            </div>
            @elseif($currentForm == 'user')
            <div id="user" class="grow flex flex-col">
                <form wire:submit="submit" class="py-8">
                    <div x-data="otp" x-on:start-countdown.window="startCountdown()" class="h-min grid grid-cols-1 gap-8 w-4/5 mx-auto font-avenir-next-rounded-light text-white">
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
                            <input wire:model="phoneno" x-mask="{{ env('TWILIO_PHONE_COUNTRY_CODE') }} {{ env('PHONE_NUMBER_VALIDATION_PATTERN') }}" class="w-full bg-black/5 outline-none p-3" placeholder="eg {{ env('TWILIO_PHONE_COUNTRY_CODE') }} {{Str::replace('9', 'X', env('PHONE_NUMBER_VALIDATION_PATTERN'))}}">
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
                            <div :class="formattedTime == '00:00' && 'text-white'" x-text="formattedTime == '00:00' ? 'Otp Timed Out' : formattedTime"></div>
                            @endif
                        </div>
                        <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight w-min mx-auto whitespace-nowrap">{{$generatedOtp ? 'Resend Code' : 'Send Code'}}</button>
                    </div>
                </form>
            </div>
            @elseif($currentForm == 'summary')
            <div id="summary" class="flex justify-center items-center grow">{{ $summary }}</div>
            @endif
        </div>
    </div>
</div>