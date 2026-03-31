# CLAUDE.md

Guidance for code agents working in this repository.

## Project Overview
Horror Bark is a Laravel 11 resort/theme park booking platform with Filament 3 panels. The main application lives in `application/`. Public flows cover hotels, ferries, rides, games, beach events, invoices, ferry passes, promotions, and the customer portal.

## Commands
Run from `application/`:

```bash
composer dev
php artisan test
./vendor/bin/pint --test
npm run build
php artisan migrate:fresh --seed
```

## Architecture Notes
- Multi-panel Filament setup: `admin`, `hotel`, `ferry`, `ride`, `game`, `user`
- Operator resources are ownership-scoped inside their respective panels
- Ferry bookings generate invoices and ferry passes
- Promotions are admin-managed and surfaced on the homepage
- Ferry passenger reports are available at `/ferry/passenger-reports`
- Island-aware validation is centralized in `App\Services\IslandAccessService`

## Tech Stack
- Laravel 11.31
- Filament 3.3
- Blade + Tailwind + Alpine + Vite
- Spatie Permission + Filament Shield
- DomPDF
- Leaflet / map integrations for island and attraction mapping

## Key Paths
- `application/app/Filament/`
- `application/app/Http/Controllers/`
- `application/app/Services/`
- `application/app/Models/`
- `application/resources/views/`
- `application/database/migrations/`
- `application/tests/`

## Seeded Admin
- Email: `test@admin.com`
- Password: `test@admin.com`

## Status Note
- `uwe-course.md` is contextual background only.
- For repo status, prefer the main README and docs in `docs/`.
