<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class BookingController extends Controller
{
    /**
     * Get all bookings for the authenticated user (API).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $bookings = Booking::where('user_id', $user->id)
            ->with(['room', 'user'])
            ->paginate(10);

        return response()->json($bookings);
    }

    /**
     * Get a single booking by ID (API).
     */
    public function showApi(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('view', $booking);

        return response()->json($booking->load(['room', 'user']));
    }

    /**
     * Create a new booking (API).
     */
    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
        ]);

        $room = Room::findOrFail($validated['room_id']);

        // Check for booking conflicts
        $existingBooking = Booking::where('room_id', $room->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('check_in_date', [$validated['check_in_date'], $validated['check_out_date']])
                    ->orWhereBetween('check_out_date', [$validated['check_in_date'], $validated['check_out_date']]);
            })
            ->exists();

        if ($existingBooking) {
            return response()->json([
                'message' => 'Room is not available for the selected dates',
            ], 409);
        }

        // Calculate total price
        $checkIn = new \DateTime($validated['check_in_date']);
        $checkOut = new \DateTime($validated['check_out_date']);
        $nights = $checkOut->diff($checkIn)->days;
        $totalPrice = $nights * (float) $room->price_per_night;

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'room_id' => $room->id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        return response()->json($booking->load(['room', 'user']), 201);
    }

    /**
     * Cancel a booking (API).
     */
    public function cancelApi(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('update', $booking);

        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'Booking is already cancelled',
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'booking' => $booking,
        ]);
    }

    /**
     * Confirm a booking (admin only, API).
     */
    public function confirmApi(Request $request, Booking $booking): JsonResponse
    {
        abort_unless($request->user()?->is_admin, 403);

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending bookings can be confirmed',
            ], 422);
        }

        $booking->update(['status' => 'confirmed']);

        return response()->json([
            'message' => 'Booking confirmed successfully',
            'booking' => $booking,
        ]);
    }

    // Web View Methods

    /**
     * Show user bookings list (web)
     */
    public function indexView(Request $request): View
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['room'])
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show booking details (web)
     */
    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load(['room', 'user']);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Create booking form (web)
     */
    public function create(Room $room): View
    {
        if (! $room->available) {
            abort(404, 'This room is not available for booking');
        }

        return view('bookings.create', compact('room'));
    }

    /**
     * Store booking (web)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
        ]);

        $room = Room::findOrFail($validated['room_id']);

        // Check for booking conflicts
        $existingBooking = Booking::where('room_id', $room->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('check_in_date', [$validated['check_in_date'], $validated['check_out_date']])
                    ->orWhereBetween('check_out_date', [$validated['check_in_date'], $validated['check_out_date']]);
            })
            ->exists();

        if ($existingBooking) {
            return redirect()->back()->withErrors(['checkout_date' => 'Room is not available for the selected dates']);
        }

        // Calculate total price
        $checkIn = new \DateTime($validated['check_in_date']);
        $checkOut = new \DateTime($validated['check_out_date']);
        $nights = $checkOut->diff($checkIn)->days;
        $totalPrice = $nights * (float) $room->price_per_night;

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking created! Awaiting admin confirmation.');
    }

    /**
     * Cancel booking (web)
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);

        if ($booking->status === 'cancelled') {
            return redirect()->back()->withErrors(['error' => 'Booking is already cancelled']);
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Booking cancelled successfully!');
    }

    /**
     * Admin bookings list (web)
     */
    public function adminIndex(Request $request): View
    {
        $query = Booking::with(['room', 'user']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->string('status'));
        }

        $bookings = $query->latest()->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Confirm booking (admin, web)
     */
    public function confirm(Booking $booking): RedirectResponse
    {
        abort_unless(auth()->user()->is_admin, 403);

        if ($booking->status !== 'pending') {
            return redirect()->back()->withErrors(['error' => 'Only pending bookings can be confirmed']);
        }

        $booking->update(['status' => 'confirmed']);

        return redirect()->back()->with('success', 'Booking confirmed successfully!');
    }
}
