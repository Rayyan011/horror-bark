---
name: horror-bark-resort
description: Full-stack development for Horror-Bark theme park and island resort booking platform. Use when working on Laravel 11 multi-panel booking systems with Filament 3 admin interfaces. Triggers on mentions of Horror-Bark, theme park bookings, multi-type reservations (hotels, ferries, rides, games, beach events), Filament panels, resort platforms, or island tourism systems.
---

# Horror-Bark Resort Platform Development

## Overview

Development guide for Horror-Bark, a comprehensive theme park and island resort booking platform with multiple booking types and operator panels.

## Tech Stack

- **Backend**: Laravel 11.31 (PHP 8.2+)
- **Admin Interface**: Filament 3.3 with multi-panel architecture
- **Frontend**: Blade templates + Tailwind CSS 3.4 + Alpine.js (via Livewire)
- **Build Tool**: Vite
- **Database**: MySQL (Docker) or SQLite (local)
- **Maps**: Dotswan MapPicker (ArcGIS satellite tiles) + fathihfaiz/laravel-maps-suite (Leaflet on frontend)
- **Permissions**: Spatie Laravel Permission + FilamentShield
- **PDF Generation**: barryvdh/laravel-dompdf (invoices)
- **Currency**: MVR (Maldivian Rufiyaa)

## Architecture Overview

### Multi-Panel System

Horror-Bark uses 6 separate Filament admin panels for role-based management:

```
/admin  → Main administrator panel (Amber, full access, registration enabled)
/hotel  → Hotel operators (Rose, manage rooms and hotel bookings)
/ferry  → Ferry operators (Amber, manage ferries and ferry bookings)
/ride   → Ride operators (Amber, manage rides and ride bookings)
/game   → Game operators (Blue, manage games and game bookings)
/user   → Customer panel (Indigo)
```

Each panel discovers resources from its own namespace:
- Admin: `App\Filament\Resources`
- Hotel: `App\Filament\Hotel\Resources`
- Ferry: `App\Filament\Ferry\Resources`
- Ride: `App\Filament\Ride\Resources`
- Game: `App\Filament\Game\Resources`
- User: `App\Filament\User\Resources`

Operator panels scope queries to the authenticated user via `getEloquentQuery()`.

### Database Structure

See `references/database_schema.md` for complete schema details.

**Core Models (16 total):**
- User (customers and operators, with Spatie roles)
- Hotel, Room, HotelBooking
- Ferry, FerryBooking
- Ride, RideBooking
- Game, GameBooking
- BeachEvent, BeachEventBooking
- Invoice (polymorphic, links to any booking type)
- Island, Contact, Page (CMS)

### Owner/Operator Pattern

Ferries, Rides, Games, and BeachEvents all have a `user_id` field referencing the operator/owner. The relationship is defined as:
```php
public function owner() { return $this->belongsTo(User::class, 'user_id'); }
```

### Invoice System

All bookings automatically generate a polymorphic invoice with PDF:

```php
// InvoiceService creates invoice on every booking
$invoiceService->createForBooking($booking, $userId, (float) $booking->total_price);

// Invoice fields: invoice_number (INV-YYYYMMDD-XXXXXX), invoiceable_type, invoiceable_id,
//   user_id, amount, status ('issued'), issued_at, pdf_path
// PDF generated via DomPDF from 'invoices.pdf' Blade view, stored in storage/app/invoices/
```

On cancellation, both booking and invoice status are set to `'canceled'`.

## Booking System Implementation

### 5 Booking Types

All bookings share these fields: `user_id`, `quantity`, `total_price`, `status`.
Status values: `pending`, `confirmed`, `canceled` (note: "canceled" with one L).
There is **no** `payment_status` or `booking_reference` field on bookings.

#### 1. Hotel Bookings
- **Date fields**: `start_date`, `end_date`
- **Capacity field**: `quantity` (max: room's `max_occupancy`)
- **Price calc**: `room.price * quantity * nights`
- **Overlap check**: Overlapping date ranges where `start_date < end_date AND end_date > start_date`

#### 2. Ferry Bookings
- **Time field**: `booking_time` (datetime, must be on the hour between 9:00-16:00)
- **Capacity field**: `quantity` (max: ferry's `max_booking_quantity`, total per slot: `max_capacity`)
- **Price calc**: `ferry.price * quantity`

#### 3. Ride Bookings
- **Time field**: `booking_time` (datetime, only 9:00 or 17:00 allowed)
- **Capacity field**: `quantity` (max: ride's `max_booking_quantity`, total per slot: `max_capacity`)
- **Price calc**: `ride.price * quantity`

#### 4. Game Bookings
- **Time field**: `booking_time` (datetime, only 9:00 or 17:00 allowed — same as rides)
- **Capacity field**: `quantity` (max: game's `max_booking_quantity`, total per slot: `max_capacity`)
- **Price calc**: `game.price * quantity`

#### 5. Beach Event Bookings
- **Date fields**: `booking_date` (must match event's `event_date`), `booking_time` (H:i format)
- **Capacity field**: `quantity` (max: event's `max_booking_quantity`, total: `max_capacity`)
- **Price calc**: `beachEvent.price * quantity`

## Public Frontend Routes

### Route Structure

```php
// Public routes
GET  /                    → HomeController@index         (home)
GET  /hotels              → HotelController@index        (hotels.index)
GET  /hotels/{hotel}      → HotelController@show         (hotels.show)
GET  /ferrytickets        → FerryController@index        (ferries.index)
GET  /themepark           → ThemeParkController@index    (themepark.index) — rides + games combined
GET  /beach-events        → BeachEventController@index   (beach-events.index)
GET  /contact             → ContactController@create     (contacts.create)
POST /contact             → ContactController@store      (contacts.store)
GET  /{page_name}         → PagesController@show         (custom_page) — CMS catch-all

// Authenticated routes
GET  /portal              → CustomerBookingController@index  (portal)
GET  /bookings            → CustomerBookingController@index  (bookings.index)

// Booking CRUD (auth required)
GET   /bookings/hotels/{hotelBooking}                → showHotel
GET   /bookings/ferries/{ferryBooking}               → showFerry
GET   /bookings/rides/{rideBooking}                  → showRide
GET   /bookings/games/{gameBooking}                  → showGame
GET   /bookings/beach-events/{beachEventBooking}     → showBeachEvent

POST  /bookings/hotels/rooms/{room}                  → HotelBookingController@store
POST  /bookings/ferries/{ferry}                      → FerryBookingController@store
POST  /bookings/rides/{ride}                         → RideBookingController@store
POST  /bookings/games/{game}                         → GameBookingController@store
POST  /bookings/beach-events/{beachEvent}            → BeachEventBookingController@store

PATCH /bookings/hotels/{hotelBooking}/cancel          → cancelHotel
PATCH /bookings/ferries/{ferryBooking}/cancel         → cancelFerry
PATCH /bookings/rides/{rideBooking}/cancel            → cancelRide
PATCH /bookings/games/{gameBooking}/cancel            → cancelGame
PATCH /bookings/beach-events/{beachEventBooking}/cancel → cancelBeachEvent

// Profile & Invoices
GET   /profile                       → ProfileController@edit
PATCH /profile                       → ProfileController@update
PATCH /profile/password              → ProfileController@updatePassword
GET   /invoices/{invoice}            → InvoiceController@show
GET   /invoices/{invoice}/download   → InvoiceController@download
```

### Authorization

All booking show/cancel actions are protected by Gate policies. Each booking type has a policy (e.g., `HotelBookingPolicy`) that checks `$booking->user_id === auth()->id()`.

### Frontend Components

Blade components in `resources/views/components/`:
- `featured-card.blade.php` — reusable card with image carousel, title, description, link
- `image-carousel.blade.php` — Alpine.js auto-rotating carousel (3s interval, pause on hover)
- `beach-event-card.blade.php` — beach event specific card

Layout uses Tailwind dark theme (`bg-gray-900`), horror-themed "Creepster" font for headings.

## Maps Integration

Two map integrations:
1. **Dotswan MapPicker** in Filament admin forms (entity location selection)
2. **Laravel Maps Suite** with Leaflet on the public homepage (displaying markers)

Default map center: lat `4.227`, lng `73.427` (Maldives).

Entities with coordinates: Hotels, Rides, Games, BeachEvents, Islands.

## Common Development Tasks

### Adding a New Booking Type

1. Create model with migration (include: `user_id`, `{entity}_id`, `booking_time`, `quantity`, `total_price`, `status`)
2. Define relationships (belongsTo User, belongsTo entity, morphOne Invoice)
3. Create booking controller extending Controller with `store()` method
4. Add validation (time slot rules, capacity checks)
5. Call `InvoiceService->createForBooking()` after creating the booking
6. Add routes in `web.php` under the auth middleware group
7. Create Filament resources for admin panel and operator panel
8. Add policy for authorization (view/update gates)
9. Register policy in `AppServiceProvider`
10. Add to `CustomerBookingController` (show, cancel, query builder, stats)

### Image Handling

```php
// In Filament resource forms
FileUpload::make('images')
    ->multiple()
    ->image()
    ->directory('{entity}/gallery')
    ->maxFiles(5)
    ->maxSize(1024) // 1MB
    ->imageEditor()
    ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1'])
    ->reorderable()
    ->appendFiles();
```

Images are stored as JSON arrays and cast with `'images' => 'array'` in models.

### Map Picker in Filament

```php
use Dotswan\MapPicker\Fields\Map;

Map::make('location_data')
    ->defaultLocation(latitude: 4.227, longitude: 73.427)
    ->draggable()->clickable()->zoom(16)
    ->tilesUrl('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}')
    ->afterStateHydrated(/* populate from record lat/lng */)
    ->afterStateUpdated(/* sync back to lat/lng fields */);
```

## Testing Guidelines

Run tests: `php artisan test` or `vendor/bin/phpunit` (from `application/`).
Format code: `php artisan pint` or `./vendor/bin/pint`.

### Key Areas to Test
- Capacity validation (overlapping hotel dates, time slot totals vs max_capacity)
- Time slot enforcement (ferry 9-16, ride/game 9 or 17 only, beach event date match)
- Invoice auto-creation on booking
- Invoice cancellation cascade
- Policy authorization (users can only view/cancel their own bookings)
- Operator panel scoping (only see own entities)

## References

Detailed reference documentation in `horror-bark-resort/references/`:

- `database_schema.md` — Complete database structure with all tables and relationships
- `booking_workflows.md` — Detailed booking logic and validation for each booking type
- `filament_patterns.md` — Common Filament resource patterns for the multi-panel system
