<?php

namespace App\Services;

use App\Models\FerryBooking;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FerryPassService
{
    public function createForBooking(FerryBooking $booking): FerryBooking
    {
        if (! $booking->pass_number) {
            $booking->update([
                'pass_number' => $this->generatePassNumber(),
            ]);
        }

        $this->generatePdf($booking);

        return $booking->fresh(['ferry.island', 'user']);
    }

    public function generatePdf(FerryBooking $booking): string
    {
        if (! $booking->pass_number) {
            $booking->update([
                'pass_number' => $this->generatePassNumber(),
            ]);
        }

        Storage::disk('local')->makeDirectory('ferry-passes');

        $booking->loadMissing('ferry.island', 'user');

        $pdf = app('dompdf.wrapper')->loadView('ferry-passes.pdf', [
            'booking' => $booking,
        ]);

        $fileName = 'ferry-passes/'.$booking->pass_number.'.pdf';
        Storage::disk('local')->put($fileName, $pdf->output());

        $booking->update([
            'pass_path' => $fileName,
        ]);

        return $fileName;
    }

    private function generatePassNumber(): string
    {
        return 'PASS-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
    }
}
