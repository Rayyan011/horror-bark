<?php

namespace App\Http\Controllers;

use App\Models\Ferry;
use App\Models\FerryBooking;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FerryOperatorReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeOperator($request);

        $filters = $this->validatedFilters($request);
        $ferries = $this->accessibleFerries($request)->get(['id', 'name', 'island_id']);

        if (! empty($filters['ferry_id']) && ! $ferries->contains('id', (int) $filters['ferry_id'])) {
            abort(403);
        }

        $manifest = $this->buildManifestQuery($request, $filters)
            ->orderBy('booking_time')
            ->get();

        $tripSummary = $manifest
            ->groupBy(fn (FerryBooking $booking) => $booking->ferry_id.'|'.$booking->booking_time->format('Y-m-d H:i:s'))
            ->map(function ($group) {
                /** @var FerryBooking $first */
                $first = $group->first();

                return [
                    'ferry_name' => $first->ferry->name,
                    'departure_time' => $first->booking_time,
                    'bookings_count' => $group->count(),
                    'passenger_count' => $group->sum('quantity'),
                    'revenue' => $group->sum('total_price'),
                ];
            })
            ->sortBy('departure_time')
            ->values();

        return view('pages.ferry-reports.index', [
            'filters' => $filters,
            'ferries' => $ferries,
            'tripSummary' => $tripSummary,
            'manifest' => $manifest,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorizeOperator($request);

        $filters = $this->validatedFilters($request);
        $ferries = $this->accessibleFerries($request)->pluck('id');

        if (! empty($filters['ferry_id']) && ! $ferries->contains((int) $filters['ferry_id'])) {
            abort(403);
        }

        $manifest = $this->buildManifestQuery($request, $filters)
            ->orderBy('booking_time')
            ->get();

        $filename = 'ferry-passenger-report-'.($filters['date'] ?? now()->toDateString()).'.csv';

        return response()->streamDownload(function () use ($manifest) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'booking_id',
                'pass_number',
                'passenger_name',
                'passenger_email',
                'ferry',
                'departure_time',
                'quantity',
                'total_price',
                'status',
            ]);

            foreach ($manifest as $booking) {
                fputcsv($handle, [
                    $booking->id,
                    $booking->pass_number,
                    $booking->user->name,
                    $booking->user->email,
                    $booking->ferry->name,
                    $booking->booking_time->format('Y-m-d H:i'),
                    $booking->quantity,
                    number_format((float) $booking->total_price, 2, '.', ''),
                    $booking->status,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function validatedFilters(Request $request): array
    {
        $filters = $request->validate([
            'date' => ['nullable', 'date'],
            'ferry_id' => ['nullable', 'integer', 'exists:ferries,id'],
            'hour' => ['nullable', 'integer', 'between:9,16'],
        ]);

        $filters['date'] = $filters['date'] ?? now()->toDateString();

        return $filters;
    }

    private function accessibleFerries(Request $request)
    {
        $query = Ferry::query()->orderBy('name');

        if (! $request->user()->hasRole('super_admin')) {
            $query->where('user_id', $request->user()->id);
        }

        return $query;
    }

    private function buildManifestQuery(Request $request, array $filters)
    {
        $query = FerryBooking::query()
            ->with(['ferry.island', 'user'])
            ->whereHas('ferry', function ($builder) use ($request) {
                if (! $request->user()->hasRole('super_admin')) {
                    $builder->where('user_id', $request->user()->id);
                }
            });

        $query->whereDate('booking_time', $filters['date']);

        if (! empty($filters['ferry_id'])) {
            $query->where('ferry_id', $filters['ferry_id']);
        }

        if (! empty($filters['hour'])) {
            $query->whereHour('booking_time', $filters['hour']);
        }

        return $query;
    }

    private function authorizeOperator(Request $request): void
    {
        abort_unless(
            $request->user()->hasAnyRole(['ferry_manager', 'super_admin']),
            403
        );
    }
}
