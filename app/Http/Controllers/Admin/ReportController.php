<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

/**
 * Admin controller for generating reports and statistics.
 * Provides insights into promotions, usage, stores, and clients.
 */
class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {
    }

    /**
     * Display the main reports dashboard.
     */
    public function index()
    {
        // Reports are now integrated in the main dashboard
        return redirect()->route('admin.dashboard', ['section' => 'reportes']);
    }

    /**
     * Generate promotion usage report.
     */
    public function promotionUsage(Request $request)
    {
        // Reports are now integrated in the main dashboard
        return redirect()->route('admin.dashboard', ['section' => 'reportes']);
    }

    /**
     * Generate store performance report.
     */
    public function storePerformance(Request $request)
    {
        // Reports are now integrated in the main dashboard
        return redirect()->route('admin.dashboard', ['section' => 'reportes']);
    }

    /**
     * Generate client category distribution report.
     */
    public function clientDistribution()
    {
        // Reports are now integrated in the main dashboard
        return redirect()->route('admin.dashboard', ['section' => 'reportes']);
    }

    /**
     * Generate most popular promotions report.
     */
    public function popularPromotions(Request $request)
    {
        // Reports are now integrated in the main dashboard
        return redirect()->route('admin.dashboard', ['section' => 'reportes']);
    }

    /**
     * Generate client activity report.
     */
    public function clientActivity(Request $request)
    {
        // Reports are now integrated in the main dashboard
        return redirect()->route('admin.dashboard', ['section' => 'reportes']);
    }

    /**
     * Generate pending approvals summary.
     */
    public function pendingApprovals()
    {
        // Reports are now integrated in the main dashboard
        return redirect()->route('admin.dashboard', ['section' => 'reportes']);
    }

    /**
     * Export promotion usage report to CSV.
     */
    public function exportCSV(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'store_id' => 'nullable|exists:stores,id',
            'status' => 'nullable|in:enviada,aceptada,rechazada',
        ]);

        $filters = $request->only(['start_date', 'end_date', 'store_id', 'status']);

        $report = $this->reportService->getPromotionUsageReport(
            $filters['start_date'] ?? null,
            $filters['end_date'] ?? null,
            $filters['store_id'] ?? null,
            $filters['status'] ?? null
        );

        // Generate CSV
        $filename = 'promotion-usage-report-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Código Promoción',
                'Texto Promoción',
                'Local',
                'Total Solicitudes',
                'Aceptadas',
                'Rechazadas',
                'Pendientes',
                'Tasa Aceptación'
            ]);

            // Data rows
            foreach ($report['by_promotion'] as $item) {
                fputcsv($file, [
                    $item->code_promocion,
                    $item->description_promocion,
                    $item->store->name,
                    $item->total_requests,
                    $item->accepted_count,
                    $item->rejected_count,
                    $item->pending_count,
                    number_format($item->acceptance_rate, 2) . '%'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
