# Architecture

## Overview
Horror Bark is a Laravel 11 monolith with Blade/Vite on the public site and Filament v3 for admin/operator panels. The same codebase serves customer browsing/booking, operator workflows, PDF document generation, and marketing/content management.

## High-Level Diagram
```
           +-----------------------+
           |       Browser         |
           +-----------+-----------+
                       |
                       v
           +-----------------------+
           |     Laravel App       |
           | routes + middleware   |
           +-----------+-----------+
                       |
        +--------------+------------------------------+
        |                                             |
        v                                             v
+------------------------+                +---------------------------+
| Public MVC Controllers |                | Filament Panels           |
| catalogs + bookings    |                | admin + operator portals  |
+-----------+------------+                +-------------+-------------+
            |                                             |
            v                                             v
+------------------------+                +---------------------------+
| Blade Views + Vite     |                | Eloquent Models + Queries |
| homepage + portal      |                | scoped by role/ownership  |
+-----------+------------+                +-------------+-------------+
            +-----------------------------+-------------+
                                          v
                                  +---------------+
                                  |   Database    |
                                  +-------+-------+
                                          |
                                          v
                                  +---------------+
                                  | Storage / PDF |
                                  +---------------+
```

## Core Domains
- Hotels, Rooms, HotelBookings
- Ferries, FerrySlots, FerryBookings, Ferry Passes
- Rides, RideBookings
- Games, GameBookings
- BeachEvents, BeachEventBookings
- Invoices
- Promotions
- Islands
- Pages
- Contacts
- Users and role-based panel access

## Public Flow
```
[Browse] -> [Select item] -> [Create booking] -> [Invoice / Ferry Pass] -> [Portal]
     |             |                |                    |                |
     |             |                |                    |                +-- View / cancel
     |             |                |                    +------------------- Download documents
     |             |                +---------------------------------------- Persist booking
     +-------------+--------------------------------------------------------- Catalog + marketing
```

## Panels
- `admin`: full management, dashboards, promotions, content, users, bookings
- `hotel`: hotel inventory and hotel booking operations
- `ferry`: ferry inventory, ferry booking operations, passenger reports
- `ride`: ride inventory and ride booking operations
- `game`: game inventory and game booking operations
- `user`: customer-facing booking widgets and summaries

## Important Runtime Rules
- Ferry bookings require a valid overlapping hotel stay only when the destination is Horror Island.
- Ride, game, and beach event bookings require a valid overlapping hotel stay before purchase.
- Ferry operators, game operators, ride operators, and hotel operators are scoped to their own records in their panels.

## Documents and Reporting
- Invoices are generated as PDFs for all booking types.
- Ferry bookings generate a separate ferry pass PDF.
- Ferry operators can view passenger manifests and export CSV trip reports.

## Deployment
- Local dev via `composer dev`
- Docker via `docker-compose.yml`
- CI via GitHub Actions for tests, Pint, and frontend build
