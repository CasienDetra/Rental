@extends('layouts.app')

@section('title', 'Booking Details - RoomRent')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('bookings.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold mb-8 inline-flex items-center gap-2">
        <span>←</span> Back to Bookings
    </a>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Booking Details -->
        <div class="md:col-span-2">
            <!-- Status Badge -->
            <div class="mb-6">
                @if($booking->status === 'confirmed')
                    <span class="px-4 py-2 bg-green-100 text-green-800 font-bold rounded-full text-lg">✓ Confirmed</span>
                @elseif($booking->status === 'pending')
                    <span class="px-4 py-2 bg-yellow-100 text-yellow-800 font-bold rounded-full text-lg">⏳ Pending Confirmation</span>
                @elseif($booking->status === 'cancelled')
                    <span class="px-4 py-2 bg-red-100 text-red-800 font-bold rounded-full text-lg">✗ Cancelled</span>
                @endif
            </div>

            <!-- Booking Card -->
            <div class="bg-white rounded-lg shadow p-8 mb-8">
                <h1 class="text-3xl font-bold mb-2">Booking #{{ $booking->id }}</h1>
                <p class="text-gray-600 mb-8">{{ $booking->created_at->format('F d, Y') }}</p>

                <!-- Room Info -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-2xl font-bold mb-4">Room Details</h2>
                    <div class="bg-gray-50 p-6 rounded-lg space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Room Name:</span>
                            <span class="font-semibold">{{ $booking->room->title }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Capacity:</span>
                            <span class="font-semibold">{{ $booking->room->capacity }} guests</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Price per night:</span>
                            <span class="font-semibold">${{ number_format($booking->room->price_per_night, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Dates Info -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-2xl font-bold mb-4">Stay Details</h2>
                    <div class="bg-blue-50 p-6 rounded-lg space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Check-in:</span>
                            <span class="font-semibold">{{ $booking->check_in_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Check-out:</span>
                            <span class="font-semibold">{{ $booking->check_out_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Number of nights:</span>
                            <span class="font-semibold">{{ $booking->check_out_date->diffInDays($booking->check_in_date) }} nights</span>
                        </div>
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Price Summary</h2>
                    <div class="bg-gray-50 p-6 rounded-lg space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">${{ number_format($booking->room->price_per_night, 2) }} × {{ $booking->check_out_date->diffInDays($booking->check_in_date) }} nights</span>
                            <span class="font-semibold">${{ number_format($booking->total_price * 0.909, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Service fee (10%)</span>
                            <span class="font-semibold">${{ number_format($booking->total_price * 0.091, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-3 border-t">
                            <span>Total</span>
                            <span class="text-blue-600">${{ number_format($booking->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Host Info -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="font-bold text-lg mb-3">Host</h3>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($booking->room->user_id ?? 'Admin', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold">Room Administrator</p>
                            <p class="text-gray-600 text-sm">Verified host</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($booking->status === 'pending' && auth()->id() === $booking->user_id)
                <div class="flex gap-4">
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition font-semibold">
                            Cancel Booking
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-20">
                <h3 class="text-xl font-bold mb-6">Booking Status</h3>

                @if($booking->status === 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-6">
                        <p class="text-sm text-yellow-800">
                            <strong>⏳ Awaiting Confirmation</strong>
                            Your booking is pending admin approval. You'll receive confirmation within 24 hours.
                        </p>
                    </div>
                @elseif($booking->status === 'confirmed')
                    <div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-6">
                        <p class="text-sm text-green-800">
                            <strong>✓ Booking Confirmed</strong>
                            Your reservation is confirmed. Check your email for details.
                        </p>
                    </div>
                @elseif($booking->status === 'cancelled')
                    <div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-6">
                        <p class="text-sm text-red-800">
                            <strong>✗ Booking Cancelled</strong>
                            This booking has been cancelled.
                        </p>
                    </div>
                @endif

                <!-- Contact Host -->
                <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold mb-3">
                    Message Host
                </button>

                <!-- Download Invoice -->
                <button class="w-full border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition font-semibold">
                    Download Invoice
                </button>

                <!-- Help -->
                <div class="mt-6 pt-6 border-t">
                    <p class="text-sm text-gray-600 mb-3">Need help?</p>
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
