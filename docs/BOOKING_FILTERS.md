# Booking and Catalog Filters

This document describes customer-facing URL query filters implemented across catalog and portal pages.

## Catalog Pages

### Hotels (`/hotels`)
- `search`: Hotel name or location text.
- `min_price`: Minimum room price.
- `max_price`: Maximum room price.
- `min_occupancy`: Minimum room occupancy.
- `sort`: `name_asc`, `name_desc`, `price_asc`, `price_desc`.

Example:
`/hotels?search=harbor&min_price=100&max_price=300&min_occupancy=2&sort=price_asc`

### Ferries (`/ferrytickets`)
- `search`: Ferry name.
- `island_type`: `Horror-Island` or `Picnic-Island`.
- `island_id`: Destination island ID.
- `min_price`: Minimum ticket price.
- `max_price`: Maximum ticket price.
- `min_capacity`: Minimum ferry capacity.
- `sort`: `name_asc`, `name_desc`, `price_asc`, `price_desc`.

Example:
`/ferrytickets?island_type=Picnic-Island&island_id=1&max_price=80&sort=name_asc`

### Theme Park (`/themepark`)
- `section`: `all`, `rides`, `games`.
- `search`: Ride/game name.
- `island_type`: `Horror-Island` or `Picnic-Island`.
- `min_price`: Minimum price.
- `max_price`: Maximum price.
- `min_capacity`: Minimum capacity.
- `sort`: `name_asc`, `name_desc`, `price_asc`, `price_desc`.

Example:
`/themepark?section=rides&island_type=Horror-Island&min_capacity=10&sort=price_desc`

### Beach Events (`/beach-events`)
- `search`: Event name or organizer name.
- `island_type`: `Horror-Island` or `Picnic-Island`.
- `date_from`: Start date (inclusive).
- `date_to`: End date (inclusive).
- `min_price`: Minimum ticket price.
- `max_price`: Maximum ticket price.
- `sort`: `date_asc`, `date_desc`, `price_asc`, `price_desc`, `name_asc`, `name_desc`.

Example:
`/beach-events?island_type=Picnic-Island&date_from=2026-03-01&date_to=2026-03-31&sort=date_asc`

## Portal Page

### Booking History (`/bookings` or `/portal`)
- `type`: `hotel`, `ferry`, `ride`, `game`, `beach-event`.
- `search`: Entity name search.
- `from`: Start date filter.
- `to`: End date filter.

Example:
`/bookings?type=ferry&from=2026-03-01&to=2026-03-31`

### Receipts (same page)
- `receipt_search`: Invoice number search.
- `receipt_status`: `issued`, `canceled`.
- `receipt_page`: Receipt pagination page.

Example:
`/bookings?receipt_search=INV-202603&receipt_status=issued`

## Persistence Notes
- All filter forms use GET.
- Catalog and receipt paginators preserve query strings for shareable URLs.
- Reset actions clear only the relevant filter set.
