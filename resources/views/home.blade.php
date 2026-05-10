@extends('layouts.app')

@section('title', 'Home - RoomRent')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg p-12 mb-12">
    <div class="max-w-3xl">
        <h1 class="text-5xl font-bold mb-4">Find Your Perfect Room</h1>
        <p class="text-xl text-blue-100 mb-8">Discover comfortable and affordable rooms in prime locations. Book your stay with confidence.</p>
        <a href="{{ route('rooms.index') }}" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
            Browse Rooms Now
        </a>
    </div>
</div>

<!-- Features Section -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-white p-8 rounded-lg shadow">
        <div class="text-4xl mb-4">🔍</div>
        <h3 class="text-xl font-bold mb-2">Easy Search</h3>
        <p class="text-gray-600">Find rooms by location, price, and capacity. Use advanced filters to narrow down your options.</p>
    </div>
    <div class="bg-white p-8 rounded-lg shadow">
        <div class="text-4xl mb-4">⚡</div>
        <h3 class="text-xl font-bold mb-2">Quick Booking</h3>
        <p class="text-gray-600">Book your room in minutes. No complicated process, just simple and secure transactions.</p>
    </div>
    <div class="bg-white p-8 rounded-lg shadow">
        <div class="text-4xl mb-4">✓</div>
        <h3 class="text-xl font-bold mb-2">Verified Rooms</h3>
        <p class="text-gray-600">All rooms are verified and checked for quality. Stay with confidence knowing what to expect.</p>
    </div>
</div>

<!-- Latest Rooms Section -->
<div class="mb-12">
    <h2 class="text-3xl font-bold mb-8">Featured Rooms</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($featured_rooms as $room)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                <div class="bg-gray-200 h-48 rounded-t-lg flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-2m-9-2l4 2m0-5L9 7m9 0L15 7"></path>
                    </svg>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2">{{ $room->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($room->description, 60) }}</p>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-2xl font-bold text-blue-600">${{ number_format($room->price_per_night, 2) }}</span>
                        <span class="text-sm text-gray-600">per night</span>
                    </div>
                    <div class="flex items-center justify-between mb-4 text-sm text-gray-600">
                        <span>👥 {{ $room->capacity }} guests</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Available</span>
                    </div>
                    <a href="{{ route('rooms.show', $room) }}" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition text-center">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-4 text-center py-12">
                <p class="text-gray-600">No rooms available at the moment.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- CTA Section -->
<div class="bg-blue-50 rounded-lg p-12 text-center">
    <h2 class="text-3xl font-bold mb-4">Ready to Book?</h2>
    <p class="text-gray-600 mb-8">Join thousands of satisfied customers and find your perfect room today.</p>
    @guest
        <a href="{{ route('register') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
            Get Started Now
        </a>
    @else
        <a href="{{ route('rooms.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
            Browse All Rooms
        </a>
    @endguest
</div>
@endsection
