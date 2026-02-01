# Architecture

## Overview
This is a Laravel 11 monolith for a theme park booking site with a Filament
admin panel. The customer-facing site supports browsing and booking hotels,
ferry tickets, rides, games, and beach events, plus a contact form, CMS pages,
and a customer booking portal. Admins manage catalog data and bookings via
Filament resources.

## High-Level Diagram
```
           +-----------------------+
           |       Browser         |
           +-----------+-----------+
                       |
                       v
           +-----------------------+
           |     Laravel App       |
           |  routes + middleware  |
           +-----------+-----------+
                       |
        +--------------+-------------------+
        |                                  |
        v                                  v
+-------------------+            +-----------------------+
| MVC Controllers   |            |  Filament Admin       |
| (web + bookings)  |            |  /admin resources     |
+---------+---------+            +-----------+-----------+
          |                                  |
          v                                  v
+-------------------+            +-----------------------+
| Blade Views +     |            |  Eloquent Models      |
| Vite Assets        |            |  + Policies           |
+---------+---------+            +-----------+-----------+
          |                                  |
          +---------------+------------------+
                          v
                  +---------------+
                  |   Database    |
                  +---------------+
                          |
                          v
                  +---------------+
                  |  Storage/FS   |
                  +---------------+
```

## Booking Flow (Customer)
```
[Browse] -> [Select item] -> [Create Booking] -> [Invoice] -> [Portal]
   |            |                  |               |          |
   |            |                  |               |          +-- View/cancel booking
   |            |                  |               +------------- Download invoice
   |            |                  +----------------------------- Booking record
   +------------+----------------------------------------------- Public catalog
```

## Key Modules
- Web routes: `application/routes/web.php`
- Controllers: `application/app/Http/Controllers/`
- Booking controllers: `application/app/Http/Controllers/Bookings/`
- Models: `application/app/Models/`
- Filament resources: `application/app/Filament/Resources/`
- Views/assets: `application/resources/`

## Core Domain Entities
- Hotels, Rooms, HotelBookings
- Ferries, FerrySlots, FerryBookings
- Rides, RideBookings
- Games, GameBookings
- BeachEvents, BeachEventBookings
- Islands
- Pages (CMS)
- Contacts (contact form)
- Invoices
- Users

## Admin (Filament)
Admins use Filament resources to manage:
- Catalog data (hotels, rooms, ferries, slots, rides, games, events, islands)
- CMS pages
- Users
- Contacts
- Bookings and invoices

## Data & State
```
Catalog (hotels/ferries/rides/games/events)
        |
        v
Bookings (per user)
        |
        v
Invoices + Downloads
```

## Runtime/Deployment
- Local dev: `composer dev` in `application/`
- Docker stack: `docker-compose.yml` with PHP-FPM + Nginx + MySQL
- Public files via `storage:link`
