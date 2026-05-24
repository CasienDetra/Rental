@extends('layouts.app')

@section('title', 'Manage Rooms - RoomRent')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-bold">Manage Rooms</h1>
        <p class="text-gray-600">Add, edit, or delete rental rooms</p>
    </div>
    <a href="{{ route('admin.rooms.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
        + Add New Room
    </a>
</div>

@if($rooms->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Room Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Capacity</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Price/Night</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($rooms as $room)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div>
                                <h4 class="font-semibold">{{ $room->title }}</h4>
                                <p class="text-sm text-gray-600">{{ Str::limit($room->description, 50) }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold">{{ $room->capacity }} guests</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-blue-600">{{ $room->currency->symbol() }}{{ number_format($room->price_per_night, 2) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($room->available)
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 font-semibold rounded-full text-sm">Available</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-red-100 text-red-800 font-semibold rounded-full text-sm">Unavailable</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.rooms.edit', $room) }}" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm font-semibold">
                                    Edit
                                </a>
                                <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Are you sure?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm font-semibold">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($rooms->hasPages())
        <div class="mt-8">
            {{ $rooms->links() }}
        </div>
    @endif
@else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">No Rooms Yet</h3>
        <p class="text-gray-600 mb-6">Start by adding your first rental room to the system.</p>
        <a href="{{ route('admin.rooms.create') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
            Create Your First Room
        </a>
    </div>
@endif
@endsection
