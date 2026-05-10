<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RoomController extends Controller
{
    /**
     * Get all rooms with optional filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Room::query();

        // Filter by availability
        if ($request->has('available')) {
            $query->where('available', $request->boolean('available'));
        }

        // Filter by price range
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price_per_night', [
                $request->float('min_price'),
                $request->float('max_price'),
            ]);
        }

        // Filter by capacity
        if ($request->has('capacity')) {
            $query->where('capacity', '>=', $request->integer('capacity'));
        }

        // Search in title and description
        if ($request->has('search')) {
            $search = $request->string('search');
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $rooms = $query->paginate(12);

        return response()->json($rooms);
    }

    /**
     * Get a single room by ID.
     */
    public function show(Room $room): JsonResponse
    {
        return response()->json($room);
    }

    /**
     * Create a new room (admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Room::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'available' => ['boolean'],
            'image_path' => ['nullable', 'string'],
        ]);

        $room = Room::create($validated);

        return response()->json($room, 201);
    }

    /**
     * Update a room (admin only).
     */
    public function update(Request $request, Room $room): JsonResponse
    {
        $this->authorize('update', $room);

        $validated = $request->validate([
            'title' => ['string', 'max:255'],
            'description' => ['string'],
            'price_per_night' => ['numeric', 'min:0'],
            'capacity' => ['integer', 'min:1'],
            'available' => ['boolean'],
            'image_path' => ['nullable', 'string'],
        ]);

        $room->update($validated);

        return response()->json($room);
    }

    /**
     * Delete a room (admin only).
     */
    public function destroy(Request $request, Room $room): JsonResponse
    {
        $this->authorize('delete', $room);

        $room->delete();

        return response()->json([
            'message' => 'Room deleted successfully',
        ]);
    }
}
