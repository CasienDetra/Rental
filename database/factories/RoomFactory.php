<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
final class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, asText: true),
            'description' => fake()->paragraph(),
            'price_per_night' => fake()->randomFloat(2, 20, 500),
            'capacity' => fake()->numberBetween(1, 6),
            'available' => true,
            'image_path' => null,
        ];
    }

    /**
     * Indicate that the room is unavailable.
     */
    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'available' => false,
        ]);
    }
}
