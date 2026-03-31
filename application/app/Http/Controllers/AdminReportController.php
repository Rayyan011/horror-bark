<?php

namespace App\Http\Controllers;

use App\Services\BookingReportService;
use App\Services\CsvExportService;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function __construct(
        private readonly BookingReportService $bookingReportService,
        private readonly CsvExportService $csvExportService
    ) {}

    public function index(Request $request)
    {
        abort_unless($request->user()->hasAnyRole(['super_admin', 'admin']), 403);

        $filters = $this->filters($request);
        $rows = $this->bookingReportService->adminRows($filters);

        return view('pages.reports.index', [
            'title' => 'Admin Reports',
            'filters' => $filters,
            'rows' => $rows,
            'summary' => $this->bookingReportService->summary($rows),
            'exportUrl' => route('admin-reports.export', array_filter($filters)),
            'islands' => $this->bookingReportService->islands(),
            'showTypeFilter' => true,
        ]);
    }

    public function export(Request $request)
    {
        abort_unless($request->user()->hasAnyRole(['super_admin', 'admin']), 403);

        $filters = $this->filters($request);
        $rows = $this->bookingReportService->adminRows($filters);

        return $this->csvExportService->download(
            'admin-booking-report-'.now()->format('Ymd-His').'.csv',
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
            'type' => ['nullable', 'in:hotel,ferry,ride,game,beach-event'],
            'island_id' => ['nullable', 'integer', 'exists:islands,id'],
        ]);
    }
}
