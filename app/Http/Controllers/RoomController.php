<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
     * Get a single room by ID (API).
     */
    public function showApi(Room $room): JsonResponse
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

    // Web View Methods

    /**
     * Show home page with featured rooms
     */
    public function home(): View
    {
        $featured_rooms = Room::where('available', true)
            ->limit(4)
            ->get();

        return view('home', compact('featured_rooms'));
    }

    /**
     * Show all rooms with filtering (web view)
     */
    public function indexView(Request $request): View
    {
        $query = Room::where('available', true);

        // Search
        if ($request->has('search')) {
            $search = $request->string('search');
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        // Price range filter
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price_per_night', [
                $request->float('min_price'),
                $request->float('max_price'),
            ]);
        }

        // Capacity filter
        if ($request->has('capacity')) {
            $query->where('capacity', '>=', $request->integer('capacity'));
        }

        $rooms = $query->paginate(12);

        return view('rooms.index', compact('rooms'));
    }

    /**
     * Show room details (web view)
     */
    public function show(Room $room): View
    {
        return view('rooms.show', compact('room'));
    }

    /**
     * Admin dashboard
     */
    public function adminDashboard(): View
    {
        $total_rooms = Room::count();
        $available_rooms = Room::where('available', true)->count();
        $total_bookings = Booking::count();
        $pending_bookings = Booking::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'total_rooms',
            'available_rooms',
            'total_bookings',
            'pending_bookings'
        ));
    }

    /**
     * Admin rooms list
     */
    public function adminIndex(): View
    {
        $rooms = Room::paginate(10);

        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Create room form
     */
    public function create(): View
    {
        return view('admin.rooms.create');
    }

    /**
     * Store room (web)
     */
    public function storeView(Request $request): RedirectResponse
    {
        $this->authorize('create', Room::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'available' => ['boolean'],
        ]);

        Room::create($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully!');
    }

    /**
     * Edit room form
     */
    public function edit(Room $room): View
    {
        $this->authorize('update', $room);

        return view('admin.rooms.edit', compact('room'));
    }

    /**
     * Update room (web)
     */
    public function updateView(Request $request, Room $room): RedirectResponse
    {
        $this->authorize('update', $room);

        $validated = $request->validate([
            'title' => ['string', 'max:255'],
            'description' => ['string'],
            'price_per_night' => ['numeric', 'min:0'],
            'capacity' => ['integer', 'min:1'],
            'available' => ['boolean'],
        ]);

        $room->update($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully!');
    }

    /**
     * Delete room (web)
     */
    public function destroyView(Room $room): RedirectResponse
    {
        $this->authorize('delete', $room);

        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully!');
    }
}
