<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    // Optional: Customize the page title in the browser tab
    public function getTitle(): string
    {
        return 'View Contact Inquiry'; // Or your custom title
    }

    // Optional: Customize the record title displayed on the page header
    public function getRecordTitle(): Htmlable|string
    {
    return $this->record->first_name . ' ' . $this->record->last_name; // Example: Display name
    }
    // Optional: If you want to customize the view form layout, you can add a `form()` method here
    // public function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             // Define the fields to display and their layout here if needed
    //             // By default, it will display all model attributes
    //         ]);
    // }
}