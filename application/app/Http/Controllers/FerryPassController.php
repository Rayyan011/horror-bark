<?php

namespace App\Http\Controllers;

use App\Models\FerryBooking;
use App\Services\FerryPassService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FerryPassController extends Controller
{
    public function download(Request $request, FerryBooking $ferryBooking, FerryPassService $ferryPassService)
    {
        $this->authorize('view', $ferryBooking);
        abort_if($ferryBooking->status === 'canceled', 404);

        $path = $ferryBooking->pass_path;

        if (! $path || ! Storage::disk('local')->exists($path)) {
            $path = $ferryPassService->generatePdf($ferryBooking);
        }

        return Storage::disk('local')->download($path);
    }
}
