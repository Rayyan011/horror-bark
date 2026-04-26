# Island Access Rules

This project applies island-aware access requirements for booking flows.

## Rule Matrix

| Booking Type | Island | Hotel Stay Required |
|---|---|---|
| Ferry | Horror-Island destination | Yes |
| Ferry | Picnic-Island destination | No |
| Ride | Any island | Yes |
| Game | Any island | Yes |
| Beach Event | Any island | Yes |

## Valid Hotel Stay Definition

A valid stay for hotel-gated bookings must satisfy all of:
- `status = confirmed`
- `start_date <= booking_datetime`
- `end_date > booking_datetime`

If no valid stay exists for a hotel-gated booking, booking is blocked with:
`A confirmed hotel stay is required before booking this activity.`

## Island Data Assumptions

- `Horror-Island` and `Picnic-Island` are canonical island `type` values.
- Hotels are currently assumed to exist on Horror Island by convention.
- `rides`, `games`, and `beach_events` now support `island_id` for explicit island linkage.
- During transition, `null island_id` fallback behavior is:
  - Rides/Games: treated as Horror-Island.
  - Beach Events: treated as Picnic-Island.
- Picnic-Island ferry bookings remain bookable without a hotel stay; rides, games, and beach events do not.
