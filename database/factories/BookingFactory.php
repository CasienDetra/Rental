<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
final class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('now', '+30 days');
        $checkOut = (clone $checkIn)->modify('+'.fake()->numberBetween(1, 7).' days');
        $room = Room::factory()->create();
        $nightsCount = $checkOut->diff($checkIn)->days;
        $totalPrice = $nightsCount * (float) $room->price_per_night;

        return [
            'user_id' => User::factory(),
            'room_id' => $room->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_price' => $totalPrice,
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled']),
        ];
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
