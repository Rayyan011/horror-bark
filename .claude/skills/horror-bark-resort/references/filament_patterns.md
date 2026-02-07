# Filament Resource Patterns for Horror-Bark

## Common Filament 3 Patterns Used in Multi-Panel System

---

## Panel Configuration

### Panel Providers

All 6 panels are registered in `bootstrap/providers.php` and located in `app/Providers/Filament/`.

| Panel | ID | Path | Color | Resource Namespace |
|-------|----|------|-------|--------------------|
| Admin | admin | /admin | Amber | `App\Filament\Resources` |
| Hotel | hotel | /hotel | Rose | `App\Filament\Hotel\Resources` |
| Ferry | ferry | /ferry | Amber | `App\Filament\Ferry\Resources` |
| Ride | ride | /ride | Amber | `App\Filament\Ride\Resources` |
| Game | game | /game | Blue | `App\Filament\Game\Resources` |
| User | user | /user | Indigo | `App\Filament\User\Resources` |

**Standard panel configuration:**
```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('hotel')
        ->path('hotel')
        ->login()
        ->colors(['primary' => Color::Rose])
        ->discoverResources(
            in: app_path('Filament/Hotel/Resources'),
            for: 'App\\Filament\\Hotel\\Resources'
        )
        ->discoverPages(
            in: app_path('Filament/Hotel/Pages'),
            for: 'App\\Filament\\Hotel\\Pages'
        )
        ->pages([Pages\Dashboard::class])
        ->widgets([
            Widgets\AccountWidget::class,
            Widgets\FilamentInfoWidget::class,
        ])
        ->plugin(FilamentShieldPlugin::make())
        ->middleware([/* standard Laravel middleware */])
        ->authMiddleware([Authenticate::class]);
}
```

Only the Admin panel has `->registration()` enabled.

---

## Admin Panel Resources

### Entity Resources (Hotel, Ferry, Ride, Game, BeachEvent, Island)

These share a common pattern with owner selection and map picker:

```php
public static function form(Form $form): Form
{
    return $form->schema([
        // Owner selection (Ferry, Ride, Game, BeachEvent)
        Forms\Components\Select::make('user_id')
            ->relationship('owner', 'name')
            ->label('Owner')
            ->required(),

        Forms\Components\TextInput::make('name')->required(),
        Forms\Components\TextInput::make('price')->numeric()->required(),
        Forms\Components\TextInput::make('max_capacity')->numeric()->required(),
        Forms\Components\TextInput::make('max_booking_quantity')->numeric()->required(),

        // Map picker (on entities with coordinates)
        Map::make('location_data')
            ->defaultLocation(latitude: 4.227, longitude: 73.427)
            ->draggable()->clickable()->zoom(16)
            ->tilesUrl('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}')
            ->afterStateHydrated(function ($state, $record, $set) {
                if ($record) {
                    $set('location_data', [
                        'lat' => $record->latitude,
                        'lng' => $record->longitude,
                    ]);
                }
            })
            ->afterStateUpdated(function ($state, $set) {
                $set('latitude', $state['lat']);
                $set('longitude', $state['lng']);
            }),

        Forms\Components\TextInput::make('latitude')->numeric()->required(),
        Forms\Components\TextInput::make('longitude')->numeric()->required(),

        // Image upload
        Forms\Components\FileUpload::make('images')
            ->directory('{entity}/gallery')
            ->multiple()
            ->image()
            ->maxFiles(5)
            ->maxSize(1024), // 1MB
    ]);
}
```

### Hotel Resource (Admin)

Hotels don't have an owner — simpler form:
```php
Forms\Components\TextInput::make('name')->required()->maxLength(255),
Forms\Components\TextInput::make('location')->label('Location Name')->maxLength(255),
// Map picker + lat/lng fields
// FileUpload for images
```

### Room Resource

```php
Forms\Components\Select::make('hotel_id')
    ->relationship('hotel', 'name')->required(),
Forms\Components\MarkdownEditor::make('description'),
Forms\Components\FileUpload::make('images')
    ->multiple()->required()->maxFiles(3)
    ->imageEditor()
    ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1'])
    ->reorderable()->appendFiles(),
Forms\Components\TagsInput::make('amenities'),
Forms\Components\TextInput::make('room_number')->required(),
Forms\Components\TextInput::make('price')->numeric()->required(),
Forms\Components\TextInput::make('max_occupancy')->numeric()->required(),
Forms\Components\TextInput::make('status')->default('available')->maxLength(50),
```

### Ferry Resource

Includes island selection:
```php
Forms\Components\Select::make('island_id')
    ->relationship('island', 'name')
    ->label('Home Island')
    ->required(),
// Price prefix: MVR
Forms\Components\TextInput::make('price')->numeric()->required()->prefix('MVR'),
```

### Island Resource

```php
Forms\Components\TextInput::make('name'),
Forms\Components\Select::make('type')
    ->options(['Horror-Island' => 'Horror-Island', 'Picnic-Island' => 'Picnic-Island']),
Forms\Components\MarkdownEditor::make('description'),
// Map picker + lat/lng + images
```

---

## Booking Resources

### Hotel Booking Resource (Admin)

Uses cascading selects: Hotel → Room → Price calculation.

```php
// Hotel select (not persisted, used to filter rooms)
Forms\Components\Select::make('hotel_id')
    ->label('Hotel')
    ->options(Hotel::all()->pluck('name', 'id'))
    ->reactive()
    ->afterStateUpdated(fn ($state, callable $set) => $set('room_id', null))
    ->dehydrated(false)
    ->required(),

// Room select (filtered by hotel_id)
Forms\Components\Select::make('room_id')
    ->label('Room')
    ->options(function (callable $get) {
        $hotelId = $get('hotel_id');
        if (!$hotelId) return [];
        return Room::where('hotel_id', $hotelId)->pluck('room_number', 'id');
    })
    ->searchable()->reactive()
    ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
        // Recalculate: room.price * days * quantity
    )
    ->required(),

Forms\Components\TextInput::make('quantity')
    ->label('Quantity')->numeric()->default(1)->minValue(1)
    ->reactive()->required(),

Forms\Components\DatePicker::make('start_date')->reactive()->required(),
Forms\Components\DatePicker::make('end_date')->reactive()->required(),

// Auto-calculated, disabled
Forms\Components\TextInput::make('total_price')
    ->numeric()->disabled()->reactive()->required(),

Forms\Components\Select::make('status')
    ->options(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'])
    ->default('pending')->required(),
```

**Price recalculation helper (used in afterStateUpdated):**
```php
function recalculate(callable $get, callable $set): void
{
    $room = Room::find($get('room_id'));
    $startDate = $get('start_date');
    $endDate = $get('end_date');
    $quantity = $get('quantity');

    if ($room && $startDate && $endDate && $quantity) {
        $days = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)));
        $set('total_price', $room->price * $days * $quantity);
    }
}
```

### Ferry Booking Resource

```php
Forms\Components\Select::make('user_id')
    ->relationship('user', 'name')->required(),
Forms\Components\Select::make('ferry_id')
    ->relationship('ferry', 'name')->required(),
Forms\Components\DateTimePicker::make('booking_time')
    ->label('Booking Time')->required(),
    // TODO: Add validation for 9am-4pm
Forms\Components\TextInput::make('quantity')
    ->numeric()->required()->minValue(1),
Forms\Components\TextInput::make('total_price')
    ->numeric()->required()->prefix('MVR'),
Forms\Components\Select::make('status')
    ->options(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'])
    ->required(),
```

### Ride/Game Booking Resources

Use reactive price calculation:
```php
Forms\Components\Select::make('ride_id') // or game_id
    ->relationship('ride', 'name') // or game
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set, callable $get) {
        $entity = Ride::find($state); // or Game
        $quantity = $get('quantity');
        if ($entity && $quantity) {
            $set('total_price', $entity->price * $quantity);
        }
    })
    ->required(),

Forms\Components\TextInput::make('quantity')
    ->numeric()->minValue(1)->reactive()
    ->afterStateUpdated(/* same price recalculation */)
    ->required(),

Forms\Components\TextInput::make('total_price')
    ->numeric()->disabled()->dehydrated()->required(),
```

### Beach Event Booking Resource

Validates booking_date matches event_date:
```php
Forms\Components\Select::make('beach_event_id')
    ->relationship('beachEvent', 'name')
    ->reactive()
    ->afterStateUpdated(/* update price */)
    ->required(),

Forms\Components\DatePicker::make('booking_date')
    ->reactive()
    ->afterStateUpdated(function ($state, callable $get) {
        $event = BeachEvent::find($get('beach_event_id'));
        if ($event && $state !== $event->event_date) {
            // Show validation error
        }
    }),
```

---

## Operator Panel Patterns

### Query Scoping

Operator panels restrict data to the authenticated user:
```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->where('user_id', auth()->id());
}
```

### Hidden User ID

Operator forms use a hidden field instead of a select:
```php
Forms\Components\Hidden::make('user_id')
    ->default(fn () => auth()->id())
    ->required(),
```

### Hotel Operator Panel

Hotel operator's `HotelBookingResource` filters hotels to ones owned by the user:
```php
Forms\Components\Select::make('hotel_id')
    ->label('Hotel')
    ->options(Hotel::where('user_id', auth()->id())->pluck('name', 'id'))
    ->reactive()
    ->required(),
```

### Ride Operator Panel

Ride operator's `RideBookingResource` adds time slot validation:
```php
Forms\Components\DateTimePicker::make('booking_time')
    ->label('Booking Time')
    ->rules(['date_format:Y-m-d H:i', 'in:09:00,17:00'])
    ->required(),
```

---

## Table Patterns

### Standard Table Columns

```php
Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
Tables\Columns\TextColumn::make('owner.name')->label('Owner')->sortable(),
Tables\Columns\TextColumn::make('price')->sortable(),
Tables\Columns\TextColumn::make('max_capacity')->sortable(),
Tables\Columns\ImageColumn::make('images')
    ->getStateUsing(fn ($record) => $record->images[0] ?? null)
    ->size(50)->label('Gallery'),
Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
```

### Booking Status Badge

```php
Tables\Columns\BadgeColumn::make('status')
    ->colors([
        'warning' => 'pending',
        'success' => 'confirmed',
        'danger'  => 'cancelled',
    ]),
```

### Ferry Booking Table (with MVR currency)

```php
Tables\Columns\TextColumn::make('total_price')->money('MVR'),
Tables\Columns\TextColumn::make('booking_time')
    ->label('Booking Time')
    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('Y-m-d H:i')),
```

### Status Filter

```php
Tables\Filters\SelectFilter::make('status')
    ->options([
        'pending'   => 'Pending',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
    ]),
```

---

## CMS Page Builder (PageResource)

Uses Filament Builder for dynamic content blocks:

```php
Forms\Components\Builder::make('content')
    ->blocks([
        Forms\Components\Builder\Block::make('heading')->schema([
            Forms\Components\TextInput::make('pos')->label('Heading Position')->required(),
            Forms\Components\TextInput::make('content')->label('Heading')->required(),
        ])->columns(2),

        Forms\Components\Builder\Block::make('text')->schema([
            Forms\Components\TextInput::make('pos')->required(),
            Forms\Components\Textarea::make('content')->required(),
        ]),

        Forms\Components\Builder\Block::make('image')->schema([
            Forms\Components\TextInput::make('pos')->required(),
            Forms\Components\FileUpload::make('content')->required(),
        ]),

        Forms\Components\Builder\Block::make('icon')->schema([
            Forms\Components\TextInput::make('pos')->required(),
            Forms\Components\TextInput::make('content')->required(),
        ]),

        Forms\Components\Builder\Block::make('imageset')->schema([
            Forms\Components\TextInput::make('pos')->required(),
            Forms\Components\FileUpload::make('content')
                ->multiple()->reorderable()
                ->imageEditor()
                ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1'])
                ->required(),
        ]),

        Forms\Components\Builder\Block::make('RichEditor')->schema([
            Forms\Components\TextInput::make('pos')->required(),
            Forms\Components\RichEditor::make('content')
                ->toolbarButtons(['bold', 'bulletList', 'italic', 'link',
                    'orderedList', 'redo', 'underline', 'undo']),
        ]),
    ])->columnSpanFull(),
```

PageResource is in the **Website Configurations** navigation group with sort order 11.

---

## User Resource

```php
Forms\Components\TextInput::make('name')->required()->maxLength(255),
Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
Forms\Components\TextInput::make('password')
    ->password()
    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
    ->dehydrated(fn ($state) => filled($state))
    ->required(fn (string $context) => $context === 'create')
    ->maxLength(255),
Forms\Components\Select::make('roles')
    ->relationship('roles', 'name')
    ->multiple()->preload()->searchable()->required(),
```

### Contact Resource

In the **Customer Service** navigation group:
```php
Forms\Components\TextInput::make('first_name')->required()->maxLength(255),
Forms\Components\TextInput::make('last_name')->required()->maxLength(255),
Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
Forms\Components\TextInput::make('phone_number')->tel()->maxLength(20),
Forms\Components\Textarea::make('message')->required()->maxLength(65535),
```

---

## Navigation Structure

### Admin Panel Groups
- **Customer Service** — ContactResource
- **Website Configurations** — PageResource (sort: 11)
- Default group — all other resources

### Navigation Icons
- UserResource: `heroicon-o-user`
- ContactResource: `heroicon-o-envelope`
- Hotel Operator HotelResource: `heroicon-o-building-office`
- Ride Operator RideResource: `heroicon-o-truck`
- Ride Operator RideBookingResource: `heroicon-o-calendar-days`
- All others: `heroicon-o-rectangle-stack`
