<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{

    public function before(User $user, string $ability): bool|null
    {
        if ($user->role->name == "administrator") {
            return true;
        }

        return null;
    }

    public function viewCustomerDetailColumns(User $user): bool
    {
        return $user->role->name == "staff";
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->name == "staff";
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->role->name == "staff";
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        //
    }

    public function viewfilters(User $user): bool
    {
        return false;
    }

    public function createBooking(User $user): bool
    {
        return $user->role->name == "staff";
    }

    public function viewAllBookingDatesWhileCreate(User $user): bool
    {
        return false;
    }
}
