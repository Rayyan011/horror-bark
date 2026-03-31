# Developer Guide

## Stack
- Laravel 11 / PHP 8.2+
- Filament v3
- Blade + Tailwind + Alpine + Vite
- SQLite by default locally, MySQL in Docker
- DomPDF for invoices and ferry passes
- Spatie Permission + Filament Shield

## Local Setup
Run from `application/`:
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
composer dev
```

## Docker Setup
```bash
docker-compose up -d
docker-compose exec php bash
cd /var/www/html
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan vendor:publish --tag=maps-views
```

## Seeded Admin
- `test@admin.com`
- password `test@admin.com`

## Common Commands
- `composer dev`
- `php artisan test`
- `./vendor/bin/pint --test`
- `npm run build`

## App Map
- Routes: `application/routes/web.php`
- Controllers: `application/app/Http/Controllers/`
- Booking controllers: `application/app/Http/Controllers/Bookings/`
- Services: `application/app/Services/`
- Filament resources/panels: `application/app/Filament/`
- Models: `application/app/Models/`
- Views/assets: `application/resources/`
- Tests: `application/tests/`

## Current Implementation Notes
- Promotions are admin-managed through `PromotionResource` and rendered on the public homepage.
- Ferry bookings issue both an invoice and a ferry pass.
- Ferry passenger reports live on `/ferry/passenger-reports` and support CSV export.
- Island-aware booking enforcement is implemented in `IslandAccessService`.
- Operator resources are scoped by ownership rather than showing global inventory.

## Verification Notes
- The repo now includes a GitHub Actions workflow at `.github/workflows/ci.yml`.
- Local verification still requires installing Composer and npm dependencies because vendor/node modules are not committed.
