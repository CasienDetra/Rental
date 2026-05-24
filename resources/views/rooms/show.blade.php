@extends('layouts.app')

@section('title', $room->title . ' - RoomRent')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Room Image -->
        <div class="bg-gradient-to-br from-blue-400 to-blue-600 h-96 rounded-lg flex items-center justify-center mb-8">
            <svg class="w-48 h-48 text-white opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-2m-9-2l4 2m0-5L9 7m9 0L15 7"></path>
            </svg>
        </div>

        <!-- Room Details -->
        <div class="bg-white rounded-lg shadow p-8 mb-8">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-4xl font-bold mb-2">{{ $room->title }}</h1>
                    <p class="text-gray-600">📍 Premium Location</p>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-bold text-blue-600">{{ $room->currency->symbol() }}{{ number_format($room->price_per_night, 2) }}</div>
                    <div class="text-gray-600">per night</div>
                </div>
            </div>

            <!-- Rating and Info -->
            <div class="flex items-center justify-between py-4 border-t border-b">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1">
                        <span class="text-yellow-400">★★★★☆</span>
                        <span class="text-gray-600">4.5 (28 reviews)</span>
                    </div>
                    <div class="text-gray-600">
                        👥 Sleeps {{ $room->capacity }} {{ $room->capacity === 1 ? 'guest' : 'guests' }}
                    </div>
                </div>
                @if($room->available)
                    <span class="px-4 py-2 bg-green-100 text-green-800 font-semibold rounded-full">Available</span>
                @else
                    <span class="px-4 py-2 bg-red-100 text-red-800 font-semibold rounded-full">Not Available</span>
                @endif
            </div>

            <!-- Description -->
            <div class="mt-8">
                <h2 class="text-2xl font-bold mb-4">About This Room</h2>
                <p class="text-gray-700 leading-relaxed mb-6">{{ $room->description }}</p>
            </div>

            <!-- Amenities -->
            <div class="mt-8">
                <h3 class="text-2xl font-bold mb-4">Amenities</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="text-2xl">🛏️</span>
                        <span>Comfortable Bed</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="text-2xl">📺</span>
                        <span>Smart TV</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="text-2xl">❄️</span>
                        <span>Air Conditioning</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="text-2xl">🚿</span>
                        <span>Modern Bathroom</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="text-2xl">📶</span>
                        <span>Free WiFi</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="text-2xl">🔒</span>
                        <span>24/7 Security</span>
                    </div>
                </div>
            </div>

            <!-- House Rules -->
            <div class="mt-8">
                <h3 class="text-2xl font-bold mb-4">House Rules</h3>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-center gap-3">
                        <span class="text-green-500">✓</span>
                        <span>Check-in after 3:00 PM</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="text-green-500">✓</span>
                        <span>Check-out before 11:00 AM</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="text-red-500">✗</span>
                        <span>No smoking inside the room</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="text-red-500">✗</span>
                        <span>No loud noise after 10:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Booking Card Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6 sticky top-20">
            <h3 class="text-2xl font-bold mb-6">Reserve This Room</h3>

            @auth
                @if($room->available)
                    <form action="{{ route('bookings.store') }}" method="POST">
                        @csrf
                        
                        <input type="hidden" name="room_id" value="{{ $room->id }}">

                        <!-- Check-in Date -->
                        <div class="mb-4">
                            <label for="check_in" class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                            <input 
                                type="date" 
                                id="check_in" 
                                name="check_in_date" 
                                value="{{ old('check_in_date') }}"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                            @error('check_in_date')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Check-out Date -->
                        <div class="mb-6">
                            <label for="check_out" class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                            <input 
                                type="date" 
                                id="check_out" 
                                name="check_out_date" 
                                value="{{ old('check_out_date') }}"
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                            @error('check_out_date')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price Summary -->
                         <div class="bg-gray-50 p-4 rounded-lg mb-6 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Price per night</span>
                                <span class="font-semibold">{{ $room->currency->symbol() }}{{ number_format($room->price_per_night, 2) }}</span>
                            </div>
                            <div class="flex justify-between pb-2 border-b">
                                <span class="text-gray-600">Number of nights</span>
                                <span class="font-semibold" id="nights">0</span>
                            </div>
                            <div class="flex justify-between text-lg">
                                <span class="font-bold">Total</span>
                                <span class="font-bold text-blue-600">{{ $room->currency->symbol() }}{{ number_format($room->price_per_night, 2) }} × <span id="total-nights">0</span></span>
                            </div>
                        </div>

                        <!-- Book Button -->
                        <button 
                            type="submit" 
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition"
                        >
                            Reserve Room
                        </button>
                    </form>

                    <script>
                        function calculateNights() {
                            const checkIn = document.getElementById('check_in').value;
                            const checkOut = document.getElementById('check_out').value;
                            
                            if (checkIn && checkOut) {
                                const checkInDate = new Date(checkIn);
                                const checkOutDate = new Date(checkOut);
                                const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
                                
                                if (nights > 0) {
                                    document.getElementById('nights').textContent = nights;
                                    document.getElementById('total-nights').textContent = nights;
                                }
                            }
                        }

                        document.getElementById('check_in').addEventListener('change', calculateNights);
                        document.getElementById('check_out').addEventListener('change', calculateNights);
                        calculateNights();
                    </script>
                @else
                    <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-center">
                        <p class="text-red-700 font-semibold mb-3">This room is currently not available</p>
                        <a href="{{ route('rooms.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Browse Other Rooms
                        </a>
                    </div>
                @endif
            @else
                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg text-center">
                    <p class="text-gray-700 mb-4">Sign in to make a reservation</p>
                    <a href="{{ route('login') }}" class="block w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-semibold mb-2">
                        Login
                    </a>
                    <p class="text-sm text-gray-600">or <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-semibold">create an account</a></p>
                </div>
            @endauth

            <!-- Share -->
            <div class="mt-6 pt-6 border-t">
                <p class="text-sm text-gray-600 mb-3">Share this room:</p>
                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm font-semibold">📱 Share</button>
                    <button class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm font-semibold">❤️ Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
