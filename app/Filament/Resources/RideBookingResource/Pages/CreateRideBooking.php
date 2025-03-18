<?php

namespace App\Filament\Resources\RideBookingResource\Pages;

use App\Filament\Resources\RideBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\HotelBooking;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class CreateRideBooking extends CreateRecord
{
    protected static string $resource = RideBookingResource::class;

    
}
