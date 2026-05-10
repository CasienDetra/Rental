<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class BookingController extends Controller
{
    /**
     * Get all bookings for the authenticated user.
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
     * Get a single booking by ID.
     */
    public function show(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('view', $booking);

        return response()->json($booking->load(['room', 'user']));
    }

    /**
     * Create a new booking.
     */
    public function store(Request $request): JsonResponse
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
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking): JsonResponse
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
     * Confirm a booking (admin only).
     */
    public function confirm(Request $request, Booking $booking): JsonResponse
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
}
