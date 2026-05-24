@extends('layouts.app')

@section('title', 'Book ' . $room->title . ' - RoomRent')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-2">Confirm Your Booking</h1>
    <p class="text-gray-600 mb-8">{{ $room->title }}</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Booking Form -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow p-8">
                <form action="{{ route('bookings.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="room_id" value="{{ $room->id }}">

                    <!-- Check-in Date -->
                    <div class="mb-6">
                        <label for="check_in_date" class="block text-sm font-medium text-gray-700 mb-2">Check-in Date *</label>
                        <input 
                            type="date" 
                            id="check_in_date" 
                            name="check_in_date" 
                            value="{{ old('check_in_date') }}"
                            min="{{ now()->format('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('check_in_date') border-red-500 @enderror"
                            required
                        >
                        @error('check_in_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Check-out Date -->
                    <div class="mb-6">
                        <label for="check_out_date" class="block text-sm font-medium text-gray-700 mb-2">Check-out Date *</label>
                        <input 
                            type="date" 
                            id="check_out_date" 
                            name="check_out_date" 
                            value="{{ old('check_out_date') }}"
                            min="{{ now()->addDay()->format('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('check_out_date') border-red-500 @enderror"
                            required
                        >
                        @error('check_out_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Room Info -->
                    <div class="bg-blue-50 p-6 rounded-lg mb-8 border border-blue-200">
                        <h3 class="font-bold text-lg mb-4">Room Information</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Room:</span>
                                <span class="font-semibold">{{ $room->title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Capacity:</span>
                                <span class="font-semibold">{{ $room->capacity }} {{ $room->capacity === 1 ? 'guest' : 'guests' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Price per night:</span>
                                <span class="font-semibold">{{ $room->currency->symbol() }}{{ number_format($room->price_per_night, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="mb-6">
                        <label class="flex items-center gap-3">
                            <input 
                                type="checkbox" 
                                name="agree_terms" 
                                class="w-4 h-4 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                required
                            >
                            <span class="text-sm text-gray-600">I agree to the <a href="#" class="text-blue-600 hover:text-blue-700">Terms & Conditions</a> and <a href="#" class="text-blue-600 hover:text-blue-700">House Rules</a></span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-4">
                        <a href="{{ route('rooms.show', $room) }}" class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-50 transition text-center">
                            Cancel
                        </a>
                        <button 
                            type="submit" 
                            class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
                        >
                            Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow p-8 sticky top-20">
                <h3 class="text-xl font-bold mb-6">Booking Summary</h3>

                <div class="space-y-3 mb-6 pb-6 border-b">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Check-in</span>
                        <span class="font-semibold" id="summary-checkin">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Check-out</span>
                        <span class="font-semibold" id="summary-checkout">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nights</span>
                        <span class="font-semibold" id="summary-nights">0</span>
                    </div>
                </div>

                <div class="space-y-2 mb-6 pb-6 border-b">
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ $room->currency->symbol() }}{{ number_format($room->price_per_night, 2) }} × <span id="price-nights">0</span> nights</span>
                        <span class="font-semibold" id="subtotal">{{ $room->currency->symbol() }}0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service fee</span>
                        <span class="font-semibold" id="fee">{{ $room->currency->symbol() }}0.00</span>
                    </div>
                </div>

                <div class="flex justify-between text-lg font-bold mb-6">
                    <span>Total</span>
                    <span class="text-blue-600" id="total">{{ $room->currency->symbol() }}0.00</span>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded text-sm text-yellow-800">
                    <p>⚠️ This booking requires admin confirmation before it's finalized.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const pricePerNight = {{ $room->price_per_night }};
        const currencySymbol = '{{ $room->currency->symbol() }}';

        function updateSummary() {
            const checkIn = document.getElementById('check_in_date').value;
            const checkOut = document.getElementById('check_out_date').value;

            if (checkIn && checkOut) {
                const checkInDate = new Date(checkIn);
                const checkOutDate = new Date(checkOut);
                const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));

                if (nights > 0) {
                    document.getElementById('summary-checkin').textContent = new Date(checkIn).toLocaleDateString();
                    document.getElementById('summary-checkout').textContent = new Date(checkOut).toLocaleDateString();
                    document.getElementById('summary-nights').textContent = nights;
                    document.getElementById('price-nights').textContent = nights;

                    const subtotal = nights * pricePerNight;
                    const fee = Math.round(subtotal * 0.1 * 100) / 100; // 10% service fee
                    const total = subtotal + fee;

                    document.getElementById('subtotal').textContent = currencySymbol + subtotal.toFixed(2);
                    document.getElementById('fee').textContent = currencySymbol + fee.toFixed(2);
                    document.getElementById('total').textContent = currencySymbol + total.toFixed(2);
                }
            }
        }

        document.getElementById('check_in_date').addEventListener('change', updateSummary);
        document.getElementById('check_out_date').addEventListener('change', updateSummary);
    </script>
</div>
@endsection
