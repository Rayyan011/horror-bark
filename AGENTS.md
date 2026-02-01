# AGENTS

This repo contains a Laravel 11 app with Filament admin panels. The main
application lives in `application/` and is served either locally or via Docker.
It powers a Horror Bark theme park booking site with customer auth, a booking
portal, and invoices. Core user-facing features include hotels/rooms, ferry
tickets, theme park rides, games, beach events, a contact form, and CMS pages.
Admins manage hotels, rooms, ferries (and slots), rides, games, beach events,
islands, pages, users, contacts, bookings, and invoices via Filament.

## Quick Start (Local)
- `cd application`
- `composer install`
- `npm install`
- `cp .env.example .env` and set DB credentials
- `php artisan key:generate`
- `php artisan migrate:fresh --seed`
- `php artisan storage:link`
- `composer dev` (runs app server, queue, logs, and Vite)

App: `http://127.0.0.1:8000`  
Admin: `http://127.0.0.1:8000/admin`

## Quick Start (Docker)
- `docker-compose up -d`
- `docker-compose exec php bash`
- `cd /var/www/html`
- `composer install`
- `php artisan key:generate`
- `php artisan migrate:fresh --seed`
- `php artisan storage:link`
- `php artisan vendor:publish --tag=maps-views`

App: `http://localhost:8080`  
Admin: `http://localhost:8080/admin`

If Docker builds act up: `docker compose build --no-cache`.

## Admin Credentials (Seeded)
- Email: `test@admin.com`
- Password: `test@admin.com`

## Common Commands (run in `application/`)
- Dev server + queue + logs + Vite: `composer dev`
- Vite only: `npm run dev`
- Build assets: `npm run build`
- Run tests: `php artisan test` or `vendor/bin/phpunit`
- Format PHP: `php artisan pint` or `./vendor/bin/pint`

## Key Paths
- App code: `application/app/`
- Routes: `application/routes/`
- Views/assets: `application/resources/`
- Migrations/seeders: `application/database/`
- Tests: `application/tests/`
- Public assets: `application/public/`
- Nginx config: `nginx/`
- PHP-FPM config: `php/`
- Docker compose: `docker-compose.yml`

## Environment Notes
- Default `.env.example` uses SQLite. Switch to MySQL for local or Docker.
- Docker MySQL defaults (from `docker-compose.yml`):
  - DB: `horrorbark`
  - User: `db_user`
  - Password: `db_password`
  - Root password: `root`
- Do not commit `.env`.

## Panels & Features
Filament panels are used for admin domains (hotel, ferry, game, ride, etc.).
See `application/app/` and `application/bootstrap/providers.php` for registration.
