# Requirements Gap Analysis

Analysis of Horror-Bark implementation against UWE assignment requirements.

**Date:** 2026-02-08

---

## Critical Missing Features

### 1. Ferry Booking Requires Valid Hotel Booking

**Priority:** HIGH
**Assignment Requirement:** "Purchase ferry tickets (only if a valid hotel booking exists)"

**Current State:** No validation - users can book ferries without hotel bookings. Homepage shows a notice but it's not enforced.

**Implementation:**
- Add validation in `FerryBookingController@store`
- Check for active hotel booking: `HotelBooking::where('user_id', auth()->id())->where('status', '!=', 'canceled')->exists()`
- Return error if no valid hotel booking exists
- Update frontend to show clear messaging

**Files to modify:**
- `app/Http/Controllers/Bookings/FerryBookingController.php`
- `resources/views/pages/ferrytickets.blade.php`

---

### 2. Admin/Operator Analytics Dashboards

**Priority:** HIGH
**Assignment Requirement:** "View booking reports", "Track ticket sales", "Reports on ticket sales and visitors"

**Current State:** Only customer-level booking dashboard exists. No admin or operator analytics.

**Implementation:**
- Create Filament dashboard widgets for each panel
- Admin dashboard: total bookings, revenue, user counts, booking trends
- Hotel panel: occupancy rates, room booking stats, revenue
- Ferry panel: passenger counts, trip reports, capacity utilization
- Ride/Game panels: booking counts, popular times, capacity usage

**Files to create:**
- `app/Filament/Widgets/StatsOverview.php`
- `app/Filament/Widgets/BookingChart.php`
- `app/Filament/Hotel/Widgets/HotelStats.php`
- `app/Filament/Ferry/Widgets/FerryStats.php`

---

### 3. Promotional Offers Management

**Priority:** MEDIUM
**Assignment Requirement:** "Manage promotional offers for hotel stays", "Manage advertisements and promotional content"

**Current State:** Featured cards on homepage are hardcoded. No admin system to manage promotions.

**Implementation:**
- Create `Promotion` model with fields: title, description, discount_percentage, start_date, end_date, promotable_type, promotable_id (polymorphic)
- Create migration for promotions table
- Create Filament resource for admin to manage promotions
- Update homepage to display active promotions from database
- Allow hotel/ride/ferry operators to create promotions for their entities

**Files to create:**
- `app/Models/Promotion.php`
- `database/migrations/xxxx_create_promotions_table.php`
- `app/Filament/Resources/PromotionResource.php`

**Files to modify:**
- `app/Http/Controllers/HomeController.php`
- `resources/views/pages/home.blade.php`

---

### 4. Ferry Pass/Ticket Generation

**Priority:** MEDIUM
**Assignment Requirement:** "Provide customer with a ferry pass if valid hotel booking exist", "Ferry ticket issuance form"

**Current State:** Only generic invoices are generated. No ferry-specific pass document.

**Implementation:**
- Create ferry pass PDF template with: pass number, passenger name, ferry details, booking time, QR code
- Generate pass on successful ferry booking
- Add pass download link in customer portal
- Store pass_path in FerryBooking model

**Files to create:**
- `resources/views/ferry-passes/pdf.blade.php`
- `app/Services/FerryPassService.php`

**Files to modify:**
- `app/Models/FerryBooking.php` (add pass_path field)
- `database/migrations/xxxx_add_pass_path_to_ferry_bookings.php`
- `app/Http/Controllers/Bookings/FerryBookingController.php`
- `resources/views/pages/bookings/show.blade.php`

---

### 5. Passenger List & Trip Reports

**Priority:** MEDIUM
**Assignment Requirement:** "Passenger list & trip reports" for ferry operators

**Current State:** Ferry operators can see bookings but no aggregated passenger list or trip report view.

**Implementation:**
- Add "Passenger List" action to FerryResource in Ferry panel
- Group bookings by ferry and booking_time
- Show passenger counts per trip
- Add export to PDF/CSV functionality

**Files to create:**
- `app/Filament/Ferry/Pages/PassengerList.php`
- `resources/views/filament/ferry/passenger-list.blade.php`

---

## Partially Implemented Features

### 6. Promotional Banners on Homepage

**Priority:** LOW
**Current State:** Featured cards exist but are randomly selected, not admin-controlled.

**Implementation:** Covered by task #3 (Promotional Offers Management)

---

### 7. Customer Booking Reports Enhancement

**Priority:** LOW
**Current State:** Basic stats shown. Could add more filtering and export options.

**Implementation:**
- Add date range revenue breakdown
- Add booking history export (PDF/CSV)
- Add visual charts for booking trends

---

## Implementation Order

1. **Ferry Booking Validation** - Critical for assignment compliance
2. **Admin Analytics Dashboards** - Required for all operator roles
3. **Promotional Offers System** - Required for marketing features
4. **Ferry Pass Generation** - Required for complete ferry workflow
5. **Passenger Reports** - Required for ferry operators

---

## Verified Implemented Features

- User registration/login
- Hotel booking with date selection
- Room management with availability
- Ferry schedule management
- Theme park rides/games booking
- Beach events booking
- Interactive island map (Leaflet)
- Invoice generation with PDF
- Multi-panel admin system (6 panels)
- Role-based access control
- Booking cancellation
- Customer booking portal
