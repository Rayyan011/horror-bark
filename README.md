# Horror Bark

Horror Bark is a Laravel 11 + Filament booking platform for a horror-themed island resort and theme park. The public app supports hotel stays, ferry tickets, rides, games, beach events, invoices, ferry passes, CMS pages, promotions, and a customer booking portal. Filament panels handle admin and operator workflows for hotels, ferries, rides, games, and end users.

## Current State
- Mature academic/demo MVP rather than a starter project.
- Core customer booking flows are implemented and covered by feature tests.
- Operator/admin dashboards exist across panels.
- Island-aware booking rules are enforced in code.
- Promotions, ferry passes, and ferry passenger reports are now implemented.
- CI is configured to run PHP tests, Pint, and the frontend production build.

## App URLs
- App: `http://127.0.0.1:8000`
- Admin: `http://127.0.0.1:8000/admin`
- Hotel panel: `http://127.0.0.1:8000/hotel`
- Ferry panel: `http://127.0.0.1:8000/ferry`
- Ride panel: `http://127.0.0.1:8000/ride`
- Game panel: `http://127.0.0.1:8000/game`
- User panel: `http://127.0.0.1:8000/user`

## Local Setup
Run from `application/`:

1. `composer install`
2. `npm install`
3. `cp .env.example .env`
4. `php artisan key:generate`
5. `php artisan migrate:fresh --seed`
6. `php artisan storage:link`
7. `composer dev`

## Docker Setup
1. `docker-compose up -d`
2. `docker-compose exec php bash`
3. `cd /var/www/html`
4. `composer install`
5. `php artisan key:generate`
6. `php artisan migrate:fresh --seed`
7. `php artisan storage:link`
8. `php artisan vendor:publish --tag=maps-views`

## Seeded Admin
- Email: `test@admin.com`
- Password: `test@admin.com`
- Role: `super_admin`

## Implemented Highlights
- Public catalogs for hotels, ferries, rides, games, and beach events
- Customer registration/login and booking portal
- Invoice PDF generation and download
- Ferry pass generation and download
- Island-aware access rules for Horror Island vs Picnic Island
- Promotions managed via Filament and rendered on the homepage
- Ferry passenger/trip reporting with CSV export
- Multi-panel Filament dashboards and role-based panel access

## Tests and Checks
Run from `application/`:
- `php artisan test`
- `./vendor/bin/pint --test`
- `npm run build`

## Key Paths
- App code: `application/app/`
- Routes: `application/routes/`
- Views/assets: `application/resources/`
- Migrations/seeders: `application/database/`
- Tests: `application/tests/`
- Docker config: `docker-compose.yml`, `nginx/`, `php/`

## Notes
- `uwe-course.md` is a UWE module specification, not the authoritative feature/status document for this repo.
- See `docs/` for the current architecture, developer notes, and backlog.
