# Rental Room Management System

A modern **RESTful API** built with **Laravel 13**, **Eloquent ORM**, and **Sanctum authentication** for managing room bookings with full user and admin capabilities.

---

## 🎯 Features Implemented

### Core Architecture
- ✅ **Eloquent Models** with relationships: User, Room, Booking
- ✅ **Database Migrations** with proper schema and constraints
- ✅ **API Authentication** using Laravel Sanctum (token-based)
- ✅ **Authorization Policies** for secure access control
- ✅ **RESTful API** with 14 fully functional endpoints
- ✅ **Comprehensive Tests** for all major features
- ✅ **Factory Classes** for testing and data seeding

### API Endpoints (v1)

#### Authentication (Public)
- `POST /api/v1/auth/register` - User registration
- `POST /api/v1/auth/login` - User login
- `POST /api/v1/auth/logout` - User logout (authenticated)
- `GET /api/v1/auth/me` - Get authenticated user

#### Rooms (Public Browse, Admin Manage)
- `GET /api/v1/rooms` - Browse all rooms (with filtering & search)
  - Query params: `available`, `min_price`, `max_price`, `capacity`, `search`
- `GET /api/v1/rooms/{room}` - View single room
- `POST /api/v1/rooms` - Create room (admin only)
- `PUT /api/v1/rooms/{room}` - Update room (admin only)
- `DELETE /api/v1/rooms/{room}` - Delete room (admin only)

#### Bookings (User & Admin)
- `GET /api/v1/bookings` - List user's bookings
- `GET /api/v1/bookings/{booking}` - View single booking
- `POST /api/v1/bookings` - Create booking
  - Auto-calculates total_price based on nights
  - Prevents double-booking with conflict detection
- `POST /api/v1/bookings/{booking}/cancel` - Cancel booking
- `POST /api/v1/bookings/{booking}/confirm` - Confirm booking (admin only)

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/AuthController.php      # Auth endpoints
│   │   ├── RoomController.php           # Room CRUD
│   │   └── BookingController.php        # Booking management
│   └── Middleware/
│       └── IsAdmin.php                  # Admin authorization
├── Models/
│   ├── User.php                         # User model with bookings
│   ├── Room.php                         # Room model with bookings
│   └── Booking.php                      # Booking model with relationships
└── Policies/
    ├── RoomPolicy.php                   # Room authorization
    └── BookingPolicy.php                # Booking authorization

database/
├── migrations/
│   ├── create_users_table.php           # User schema
│   ├── create_rooms_table.php           # Room schema
│   └── create_bookings_table.php        # Booking schema
└── factories/
    ├── UserFactory.php
    ├── RoomFactory.php
    └── BookingFactory.php

tests/
├── Feature/
│   ├── Auth/AuthenticationTest.php
│   ├── RoomTest.php
│   └── BookingTest.php
└── Unit/
```

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.3+
- Laravel 13
- Composer
- MySQL (or SQLite for testing)

### Installation

```bash
# Clone the repository
git clone <repository>
cd assignment-larvel

# Install dependencies
composer install

# Create .env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# (Optional) Seed database
php artisan db:seed
```

### Run Development Server

```bash
php artisan serve
```

API will be available at: `http://localhost:8000/api/v1`

---

## 📋 Database Schema

### Users Table
```
id (PK)
name (string)
email (unique)
password (hashed)
is_admin (boolean, default: false)
email_verified_at (nullable timestamp)
timestamps
```

### Rooms Table
```
id (PK)
title (string)
description (text)
price_per_night (decimal)
capacity (integer)
available (boolean, default: true)
image_path (nullable string)
timestamps
```

### Bookings Table
```
id (PK)
user_id (FK -> users)
room_id (FK -> rooms)
check_in_date (datetime)
check_out_date (datetime)
total_price (decimal)
status (enum: pending, confirmed, cancelled)
timestamps
```

---

## 🔐 Authentication Flow

1. **Register**: POST `/api/v1/auth/register`
   ```json
   {
     "name": "John Doe",
     "email": "john@example.com",
     "password": "SecurePass123!",
     "password_confirmation": "SecurePass123!"
   }
   ```

2. **Login**: POST `/api/v1/auth/login`
   ```json
   {
     "email": "john@example.com",
     "password": "SecurePass123!"
   }
   ```
   Response includes `token` - use in Authorization header

3. **Use Token**: Add to all authenticated requests
   ```
   Authorization: Bearer <token>
   ```

---

## 🧪 Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
php artisan test tests/Feature/Auth
php artisan test tests/Feature/RoomTest
php artisan test tests/Feature/BookingTest
```

### Tests Coverage
- ✅ User registration & validation
- ✅ User login & logout
- ✅ Room browsing, filtering, search
- ✅ Admin-only room CRUD
- ✅ Booking creation with conflict detection
- ✅ Price calculation
- ✅ Booking cancellation & confirmation
- ✅ Authorization policies

---

## 📚 Models & Relationships

### User Model
```php
- hasMany('Booking') -> User has many bookings
- Uses Sanctum traits for API authentication
```

### Room Model
```php
- hasMany('Booking') -> Room has many bookings
- Casts for price_per_night and available boolean
```

### Booking Model
```php
- belongsTo('User')
- belongsTo('Room')
- Auto-calculates total price during creation
- Prevents double-booking with date range checking
```

---

## 🛠️ Development Commands

```bash
# Create a new model with migration
php artisan make:model ModelName -m

# Create a controller
php artisan make:controller ControllerName

# Create a policy
php artisan make:policy PolicyName --model=ModelName

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh database
php artisan migrate:fresh

# Seed database
php artisan db:seed

# List all routes
php artisan route:list --path=api
```

---

## 🔑 Key Features

### Smart Booking Management
- **Conflict Detection**: Prevents booking overlapping dates
- **Auto-Price Calculation**: Total = nights × price_per_night
- **Status Tracking**: pending → confirmed → cancelled
- **User Isolation**: Users can only view/manage their own bookings

### Security
- **Token Authentication**: Sanctum API tokens
- **Authorization Policies**: Fine-grained access control
- **Input Validation**: All endpoints validate input
- **SQL Injection Prevention**: Eloquent parameterized queries
- **Admin Middleware**: Protects admin-only endpoints

### Search & Filtering
```
GET /api/v1/rooms?available=true&min_price=50&max_price=200&capacity=2&search=deluxe
```

---

## 📖 API Response Format

### Success Response (200/201)
```json
{
  "id": 1,
  "title": "Deluxe Suite",
  "description": "...",
  "price_per_night": "150.00",
  "capacity": 2,
  "available": true
}
```

### Error Response (422)
```json
{
  "message": "The given data was invalid",
  "errors": {
    "email": ["The email has already been taken"]
  }
}
```

### Paginated Response (200)
```json
{
  "data": [...],
  "links": {...},
  "meta": {
    "current_page": 1,
    "per_page": 12,
    "total": 50
  }
}
```

---

## 🚨 Git Commits

All features implemented with individual commits:

1. ✅ `feat: add core models (Room, Booking) and migrations with relationships`
2. ✅ `fix: update users table to follow Laravel conventions (id, is_admin)`
3. ✅ `feat: create factories for User, Room, and Booking models`
4. ✅ `feat: add authentication system with Sanctum and complete API routes`
5. ✅ `feat: add Sanctum API token support to User model`
6. ✅ `feat: add comprehensive feature tests for auth, rooms, and bookings`

---

## 🎓 Best Practices Applied

- **PHP 8.2+ Features**: Typed properties, named arguments, match expressions
- **Laravel Best Practices**: Eloquent relationships, policies, factories
- **RESTful Design**: Proper HTTP verbs, status codes, resource routes
- **Security**: Input validation, authorization, SQL injection prevention
- **Testing**: Feature tests for all major functionality
- **Code Organization**: Clear separation of concerns
- **Git Workflow**: Atomic commits with meaningful messages

---

## 📝 Environment Variables

Key variables in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=m3WebProject
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:3000
```

---

## 📞 Support

For issues or questions, please check the Laravel documentation at https://laravel.com/docs

---

**Status**: ✅ Core features complete and tested
**Ready for**: Frontend integration, additional features, production deployment
