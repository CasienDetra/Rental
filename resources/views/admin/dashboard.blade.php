@extends('layouts.app')

@section('title', 'Admin Dashboard - RoomRent')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold">Admin Dashboard</h1>
    <p class="text-gray-600">Manage your rental rooms and bookings</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Rooms</p>
                <p class="text-3xl font-bold">{{ $total_rooms }}</p>
            </div>
            <div class="text-4xl">🏠</div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Available</p>
                <p class="text-3xl font-bold">{{ $available_rooms }}</p>
            </div>
            <div class="text-4xl">✓</div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Bookings</p>
                <p class="text-3xl font-bold">{{ $total_bookings }}</p>
            </div>
            <div class="text-4xl">📅</div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Pending</p>
                <p class="text-3xl font-bold">{{ $pending_bookings }}</p>
            </div>
            <div class="text-4xl">⏳</div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-8 text-white">
        <h3 class="text-xl font-bold mb-2">Manage Rooms</h3>
        <p class="text-blue-100 mb-4">Add, edit, or remove rental rooms</p>
        <a href="{{ route('admin.rooms.index') }}" class="inline-block bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 font-semibold">
            Go to Rooms →
        </a>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow p-8 text-white">
        <h3 class="text-xl font-bold mb-2">Manage Bookings</h3>
        <p class="text-green-100 mb-4">Confirm or manage customer bookings</p>
        <a href="{{ route('admin.bookings.index') }}" class="inline-block bg-white text-green-600 px-4 py-2 rounded-lg hover:bg-green-50 font-semibold">
            Go to Bookings →
        </a>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow p-8 text-white">
        <h3 class="text-xl font-bold mb-2">Create Room</h3>
        <p class="text-purple-100 mb-4">Add a new rental room to your inventory</p>
        <a href="{{ route('admin.rooms.create') }}" class="inline-block bg-white text-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50 font-semibold">
            Create New →
        </a>
    </div>
</div>

<!-- Quick Access Tables -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Bookings -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold">Pending Bookings</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <p class="text-gray-600 text-sm">You have {{ $pending_bookings }} bookings awaiting confirmation</p>
                <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Review Pending Bookings
                </a>
            </div>
        </div>
    </div>

    <!-- Room Management -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold">Rooms Overview</h3>
        </div>
        <div class="p-6">
            <div class="space-y-2">
                <p class="text-gray-600 text-sm">
                    <strong>{{ $total_rooms }}</strong> total rooms
                </p>
                <p class="text-gray-600 text-sm">
                    <strong>{{ $available_rooms }}</strong> available for booking
                </p>
                <div class="mt-4">
                    <a href="{{ route('admin.rooms.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        View All Rooms
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
