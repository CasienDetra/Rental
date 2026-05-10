@extends('layouts.app')

@section('title', 'Browse Rooms - RoomRent')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold mb-2">Browse Rooms</h1>
    <p class="text-gray-600">Find the perfect room for your stay</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Sidebar Filters -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6 sticky top-20">
            <h3 class="text-xl font-bold mb-6">Filters</h3>

            <form action="{{ route('rooms.index') }}" method="GET" class="space-y-6">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input 
                        type="text" 
                        id="search" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Room title or description"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Price Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                    <div class="flex items-center gap-2">
                        <input 
                            type="number" 
                            name="min_price" 
                            value="{{ request('min_price', 0) }}"
                            placeholder="Min"
                            step="10"
                            class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <span class="text-gray-600">-</span>
                        <input 
                            type="number" 
                            name="max_price" 
                            value="{{ request('max_price', 10000) }}"
                            placeholder="Max"
                            step="10"
                            class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Minimum Capacity</label>
                    <select 
                        id="capacity" 
                        name="capacity"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Any</option>
                        <option value="1" @if(request('capacity') == 1) selected @endif>1 Guest</option>
                        <option value="2" @if(request('capacity') == 2) selected @endif>2 Guests</option>
                        <option value="3" @if(request('capacity') == 3) selected @endif>3 Guests</option>
                        <option value="4" @if(request('capacity') == 4) selected @endif>4+ Guests</option>
                    </select>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold"
                >
                    Apply Filters
                </button>

                @if(request()->filled(['search', 'min_price', 'max_price', 'capacity']))
                    <a 
                        href="{{ route('rooms.index') }}" 
                        class="w-full block text-center border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition"
                    >
                        Clear Filters
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Rooms Grid -->
    <div class="lg:col-span-3">
        @if($rooms->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                @foreach($rooms as $room)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                        <div class="bg-gradient-to-br from-blue-400 to-blue-600 h-48 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-2m-9-2l4 2m0-5L9 7m9 0L15 7"></path>
                            </svg>
                        </div>

                        <div class="p-5">
                            <h3 class="font-bold text-lg mb-2">{{ $room->title }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $room->description }}</p>

                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span class="text-3xl font-bold text-blue-600">${{ number_format($room->price_per_night, 2) }}</span>
                                    <span class="text-sm text-gray-600"> / night</span>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">Available</span>
                            </div>

                            <div class="flex items-center justify-between text-sm text-gray-600 mb-4 pb-4 border-b">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                                    </svg>
                                    {{ $room->capacity }} {{ $room->capacity === 1 ? 'guest' : 'guests' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5.951-1.488 5.951 1.488a1 1 0 001.169-1.409l-7-14z"></path>
                                    </svg>
                                    Rating: 4.5/5
                                </span>
                            </div>

                            <div class="flex gap-3">
                                <a href="{{ route('rooms.show', $room) }}" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-center font-semibold">
                                    View Details
                                </a>
                                @auth
                                    <a href="{{ route('bookings.create', $room) }}" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition text-center font-semibold">
                                        Book Now
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition text-center font-semibold">
                                        Book Now
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($rooms->hasPages())
                <div class="flex justify-center">
                    {{ $rooms->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No rooms found</h3>
                <p class="text-gray-600 mb-6">Try adjusting your filters or search terms</p>
                <a href="{{ route('rooms.index') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Clear Filters
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
