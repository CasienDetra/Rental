@extends('layouts.app')

@section('title', isset($room) ? 'Edit ' . $room->title : 'Create Room' . ' - RoomRent')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">{{ isset($room) ? 'Edit Room' : 'Create New Room' }}</h1>

    <div class="bg-white rounded-lg shadow p-8">
        <form action="{{ isset($room) ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}" method="POST">
            @csrf
            @if(isset($room))
                @method('PUT')
            @endif

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Room Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title', $room->title ?? '') }}"
                    placeholder="e.g., Luxury Master Bedroom"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                    required
                >
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="5"
                    placeholder="Describe your room, amenities, and what makes it special"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                    required
                >{{ old('description', $room->description ?? '') }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price per Night -->
            <div class="mb-6">
                <label for="price_per_night" class="block text-sm font-medium text-gray-700 mb-2">Price per Night</label>
                <input 
                    type="number" 
                    id="price_per_night" 
                    name="price_per_night" 
                    value="{{ old('price_per_night', $room->price_per_night ?? '') }}"
                    step="0.01"
                    min="0"
                    placeholder="99.99"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price_per_night') border-red-500 @enderror"
                    required
                >
                @error('price_per_night')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Capacity -->
            <div class="mb-6">
                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Guest Capacity</label>
                <input 
                    type="number" 
                    id="capacity" 
                    name="capacity" 
                    value="{{ old('capacity', $room->capacity ?? '1') }}"
                    min="1"
                    max="10"
                    placeholder="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('capacity') border-red-500 @enderror"
                    required
                >
                @error('capacity')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Available -->
            <div class="mb-8">
                <label class="flex items-center gap-3">
                    <input 
                        type="checkbox" 
                        name="available" 
                        value="1"
                        @if(old('available', isset($room) ? $room->available : true)) checked @endif
                        class="w-4 h-4 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                    >
                    <span class="text-sm font-medium text-gray-700">Room is available for booking</span>
                </label>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4">
                <a href="{{ route('admin.rooms.index') }}" class="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition text-center font-semibold">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition"
                >
                    {{ isset($room) ? 'Update Room' : 'Create Room' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
