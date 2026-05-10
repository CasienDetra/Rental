<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Tests\TestCase;

final class BookingTest extends TestCase
{
    /**
     * Test user can create booking.
     */
    public function test_user_can_create_booking(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create(['price_per_night' => 100]);

        $checkIn = now()->addDays(5)->format('Y-m-d H:i:s');
        $checkOut = now()->addDays(7)->format('Y-m-d H:i:s');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', [
                'room_id' => $room->id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
            ]);

        $response->assertCreated()
            ->assertJsonPath('user_id', $user->id)
            ->assertJsonPath('room_id', $room->id)
            ->assertJsonPath('status', 'pending');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);
    }

    /**
     * Test booking calculates total price correctly.
     */
    public function test_booking_calculates_total_price(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create(['price_per_night' => 100]);

        $checkIn = now()->addDays(5)->format('Y-m-d H:i:s');
        $checkOut = now()->addDays(8)->format('Y-m-d H:i:s'); // 3 nights

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', [
                'room_id' => $room->id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
            ]);

        $response->assertCreated()
            ->assertJsonPath('total_price', '300.00');
    }

    /**
     * Test user cannot book overlapping dates.
     */
    public function test_user_cannot_book_overlapping_dates(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        // Create existing booking
        Booking::factory()->create([
            'room_id' => $room->id,
            'check_in_date' => now()->addDays(5),
            'check_out_date' => now()->addDays(10),
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/bookings', [
                'room_id' => $room->id,
                'check_in_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'check_out_date' => now()->addDays(12)->format('Y-m-d H:i:s'),
            ]);

        $response->assertConflict()
            ->assertJsonPath('message', 'Room is not available for the selected dates');
    }

    /**
     * Test user can view their bookings.
     */
    public function test_user_can_view_their_bookings(): void
    {
        $user = User::factory()->create();
        Booking::factory(3)->for($user)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/bookings');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test user can view single booking.
     */
    public function test_user_can_view_single_booking(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->for($user)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/bookings/{$booking->id}");

        $response->assertOk()
            ->assertJsonPath('id', $booking->id)
            ->assertJsonPath('user_id', $user->id);
    }

    /**
     * Test user cannot view other user's booking.
     */
    public function test_user_cannot_view_other_users_booking(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $booking = Booking::factory()->for($otherUser)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/bookings/{$booking->id}");

        $response->assertForbidden();
    }

    /**
     * Test user can cancel their booking.
     */
    public function test_user_can_cancel_their_booking(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->for($user)->create(['status' => 'pending']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/cancel");

        $response->assertOk();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);
    }

    /**
     * Test admin can confirm booking.
     */
    public function test_admin_can_confirm_booking(): void
    {
        $admin = User::factory()->admin()->create();
        $booking = Booking::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/confirm");

        $response->assertOk();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    }

    /**
     * Test non-admin cannot confirm booking.
     */
    public function test_non_admin_cannot_confirm_booking(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/bookings/{$booking->id}/confirm");

        $response->assertForbidden();
    }
}
