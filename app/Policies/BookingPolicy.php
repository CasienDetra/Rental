<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

final class BookingPolicy
{
    /**
     * Determine whether the user can view the booking (owner or admin).
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can update the booking (owner or admin).
     */
    public function update(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id || $user->is_admin;
    }
}
