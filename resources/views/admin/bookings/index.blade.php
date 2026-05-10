@extends('layouts.app')

@section('title', 'Manage Bookings - RoomRent')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold">Manage Bookings</h1>
    <p class="text-gray-600">Review and confirm customer bookings</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <form action="{{ route('admin.bookings.index') }}" method="GET" class="flex gap-4 items-end">
        <div class="flex-1">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
            <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="pending" @if(request('status') === 'pending') selected @endif>Pending</option>
                <option value="confirmed" @if(request('status') === 'confirmed') selected @endif>Confirmed</option>
                <option value="cancelled" @if(request('status') === 'cancelled') selected @endif>Cancelled</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">
            Filter
        </button>
        @if(request('status'))
            <a href="{{ route('admin.bookings.index') }}" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-50">
                Clear
            </a>
        @endif
    </form>
</div>

@if($bookings->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Booking ID</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Guest</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Room</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Dates</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Total Price</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($bookings as $booking)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-semibold">#{{ $booking->id }}</p>
                            <p class="text-sm text-gray-600">{{ $booking->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold">{{ $booking->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $booking->user->email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold">{{ $booking->room->title }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p><strong>Check-in:</strong> {{ $booking->check_in_date->format('M d, Y') }}</p>
                                <p><strong>Check-out:</strong> {{ $booking->check_out_date->format('M d, Y') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-blue-600">${{ number_format($booking->total_price, 2) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($booking->status === 'confirmed')
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 font-semibold rounded-full text-sm">✓ Confirmed</span>
                            @elseif($booking->status === 'pending')
                                <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 font-semibold rounded-full text-sm">⏳ Pending</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-red-100 text-red-800 font-semibold rounded-full text-sm">✗ Cancelled</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($booking->status === 'pending')
                                <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm font-semibold">
                                        Confirm
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-500 text-sm">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
        <div class="mt-8">
            {{ $bookings->links() }}
        </div>
    @endif
@else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3"></path>
        </svg>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">No Bookings</h3>
        <p class="text-gray-600">
            @if(request('status'))
                No {{ request('status') }} bookings found.
            @else
                No bookings yet.
            @endif
        </p>
    </div>
@endif
@endsection
