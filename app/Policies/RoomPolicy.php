<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

final class RoomPolicy
{
    /**
     * Determine whether the user can create models (admin only).
     */
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model (admin only).
     */
    public function update(User $user, Room $room): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model (admin only).
     */
    public function delete(User $user, Room $room): bool
    {
        return $user->is_admin;
    }
}
