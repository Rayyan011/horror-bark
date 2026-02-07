# Horror-Bark Booking Workflows

## Detailed Booking Logic for Each Type

All booking controllers are in `app/Http/Controllers/Bookings/`. Every booking creates an invoice via `InvoiceService`. All bookings default to status `'confirmed'` on creation.

---

## 1. Hotel Booking Workflow

### Controller: `HotelBookingController@store`
**Route:** `POST /bookings/hotels/rooms/{room}`

### Validation
```php
$data = $request->validate([
    'start_date' => ['required', 'date'],
    'end_date'   => ['required', 'date', 'after:start_date'],
    'quantity'   => ['required', 'integer', 'min:1', 'max:' . $room->max_occupancy],
]);
```

### Business Logic

1. Parse dates, calculate nights: `max(1, $startDate->diffInDays($endDate))`
2. Check overlapping bookings (not canceled) for the same room:
   ```php
   $overlappingQuantity = HotelBooking::query()
       ->where('room_id', $room->id)
       ->where('status', '!=', 'canceled')
       ->where(function ($query) use ($startDate, $endDate) {
           $query->where('start_date', '<', $endDate)
                 ->where('end_date', '>', $startDate);
       })
       ->sum('quantity');
   ```
3. Validate: `$overlappingQuantity + $data['quantity'] <= $room->max_occupancy`
4. Calculate price: `$room->price * $data['quantity'] * $nights`
5. Create booking with status `'confirmed'`
6. Create invoice via `$invoiceService->createForBooking()`

### Created Record
```php
HotelBooking::create([
    'user_id'     => $request->user()->id,
    'room_id'     => $room->id,
    'start_date'  => $startDate,
    'end_date'    => $endDate,
    'quantity'    => $data['quantity'],
    'total_price' => $room->price * $data['quantity'] * $nights,
    'status'      => 'confirmed',
]);
```

---

## 2. Ferry Booking Workflow

### Controller: `FerryBookingController@store`
**Route:** `POST /bookings/ferries/{ferry}`

### Validation
```php
$data = $request->validate([
    'booking_time' => ['required', 'date'],
    'quantity'     => ['required', 'integer', 'min:1', 'max:' . $ferry->max_booking_quantity],
]);
```

### Time Slot Rules
- Parse `booking_time`, set seconds to 0
- Extract hour: `(int) $bookingTime->format('G')`
- Validate: minute must be 0, hour must be between 9 and 16 (inclusive)
- **Allowed slots:** 9:00, 10:00, 11:00, 12:00, 13:00, 14:00, 15:00, 16:00

### Capacity Check
```php
$bookedQuantity = FerryBooking::query()
    ->where('ferry_id', $ferry->id)
    ->where('booking_time', $bookingTime)
    ->where('status', '!=', 'canceled')
    ->sum('quantity');

// Fail if: $bookedQuantity + $data['quantity'] > $ferry->max_capacity
```

### Price Calculation
```php
$totalPrice = $ferry->price * $data['quantity'];
```

### Created Record
```php
FerryBooking::create([
    'user_id'      => $request->user()->id,
    'ferry_id'     => $ferry->id,
    'booking_time' => $bookingTime,
    'quantity'     => $data['quantity'],
    'total_price'  => $ferry->price * $data['quantity'],
    'status'       => 'confirmed',
]);
```

---

## 3. Ride Booking Workflow

### Controller: `RideBookingController@store`
**Route:** `POST /bookings/rides/{ride}`

### Validation
```php
$data = $request->validate([
    'booking_time' => ['required', 'date'],
    'quantity'     => ['required', 'integer', 'min:1', 'max:' . $ride->max_booking_quantity],
]);
```

### Time Slot Rules
- Parse `booking_time`, set seconds to 0
- Validate: minute must be 0, hour must be exactly **9 or 17**
- **Only two sessions:** 9:00 AM (morning) and 5:00 PM (evening)

### Capacity Check
```php
$bookedQuantity = RideBooking::query()
    ->where('ride_id', $ride->id)
    ->where('booking_time', $bookingTime)
    ->where('status', '!=', 'canceled')
    ->sum('quantity');

// Fail if: $bookedQuantity + $data['quantity'] > $ride->max_capacity
```

### Price Calculation
```php
$totalPrice = $ride->price * $data['quantity'];
```

### Created Record
```php
RideBooking::create([
    'user_id'      => $request->user()->id,
    'ride_id'      => $ride->id,
    'booking_time' => $bookingTime,
    'quantity'     => $data['quantity'],
    'total_price'  => $ride->price * $data['quantity'],
    'status'       => 'confirmed',
]);
```

---

## 4. Game Booking Workflow

### Controller: `GameBookingController@store`
**Route:** `POST /bookings/games/{game}`

### Validation
```php
$data = $request->validate([
    'booking_time' => ['required', 'date'],
    'quantity'     => ['required', 'integer', 'min:1', 'max:' . $game->max_booking_quantity],
]);
```

### Time Slot Rules
- Identical to rides: minute must be 0, hour must be exactly **9 or 17**
- **Only two sessions:** 9:00 AM and 5:00 PM

### Capacity Check
```php
$bookedQuantity = GameBooking::query()
    ->where('game_id', $game->id)
    ->where('booking_time', $bookingTime)
    ->where('status', '!=', 'canceled')
    ->sum('quantity');

// Fail if: $bookedQuantity + $data['quantity'] > $game->max_capacity
```

### Price Calculation
```php
$totalPrice = $game->price * $data['quantity'];
```

### Created Record
```php
GameBooking::create([
    'user_id'      => $request->user()->id,
    'game_id'      => $game->id,
    'booking_time' => $bookingTime,
    'quantity'     => $data['quantity'],
    'total_price'  => $game->price * $data['quantity'],
    'status'       => 'confirmed',
]);
```

---

## 5. Beach Event Booking Workflow

### Controller: `BeachEventBookingController@store`
**Route:** `POST /bookings/beach-events/{beachEvent}`

### Validation
```php
$data = $request->validate([
    'booking_date' => ['required', 'date'],
    'booking_time' => ['required', 'date_format:H:i'],
    'quantity'     => ['required', 'integer', 'min:1', 'max:' . $beachEvent->max_booking_quantity],
]);
```

### Date Matching Rule
```php
$bookingDate = Carbon::parse($data['booking_date'])->toDateString();
$eventDate = Carbon::parse($beachEvent->event_date)->toDateString();

if ($bookingDate !== $eventDate) {
    return back()->withErrors([
        'booking_date' => 'Booking date must match the event date.',
    ]);
}
```

### Capacity Check
```php
$bookingTime = Carbon::parse($bookingDate . ' ' . $data['booking_time'])->setSecond(0);

$bookedQuantity = BeachEventBooking::query()
    ->where('beach_event_id', $beachEvent->id)
    ->where('booking_date', $bookingDate)
    ->where('booking_time', $bookingTime)
    ->where('status', '!=', 'canceled')
    ->sum('quantity');

// Fail if: $bookedQuantity + $data['quantity'] > $beachEvent->max_capacity
```

### Price Calculation
```php
$totalPrice = $beachEvent->price * $data['quantity'];
```

### Created Record
```php
BeachEventBooking::create([
    'user_id'        => $request->user()->id,
    'beach_event_id' => $beachEvent->id,
    'booking_date'   => $bookingDate,
    'booking_time'   => $bookingTime,
    'quantity'       => $data['quantity'],
    'total_price'    => $beachEvent->price * $data['quantity'],
    'status'         => 'confirmed',
]);
```

---

## Cancellation Logic

### Controller: `CustomerBookingController`

All cancellation methods follow the same pattern. Authorization is checked via policies.

```php
public function cancelHotel(HotelBooking $hotelBooking)
{
    $this->authorize('update', $hotelBooking);

    if ($hotelBooking->status !== 'canceled') {
        $hotelBooking->update(['status' => 'canceled']);
        if ($hotelBooking->invoice) {
            $hotelBooking->invoice->update(['status' => 'canceled']);
        }
    }

    return back()->with('status', 'Hotel booking canceled.');
}
```

**Key points:**
- Only cancels if not already canceled
- Cascades cancellation to the associated invoice
- Each booking type has its own cancel method (cancelHotel, cancelFerry, cancelRide, cancelGame, cancelBeachEvent)
- Authorization uses `update` policy gate

---

## Invoice Creation

### Service: `InvoiceService`

Called by every booking controller after creating a booking:

```php
$invoiceService->createForBooking($booking, $request->user()->id, (float) $booking->total_price);
```

**What it does:**
1. Creates Invoice record with polymorphic relationship to the booking
2. Generates unique invoice number: `INV-YYYYMMDD-XXXXXX`
3. Sets status to `'issued'` and `issued_at` to current time
4. Generates PDF via DomPDF using `invoices.pdf` Blade view
5. Stores PDF in `storage/app/invoices/{invoice_number}.pdf`
6. Updates invoice record with `pdf_path`

---

## Customer Booking Portal

### Controller: `CustomerBookingController@index`
**Route:** `GET /bookings` or `GET /portal`

### Query Filters (from URL query string)
- `type` — filter by booking type: hotel, ferry, ride, game, beach-event
- `status` — filter by status: confirmed, canceled
- `search` — search in entity names (hotel name, ferry name, etc.)
- `from` / `to` — date range filter

### Stats Calculated
```php
$stats = [
    'total'    => /* count of all bookings across all types */,
    'upcoming' => /* count of future bookings */,
    'spent'    => /* sum of total_price across all types */,
];
```

### Date Field Per Type
Each booking type uses a different date field for filtering:
- Hotel: `start_date`
- Ferry/Ride/Game: `booking_time`
- Beach Event: `booking_date`

### Pagination
Each type paginates separately (10 per page) with independent page parameters:
`hotel_page`, `ferry_page`, `ride_page`, `game_page`, `beach_event_page`
