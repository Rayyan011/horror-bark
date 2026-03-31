<?php

namespace App\Http\Controllers;

use App\Services\BookingReportService;
use App\Services\CsvExportService;
use Illuminate\Http\Request;

class OperatorReportController extends Controller
{
    public function __construct(
        private readonly BookingReportService $bookingReportService,
        private readonly CsvExportService $csvExportService
    ) {}

    public function index(Request $request, string $domain)
    {
        $this->authorizeDomain($request, $domain);

        $filters = $this->filters($request);
        $rows = $this->bookingReportService->operatorRows($request->user(), $domain, $filters);

        return view('pages.reports.index', [
            'title' => ucfirst($domain).' Reports',
            'filters' => $filters,
            'rows' => $rows,
            'summary' => $this->bookingReportService->summary($rows),
            'exportUrl' => route('operator-reports.export', ['domain' => $domain] + array_filter($filters)),
            'islands' => collect(),
            'showTypeFilter' => false,
        ]);
    }

    public function export(Request $request, string $domain)
    {
        $this->authorizeDomain($request, $domain);

        $filters = $this->filters($request);
        $rows = $this->bookingReportService->operatorRows($request->user(), $domain, $filters);

        return $this->csvExportService->download(
            $domain.'-booking-report-'.now()->format('Ymd-His').'.csv',
            $this->bookingReportService->headings(),
            $this->bookingReportService->exportRows($rows)
        );
    }

    private function filters(Request $request): array
    {
        return $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'status' => ['nullable', 'in:pending,confirmed,canceled'],
        ]);
    }

    private function authorizeDomain(Request $request, string $domain): void
    {
        $role = match ($domain) {
            'hotel' => 'hotel_manager',
            'ferry' => 'ferry_manager',
            'ride' => 'ride_manager',
            'game' => 'game_manager',
            default => null,
        };

        abort_unless($role && $request->user()->hasAnyRole([$role, 'super_admin']), 403);
    }
}
