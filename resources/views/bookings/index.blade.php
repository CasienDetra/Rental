@extends('layouts.app')

@section('title', 'My Bookings - RoomRent')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold mb-2">My Bookings</h1>
    <p class="text-gray-600">View and manage all your room reservations</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-12">
    <!-- Stats Cards -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-3xl mb-2">📅</div>
        <p class="text-gray-600 text-sm">Total Bookings</p>
        <p class="text-2xl font-bold">{{ $bookings->total() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-3xl mb-2">✓</div>
        <p class="text-gray-600 text-sm">Confirmed</p>
        <p class="text-2xl font-bold">{{ $bookings->where('status', 'confirmed')->count() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-3xl mb-2">⏳</div>
        <p class="text-gray-600 text-sm">Pending</p>
        <p class="text-2xl font-bold">{{ $bookings->where('status', 'pending')->count() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-3xl mb-2">🔗</div>
        <p class="text-gray-600 text-sm">Browse Rooms</p>
        <a href="{{ route('rooms.index') }}" class="text-blue-600 hover:text-blue-700 font-bold text-2xl">View →</a>
    </div>
</div>

@if($bookings->count() > 0)
    <!-- Bookings List -->
    <div class="space-y-4">
        @foreach($bookings as $booking)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                    <!-- Room Info -->
                    <div>
                        <p class="text-sm text-gray-600">Room</p>
                        <h3 class="font-bold text-lg">{{ $booking->room->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $booking->room->capacity }} guests</p>
                    </div>

                    <!-- Dates -->
                    <div>
                        <p class="text-sm text-gray-600">Check-in</p>
                        <p class="font-bold">{{ $booking->check_in_date->format('M d, Y') }}</p>
                        <p class="text-sm text-gray-600 mt-3">Check-out</p>
                        <p class="font-bold">{{ $booking->check_out_date->format('M d, Y') }}</p>
                    </div>

                    <!-- Status & Price -->
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        @if($booking->status === 'confirmed')
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-800 font-semibold rounded-full text-sm">✓ Confirmed</span>
                        @elseif($booking->status === 'pending')
                            <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 font-semibold rounded-full text-sm">⏳ Pending</span>
                        @else
                            <span class="inline-block px-3 py-1 bg-red-100 text-red-800 font-semibold rounded-full text-sm">✗ Cancelled</span>
                        @endif
                        
                        <p class="text-sm text-gray-600 mt-3">Total Price</p>
                        <p class="text-xl font-bold text-blue-600">${{ number_format($booking->total_price, 2) }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('bookings.show', $booking) }}" class="bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-center font-semibold">
                            View Details
                        </a>
                        @if($booking->status === 'pending')
                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full border border-red-300 text-red-600 py-2 rounded-lg hover:bg-red-50 transition font-semibold">
                                    Cancel
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
        <div class="mt-8">
            {{ $bookings->links() }}
        </div>
    @endif
@else
    <!-- Empty State -->
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3"></path>
        </svg>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">No Bookings Yet</h3>
        <p class="text-gray-600 mb-6">You haven't made any bookings. Start exploring and find your perfect room!</p>
        <a href="{{ route('rooms.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
            Browse Rooms
        </a>
    </div>
@endif
@endsection
