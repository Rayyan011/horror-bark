# Island Access Rules

This project applies island-aware access requirements for booking flows.

## Rule Matrix

| Booking Type | Island | Hotel Stay Required |
|---|---|---|
| Ferry | Horror-Island destination | Yes |
| Ferry | Picnic-Island destination | No |
| Ride | Horror-Island | Yes |
| Game | Horror-Island | Yes |
| Beach Event | Picnic-Island | No |

## Valid Hotel Stay Definition

A valid stay for Horror-Island access must satisfy all of:
- `status = confirmed`
- `start_date <= booking_datetime`
- `end_date > booking_datetime`

If no valid stay exists for a Horror-Island activity, booking is blocked with:
`A confirmed hotel stay is required to access Horror Island activities.`

## Island Data Assumptions

- `Horror-Island` and `Picnic-Island` are canonical island `type` values.
- Hotels are currently assumed to exist on Horror Island by convention.
- `rides`, `games`, and `beach_events` now support `island_id` for explicit island linkage.
- During transition, `null island_id` fallback behavior is:
  - Rides/Games: treated as Horror-Island.
  - Beach Events: treated as Picnic-Island.
