# Improvements Backlog (Proposed)

## Product/UX
- [ ] Add search and filters for catalog pages (hotels, ferries, rides, games).
- [ ] Add booking confirmation emails with calendar attachments.
- [ ] Add payment status and gateway integration (stripe/paypal/etc.).
- [ ] Improve customer portal with booking history and receipts list.
- [ ] Refresh the public site UI (modern typography, updated layouts, mobile-first).
- [ ] Update imagery and visual branding to reduce "dated" feel.

## Admin/Analytics
- [ ] Add Filament stat dashboards (KPIs, revenue, bookings, cancellations).
- [ ] Add trend charts for booking volume by product and date range.

## Reliability/Operations
- [ ] Add CI pipeline for tests and linting (PHPUnit + Pint + JS build).
- [ ] Add structured logging and error reporting (Sentry/Logtail/etc.).
- [ ] Add background jobs for emails/invoices using the DB queue (Redis optional).
- [ ] Add backup/restore docs for database and storage.

## Security
- [ ] Add role-based admin permissions and audit logs.

## Engineering Quality
- [ ] Add feature tests for booking flows and cancellations.
- [ ] Add factories/seeders for realistic demo data.
- [ ] Document API-like endpoints or internal services (if needed).
