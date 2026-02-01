# Developer Guide

## Tech Stack
- Laravel 11 (PHP 8.2+)
- Filament v3 (admin panels)
- Blade + Vite (frontend assets)
- MySQL (Docker default) or SQLite (local default in `.env.example`)
- Nginx + PHP-FPM (Docker)

## Local Setup (Non-Docker)
Run from `application/`:
```
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
composer dev
```
App: `http://127.0.0.1:8000`  
Admin: `http://127.0.0.1:8000/admin`

Admin login (seeded):
- `test@admin.com` / `test@admin.com`

## Docker Setup
```
docker-compose up -d
docker-compose exec php bash
cd /var/www/html
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan vendor:publish --tag=maps-views
```
App: `http://localhost:8080`  
Admin: `http://localhost:8080/admin`

## Key Workflows
- Dev server + queue + logs + Vite: `composer dev`
- Vite only: `npm run dev`
- Build assets: `npm run build`
- Tests: `php artisan test` or `vendor/bin/phpunit`
- Format PHP: `php artisan pint`

## Queue (DB Driver)
Use the database queue to avoid extra infrastructure.
1) Create the jobs table:
```
php artisan queue:table
php artisan migrate
```
2) Set `.env`:
```
QUEUE_CONNECTION=database
```
3) Run a worker:
```
php artisan queue:work
```

## App Map
- Routes: `application/routes/web.php`
- Controllers: `application/app/Http/Controllers/`
- Booking controllers: `application/app/Http/Controllers/Bookings/`
- Models: `application/app/Models/`
- Filament resources: `application/app/Filament/Resources/`
- Views/assets: `application/resources/`
- Migrations/seeders: `application/database/`

## Core Features
- Public catalog pages: hotels, ferries, theme park rides, games, beach events
- Customer auth + booking portal
- Booking creation and cancellations
- Invoices (view + download)
- Contact form
- CMS pages
- Filament admin resources for all core domains

## Data Model (High Level)
- `Hotel` -> `Room` -> `HotelBooking`
- `Ferry` -> `FerryBooking` (with slots)
- `Ride` -> `RideBooking`
- `Game` -> `GameBooking`
- `BeachEvent` -> `BeachEventBooking`
- `Invoice` tied to bookings
- `Page` for CMS content
- `Contact` for contact form submissions

## Admin (Filament)
Admins manage catalog data, bookings, invoices, pages, users, and contacts.
See `application/app/Filament/Resources/` for per-domain configuration.

## Notes
- `composer dev` starts a queue listener, but the app currently has few or no
  custom queued jobs defined. If adding mail/async processing, consider jobs.
- `.env.example` defaults to SQLite; switch to MySQL for parity with Docker.
