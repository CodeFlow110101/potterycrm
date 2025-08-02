<?php

use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
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
use Illuminate\Support\Facades\Gate;

use function Livewire\Volt\{state, rules, computed};

state(['first_name', 'last_name', 'email', 'phoneno', 'people' => 1, 'date' => Carbon::today()->toDateString(), 'form' => 'register', 'summary']);

rules(fn() => [
    'first_name' => ['required'],
    'last_name' => ['required'],
    'email' => [
        'required',
        'email',
        fn(string $attribute, mixed $value, Closure $fail) =>
        Gate::allows('valid-phone-number', $this->phoneno) && ((User::where('phoneno',  $this->trimmed_phoneno)->where('email', $this->email)->exists() || User::where('email', $this->email)->doesntExist()) || $fail('The email is already been taken.')),
    ],
    'people' => ['required', 'integer', 'min:1'],
    'phoneno' => [
        'required',
        function ($attribute, $value, $fail) {
            Gate::allows('valid-phone-number', $this->phoneno) || $fail('The :attribute must be in this format ' . env('TWILIO_PHONE_COUNTRY_CODE') . ' ' . Str::replace('9', 'X', env('PHONE_NUMBER_VALIDATION_PATTERN')));
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

$submit = function () {

    $this->validate();

    $credentials = collect([
        'email' => $this->email,
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'role_id' => 2,
        'password' => Hash::make('12345678'),
    ]);

    $user = App::call([UserController::class, 'upsert'], ['phoneno' => $this->trimmed_phoneno, 'credentials' => $credentials]);

    $timeslot = TimeSlot::where('start_time', Carbon::now()->copy()->startOfHour()->format('H:i:s'))->where('end_time', Carbon::now()->copy()->addHour()->startOfHour()->format('H:i:s'))->wherehas('date', function (Builder $query) {
        $query->where('date', $this->date);
    })->first();

    $isBookingCreated = $user->whereHas('bookings.timeSlot.date', function ($query) {
        $query->where('date', $this->date);
    })->doesntExist();

    $isBookingCreated && $user->bookings()->create([
        'status_id' => 3,
        'no_of_people' => $this->people,
        'time_slot_id' => $timeslot->id,
    ]);

    $this->form = 'summary';
    $this->summary = $isBookingCreated ? 'Your booking has been successfully registered' : 'You already have a booking for this date';
};

?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="text-5xl lg:text-7xl font-avenir-next-bold">Register</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-y-auto hidden-scrollbar absolute inset-x-0 flex mx-auto backdrop-blur-xl border border-white rounded-lg" :style="'height: ' + height + 'px;'">
            @if($form == 'register')
            <form x-data="otp" x-on:reset="reset()" x-on:start-countdown.window="startCountdown()" wire:submit="submit" class="w-full">
                <div class="h-min grid grid-cols-1 gap-8 w-4/5 mx-auto font-avenir-next-rounded-light py-12">
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">First Name</label>
                        <input wire:model="first_name" class="w-full bg-black/5 outline-none p-3" placeholder="First Name">
                        <div>
                            @error('first_name')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Last Name</label>
                        <input wire:model="last_name" class="w-full bg-black/5 outline-none p-3" placeholder="Last Name">
                        <div>
                            @error('last_name')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Email</label>
                        <input wire:model="email" class="w-full bg-black/5 outline-none p-3" placeholder="Email">
                        <div>
                            @error('email')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Number of People</label>
                        <input x-mask="99" wire:model="people" class="w-full bg-black/5 outline-none p-3" placeholder="Number of People">
                        <div>
                            @error('people')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Phone Number</label>
                        <input wire:model="phoneno" x-mask="{{ env('TWILIO_PHONE_COUNTRY_CODE') }} {{ env('PHONE_NUMBER_VALIDATION_PATTERN') }}" class="w-full bg-black/5 outline-none p-3" placeholder="eg {{ env('TWILIO_PHONE_COUNTRY_CODE') }} {{Str::replace('9', 'X', env('PHONE_NUMBER_VALIDATION_PATTERN'))}}">
                        <div>
                            @error('phoneno')
                            <span>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="text-black py-3 uppercase px-6 mx-auto bg-white rounded-lg tracking-tight">Submit</button>
                </div>
            </form>
            @else
            <div id="summary" class="flex justify-center items-center grow">{{ $summary }}</div>
            @endif
        </div>
    </div>
</div>