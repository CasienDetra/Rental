<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Tests\TestCase;

final class RoomTest extends TestCase
{
    /**
     * Test browsing all rooms.
     */
    public function test_can_browse_all_rooms(): void
    {
        Room::factory(5)->create();

        $response = $this->getJson('/api/v1/rooms');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'price_per_night', 'capacity', 'available'],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(5, 'data');
    }

    /**
     * Test viewing a single room.
     */
    public function test_can_view_single_room(): void
    {
        $room = Room::factory()->create();

        $response = $this->getJson("/api/v1/rooms/{$room->id}");

        $response->assertOk()
            ->assertJsonPath('id', $room->id)
            ->assertJsonPath('title', $room->title)
            ->assertJsonPath('capacity', $room->capacity);
    }

    /**
     * Test filtering rooms by availability.
     */
    public function test_can_filter_rooms_by_availability(): void
    {
        Room::factory(3)->available()->create();
        Room::factory(2)->unavailable()->create();

        $response = $this->getJson('/api/v1/rooms?available=true');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test filtering rooms by price range.
     */
    public function test_can_filter_rooms_by_price_range(): void
    {
        Room::factory()->create(['price_per_night' => 50]);
        Room::factory()->create(['price_per_night' => 100]);
        Room::factory()->create(['price_per_night' => 200]);

        $response = $this->getJson('/api/v1/rooms?min_price=75&max_price=150');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.price_per_night', '100.00');
    }

    /**
     * Test searching rooms by title.
     */
    public function test_can_search_rooms_by_title(): void
    {
        Room::factory()->create(['title' => 'Deluxe Suite']);
        Room::factory()->create(['title' => 'Standard Room']);

        $response = $this->getJson('/api/v1/rooms?search=Deluxe');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Deluxe Suite');
    }

    /**
     * Test admin can create room.
     */
    public function test_admin_can_create_room(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/rooms', [
                'title' => 'New Room',
                'description' => 'A beautiful new room',
                'price_per_night' => 150,
                'capacity' => 2,
            ]);

        $response->assertCreated()
            ->assertJsonPath('title', 'New Room');

        $this->assertDatabaseHas('rooms', [
            'title' => 'New Room',
        ]);
    }

    /**
     * Test non-admin cannot create room.
     */
    public function test_non_admin_cannot_create_room(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/rooms', [
                'title' => 'New Room',
                'description' => 'A beautiful new room',
                'price_per_night' => 150,
                'capacity' => 2,
            ]);

        $response->assertForbidden();
    }

    /**
     * Test admin can update room.
     */
    public function test_admin_can_update_room(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::factory()->create(['title' => 'Old Title']);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/rooms/{$room->id}", [
                'title' => 'Updated Title',
                'price_per_night' => 200,
            ]);

        $response->assertOk()
            ->assertJsonPath('title', 'Updated Title');

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'title' => 'Updated Title',
        ]);
    }

    /**
     * Test admin can delete room.
     */
    public function test_admin_can_delete_room(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/rooms/{$room->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }
}
