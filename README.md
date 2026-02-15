# Horror Bark (Laravel 11 + Filament)

Horror Bark is a booking platform for a theme park experience. The app includes customer-facing booking flows (hotel, ferry, rides, games, beach events) and multiple Filament panels for operations.

## App URLs
- App: `http://127.0.0.1:8000`
- Admin panel: `http://127.0.0.1:8000/admin`

## Local Setup
1. `cd application`
2. `composer install`
3. `npm install`
4. `cp .env.example .env`
5. Configure DB credentials in `.env`
6. `php artisan key:generate`
7. `php artisan migrate:fresh --seed`
8. `php artisan storage:link`
9. `composer dev`

## Docker Setup
1. `docker-compose up -d`
2. `docker-compose exec php bash`
3. `cd /var/www/html`
4. `composer install`
5. `php artisan key:generate`
6. `php artisan migrate:fresh --seed`
7. `php artisan storage:link`
8. `php artisan vendor:publish --tag=maps-views`

If Docker builds fail unexpectedly:
- `docker compose build --no-cache`

## Seeded Admin Credentials
- Email: `test@admin.com`
- Password: `test@admin.com`
- Default seeded role: `super_admin`

## Panel Access (RBAC)
Panel access is role-based and enforced at panel level.

| Panel ID / Path | Allowed Roles |
| --- | --- |
| `admin` (`/admin`) | `admin`, `super_admin` |
| `hotel` (`/hotel`) | `hotel_manager`, `super_admin` |
| `ferry` (`/ferry`) | `ferry_manager`, `super_admin` |
| `ride` (`/ride`) | `ride_manager`, `super_admin` |
| `game` (`/game`) | `game_manager`, `super_admin` |
| `user` (`/user`) | `user`, `super_admin` |

### Supported Roles
- `super_admin`
- `admin`
- `hotel_manager`
- `ferry_manager`
- `ride_manager`
- `game_manager`
- `user`

Notes:
- `super_admin` can access all panels.
- `admin` is restricted to `/admin` only.
- Users are managed as single-role accounts in the admin user form.

## Seed Only Roles + Super Admin Assignment
Run from `application/`:
- `php artisan db:seed --class=RolesAndPanelAccessSeeder`

## Common Commands (run from `application/`)
- Full dev stack: `composer dev`
- Vite only: `npm run dev`
- Build assets: `npm run build`
- Run tests: `php artisan test`
- Format PHP: `php artisan pint`

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
