# Horror-Bark Database Schema

## Complete Database Structure for Theme Park & Resort Booking Platform

---

## Core User Management

### users
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| name | VARCHAR(255) | Required |
| email | VARCHAR(255) | Unique, required |
| email_verified_at | TIMESTAMP | Nullable |
| password | VARCHAR(255) | Hashed |
| remember_token | VARCHAR(100) | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Relationships:**
```php
public function hotelBookings() { return $this->hasMany(HotelBooking::class); }
public function ferryBookings() { return $this->hasMany(FerryBooking::class); }
public function rideBookings() { return $this->hasMany(RideBooking::class); }
public function gameBookings() { return $this->hasMany(GameBooking::class); }
public function beachEventBookings() { return $this->hasMany(BeachEventBooking::class); }
public function invoices() { return $this->hasMany(Invoice::class); }
```

Uses `HasRoles` trait from Spatie Laravel Permission.

---

## Hotel Management

### hotels
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| name | VARCHAR(255) | Required |
| location | VARCHAR(255) | Location name |
| latitude | DECIMAL | Nullable |
| longitude | DECIMAL | Nullable |
| images | JSON | Array of image paths |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Casts:** `images => array`

**Relationships:**
```php
public function rooms() { return $this->hasMany(Room::class); }
```

### rooms
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| hotel_id | BIGINT FK | References hotels(id) |
| room_number | VARCHAR | Required |
| price | DECIMAL(8,2) | Per night price |
| status | VARCHAR | Default: 'available' |
| max_occupancy | INT | Maximum guests allowed |
| amenities | JSON | Nullable, array of amenity strings |
| images | JSON | Array of image paths |
| description | TEXT | Nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Casts:** `amenities => array`, `images => array`

**Relationships:**
```php
public function hotel() { return $this->belongsTo(Hotel::class); }
public function hotelBookings() { return $this->hasMany(HotelBooking::class); }
```

### hotel_bookings
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| user_id | BIGINT FK | References users(id) |
| room_id | BIGINT FK | References rooms(id) |
| start_date | DATE | Check-in date |
| end_date | DATE | Check-out date |
| total_price | DECIMAL | room.price * quantity * nights |
| quantity | INT | Number of guests |
| status | VARCHAR | pending, confirmed, canceled |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Relationships:**
```php
public function user() { return $this->belongsTo(User::class); }
public function room() { return $this->belongsTo(Room::class); }
public function invoice() { return $this->morphOne(Invoice::class, 'invoiceable'); }
```

**Methods:** `isPending(): bool` — returns `$this->status === 'pending'`

---

## Ferry Management

### ferries
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| user_id | BIGINT FK | Owner/operator, references users(id) |
| name | VARCHAR(255) | Required |
| price | DECIMAL | Ticket price per person |
| max_capacity | INT | Total capacity per time slot |
| max_booking_quantity | INT | Max per single booking |
| island_id | BIGINT FK | References islands(id) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Relationships:**
```php
public function owner() { return $this->belongsTo(User::class, 'user_id'); }
public function island() { return $this->belongsTo(Island::class); }
public function bookings() { return $this->hasMany(FerryBooking::class); }
```

### ferry_bookings
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| user_id | BIGINT FK | References users(id) |
| ferry_id | BIGINT FK | References ferries(id) |
| booking_time | DATETIME | Must be on the hour, 9:00-16:00 |
| quantity | INT | Number of passengers |
| total_price | DECIMAL | ferry.price * quantity |
| status | VARCHAR | pending, confirmed, canceled |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Relationships:**
```php
public function user() { return $this->belongsTo(User::class); }
public function ferry() { return $this->belongsTo(Ferry::class); }
public function invoice() { return $this->morphOne(Invoice::class, 'invoiceable'); }
```

**Methods:** `isPending(): bool`

### ferry_slots
Ferry slot management table (for time slot configuration).

---

## Theme Park Rides

### rides
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| user_id | BIGINT FK | Owner/operator, references users(id) |
| name | VARCHAR(255) | Required |
| price | DECIMAL | Ticket price per person |
| latitude | DECIMAL | Nullable |
| longitude | DECIMAL | Nullable |
| images | JSON | Array of image paths |
| max_capacity | INT | Total capacity per time slot |
| max_booking_quantity | INT | Max per single booking |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Casts:** `images => array`

**Relationships:**
```php
public function owner() { return $this->belongsTo(User::class, 'user_id'); }
public function bookings() { return $this->hasMany(RideBooking::class); }
```

### ride_bookings
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| user_id | BIGINT FK | References users(id) |
| ride_id | BIGINT FK | References rides(id) |
| booking_time | DATETIME | Only 9:00 or 17:00 allowed |
| quantity | INT | Number of tickets |
| total_price | DECIMAL | ride.price * quantity |
| status | VARCHAR | pending, confirmed, canceled |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Relationships:**
```php
public function user() { return $this->belongsTo(User::class); }
public function ride() { return $this->belongsTo(Ride::class); }
public function invoice() { return $this->morphOne(Invoice::class, 'invoiceable'); }
```

**Methods:** `isPending(): bool`

---

## Games

### games
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| user_id | BIGINT FK | Owner/operator, references users(id) |
| name | VARCHAR(255) | Required |
| price | DECIMAL | Price per person |
| latitude | DECIMAL | Nullable |
| longitude | DECIMAL | Nullable |
| images | JSON | Array of image paths |
| max_capacity | INT | Total capacity per time slot |
| max_booking_quantity | INT | Max per single booking |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Casts:** `images => array`

**Relationships:**
```php
public function owner() { return $this->belongsTo(User::class, 'user_id'); }
public function bookings() { return $this->hasMany(GameBooking::class); }
```

### game_bookings
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| user_id | BIGINT FK | References users(id) |
| game_id | BIGINT FK | References games(id) |
| booking_time | DATETIME | Only 9:00 or 17:00 allowed |
| quantity | INT | Number of participants |
| total_price | DECIMAL | game.price * quantity |
| status | VARCHAR | pending, confirmed, canceled |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Relationships:**
```php
public function user() { return $this->belongsTo(User::class); }
public function game() { return $this->belongsTo(Game::class); }
public function invoice() { return $this->morphOne(Invoice::class, 'invoiceable'); }
```

**Methods:** `isPending(): bool`

---

## Beach Events

### beach_events
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| user_id | BIGINT FK | Organizer, references users(id) |
| name | VARCHAR(255) | Required |
| event_date | DATE | Date of the event |
| price | DECIMAL | Ticket price per person |
| latitude | DECIMAL | Nullable |
| longitude | DECIMAL | Nullable |
| images | JSON | Array of image paths |
| max_capacity | INT | Total event capacity |
| max_booking_quantity | INT | Max per single booking |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Casts:** `images => array`

**Relationships:**
```php
public function owner() { return $this->belongsTo(User::class, 'user_id'); }
public function bookings() { return $this->hasMany(BeachEventBooking::class); }
```

### beach_event_bookings
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| user_id | BIGINT FK | References users(id) |
| beach_event_id | BIGINT FK | References beach_events(id) |
| booking_date | DATE | Must match event's event_date |
| booking_time | DATETIME | Combined date + time |
| quantity | INT | Number of attendees |
| total_price | DECIMAL | beachEvent.price * quantity |
| status | VARCHAR | pending, confirmed, canceled |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Relationships:**
```php
public function user() { return $this->belongsTo(User::class); }
public function beachEvent() { return $this->belongsTo(BeachEvent::class); }
public function invoice() { return $this->morphOne(Invoice::class, 'invoiceable'); }
```

---

## Invoices

### invoices
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| invoice_number | VARCHAR | Unique, format: INV-YYYYMMDD-XXXXXX |
| invoiceable_type | VARCHAR | Polymorphic model class name |
| invoiceable_id | BIGINT | Polymorphic model ID |
| user_id | BIGINT FK | References users(id) |
| amount | DECIMAL | Booking total price |
| status | VARCHAR | 'issued' on creation, 'canceled' on cancel |
| issued_at | DATETIME | Timestamp of issuance |
| pdf_path | VARCHAR | Path to generated PDF file |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Casts:** `issued_at => datetime`

**Relationships:**
```php
public function invoiceable() { return $this->morphTo(); }
public function user() { return $this->belongsTo(User::class); }
```

---

## Content Management

### islands
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| name | VARCHAR(255) | Required |
| type | VARCHAR | 'Horror-Island' or 'Picnic-Island' |
| description | TEXT | Nullable |
| latitude | DECIMAL | Nullable |
| longitude | DECIMAL | Nullable |
| images | JSON | Array of image paths |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Casts:** `images => array`

### pages
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| page_name | VARCHAR(255) | URL slug / identifier |
| content | JSON | Array of content blocks (heading, text, image, icon, imageset, RichEditor) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Casts:** `content => array`

Each content block has `pos` (position) and `content` fields, with block types: heading, text, image, icon, imageset, RichEditor.

### contacts
| Column | Type | Notes |
|--------|------|-------|
| id | BIGINT PK | Auto increment |
| first_name | VARCHAR(255) | Required |
| last_name | VARCHAR(255) | Required |
| email | VARCHAR(255) | Required |
| phone_number | VARCHAR(20) | Nullable |
| message | TEXT | Required |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

## Permissions (Spatie Laravel Permission)

Standard Spatie tables: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`.

All Filament panels use `FilamentShieldPlugin` for permission management.

---

## Common Query Patterns

### Check Hotel Room Availability (Overlap Detection)
```php
$overlappingQuantity = HotelBooking::query()
    ->where('room_id', $room->id)
    ->where('status', '!=', 'canceled')
    ->where(function ($query) use ($startDate, $endDate) {
        $query->where('start_date', '<', $endDate)
              ->where('end_date', '>', $startDate);
    })
    ->sum('quantity');

$available = $overlappingQuantity + $requestedQuantity <= $room->max_occupancy;
```

### Check Time Slot Capacity (Ferry/Ride/Game)
```php
$bookedQuantity = FerryBooking::query()
    ->where('ferry_id', $ferry->id)
    ->where('booking_time', $bookingTime)
    ->where('status', '!=', 'canceled')
    ->sum('quantity');

$available = $bookedQuantity + $requestedQuantity <= $ferry->max_capacity;
```

### Generate Invoice Number
```php
'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
// Example: INV-20260207-A3KF9X
```

### Count Upcoming Bookings
```php
$user->hotelBookings()->where('start_date', '>=', now())->count()
+ $user->ferryBookings()->where('booking_time', '>=', now())->count()
+ $user->rideBookings()->where('booking_time', '>=', now())->count()
+ $user->gameBookings()->where('booking_time', '>=', now())->count()
+ $user->beachEventBookings()->where('booking_date', '>=', now()->toDateString())->count();
```
