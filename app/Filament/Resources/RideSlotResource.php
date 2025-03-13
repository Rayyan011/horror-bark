<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RideSlotResource\Pages;
use App\Filament\Resources\RideSlotResource\RelationManagers;
use App\Models\RideSlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;

class RideSlotResource extends Resource
{
    /**
     * Tells Filament which Eloquent model this resource manages.
     */
    protected static ?string $model = RideSlot::class;

    /**
     * The icon used in the Filament sidebar navigation.
     */
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Defines the form schema for creating/editing a single RideSlot record.
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('ride_id')
                ->relationship('ride', 'name')
                ->label('Ride')
                ->required(),

            DatePicker::make('slot_date')
                ->label('Date')
                ->required(),

            TimePicker::make('start_time')
                ->label('Start Time')
                ->required(),

            TimePicker::make('end_time')
                ->label('End Time')
                ->required(),

            TextInput::make('capacity')
                ->label('Capacity')
                ->numeric()
                ->default(0)
                ->required(),

            TextInput::make('status')
                ->label('Status')
                ->default('open'),
        ]);
    }

    /**
     * Defines how Filament displays RideSlot records in a table, including columns and actions.
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Basic columns to display each slot’s data
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('ride.name')
                    ->label('Ride')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slot_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('End')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('capacity'),
                Tables\Columns\TextColumn::make('status'),
            ])
            // Optional row-level actions for each existing slot
            ->actions([
                // e.g. row-level "Duplicate" or "Close" actions could go here
            ])
            // Optional bulk actions for multiple selected rows
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            // Header actions appear above the table
            ->headerActions([
                // The default Filament "Create" button for single-slot creation
                Tables\Actions\CreateAction::make(),

                // A custom action to auto-generate 1-hour slots in bulk
                Action::make('generateSlots')
                    ->label('Generate Slots')
                    ->icon('heroicon-o-plus')
                    ->form([
                        // Let user pick which Ride
                        Select::make('ride_id')
                            ->label('Ride')
                            ->relationship('ride', 'name')
                            ->searchable()
                            ->required(),

                        // Let them choose a start/end date
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required(),

                        // Let them pick daily times (default 9:00–17:00)
                        TimePicker::make('start_time')
                            ->label('Daily Start')
                            ->default('09:00')
                            ->required(),
                        TimePicker::make('end_time')
                            ->label('Daily End')
                            ->default('17:00')
                            ->required(),

                        // Capacity for each slot
                        TextInput::make('capacity')
                            ->label('Capacity')
                            ->numeric()
                            ->default(20)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // 1) Extract form data
                        $rideId    = $data['ride_id'];
                        $startDate = $data['start_date']; // Carbon instance from DatePicker
                        $endDate   = $data['end_date'];   // Carbon instance
                        $startTime = $data['start_time']; // Carbon from TimePicker
                        $endTime   = $data['end_time'];   // Carbon
                        $capacity  = (int)$data['capacity'];

                        // We fix the interval at exactly 1 hour
                        $intervalHours = 1;

                        // 2) Loop each day from startDate to endDate
                        $period = CarbonPeriod::create($startDate, $endDate);
                        $slotCount = 0;

                        foreach ($period as $date) {
                            $dayString = $date->format('Y-m-d'); // e.g., "2025-03-15"

                            // Build daily start/end times
                            $currentTime = $date->copy()->setTimeFrom($startTime);
                            $endOfDay    = $date->copy()->setTimeFrom($endTime);

                            // 3) Subdivide each day into 1-hour blocks
                            while ($currentTime->lessThan($endOfDay)) {
                                $slotStart = $currentTime->copy();
                                $slotEnd   = $currentTime->copy()->addHours($intervalHours);

                                // Create a new row
                                RideSlot::create([
                                    'ride_id'   => $rideId,
                                    'slot_date' => $dayString,
                                    'start_time' => $slotStart->format('H:i'),
                                    'end_time'   => $slotEnd->format('H:i'),
                                    'capacity'   => $capacity,
                                    'status'     => 'open',
                                ]);

                                $slotCount++;
                                $currentTime->addHours($intervalHours);
                            }
                        }

                       
                    })
                    ->requiresConfirmation(), // optional: "Are you sure?" dialog
            ]);
    }

    /**
     * Standard Filament resource pages for listing, creating, editing ride slots.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRideSlots::route('/'),
            // 'create' => Pages\CreateRideSlot::route('/create'),
            'edit' => Pages\EditRideSlot::route('/{record}/edit'),
        ];
    }
}