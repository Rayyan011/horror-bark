# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Horror-Bark is a Laravel 11 theme park and island resort booking platform with Filament 3 admin panels. The main application lives in `application/`. It powers a multi-type booking system for hotels, ferry tickets, theme park rides, games, and beach events.

## Common Commands

All commands run from `application/`:

```bash
# Development (runs server + queue + logs + Vite concurrently)
composer dev

# Individual commands
php artisan serve              # Laravel dev server
npm run dev                    # Vite dev server
npm run build                  # Build assets for production

# Database
php artisan migrate:fresh --seed   # Reset and seed database

# Testing and code quality
php artisan test               # Run PHPUnit tests
php artisan pint               # Format PHP code with Laravel Pint
```

## Architecture

### Multi-Panel Admin System

Six Filament admin panels with role-based access:

| Path | Panel | Purpose |
|------|-------|---------|
| `/admin` | Admin | Full access, all resources |
| `/hotel` | Hotel | Hotel operators manage rooms and hotel bookings |
| `/ferry` | Ferry | Ferry operators manage ferries and ferry bookings |
| `/ride` | Ride | Ride operators manage rides and ride bookings |
| `/game` | Game | Game operators manage games and game bookings |
| `/user` | User | Customer portal |

Each panel auto-discovers resources from its namespace (e.g., `App\Filament\Hotel\Resources`). Operator panels scope queries via `getEloquentQuery()` to show only the authenticated user's entities.

### Owner/Operator Pattern

Ferries, Rides, Games, and BeachEvents have a `user_id` referencing the operator/owner:
```php
public function owner() { return $this->belongsTo(User::class, 'user_id'); }
```

### Booking System

Five booking types share common fields: `user_id`, `quantity`, `total_price`, `status` (pending/confirmed/canceled).

**Time slot rules:**
- **Hotel**: Date range (`start_date`, `end_date`), capacity = room's `max_occupancy`
- **Ferry**: Hourly slots 9:00-16:00, capacity per slot = `max_capacity`
- **Ride/Game**: Only 9:00 or 17:00 slots, capacity per slot = `max_capacity`
- **Beach Event**: Must match event's `event_date`, capacity = `max_capacity`

### Invoice System

All bookings auto-generate polymorphic invoices with PDF (via DomPDF):
```php
$invoiceService->createForBooking($booking, $userId, (float) $booking->total_price);
```
PDFs stored in `storage/app/invoices/`. Invoice number format: `INV-YYYYMMDD-XXXXXX`.

### Authorization

Each booking type has a policy (e.g., `HotelBookingPolicy`) checking `$booking->user_id === auth()->id()`.

## Key Paths

- Filament resources: `application/app/Filament/` (organized by panel)
- Booking controllers: `application/app/Http/Controllers/Bookings/`
- Models: `application/app/Models/`
- Routes: `application/routes/web.php`
- Views: `application/resources/views/`
- Migrations: `application/database/migrations/`

## Tech Stack

- Laravel 11.31 with Filament 3.3
- Blade + Tailwind CSS + Alpine.js
- Vite for asset bundling
- Spatie Laravel Permission + FilamentShield
- barryvdh/laravel-dompdf for PDF invoices
- Dotswan MapPicker (admin) + Laravel Maps Suite with Leaflet (frontend)
- Currency: MVR (Maldivian Rufiyaa)

## Environment

Default `.env.example` uses SQLite. Docker uses MySQL (`horrorbark` / `db_user` / `db_password`).

Seeded admin: `test@admin.com` / `test@admin.com`

## Reference Documentation

See `.claude/skills/horror-bark-resort/references/` for detailed docs:
- `database_schema.md` - Complete database structure
- `booking_workflows.md` - Booking validation logic per type
- `filament_patterns.md` - Common Filament patterns
