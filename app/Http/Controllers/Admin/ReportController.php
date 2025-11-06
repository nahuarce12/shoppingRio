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
        $summary = $this->reportService->getSystemSummary();

        return view('admin.reports.index', compact('summary'));
    }

    /**
     * Generate promotion usage report.
     */
    public function promotionUsage(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'store_id' => 'nullable|exists:stores,id',
            'estado' => 'nullable|in:enviada,aceptada,rechazada',
        ]);

        $filters = $request->only(['start_date', 'end_date', 'store_id', 'estado']);

        $report = $this->reportService->getPromotionUsageReport(
            $filters['start_date'] ?? null,
            $filters['end_date'] ?? null,
            $filters['store_id'] ?? null,
            $filters['estado'] ?? null
        );

        $stores = \App\Models\Store::orderBy('nombre')->get();

        return view('admin.reports.promotion-usage', compact('report', 'filters', 'stores'));
    }

    /**
     * Generate store performance report.
     */
    public function storePerformance(Request $request)
    {
        $request->validate([
            'period_months' => 'nullable|integer|min:1|max:12',
        ]);

        $periodMonths = $request->input('period_months', 3);

    $report = $this->reportService->getStorePerformanceReport($periodMonths);

        return view('admin.reports.store-performance', compact('report', 'periodMonths'));
    }

    /**
     * Generate client category distribution report.
     */
    public function clientDistribution()
    {
        $distribution = $this->reportService->getClientCategoryDistribution();

        // Get category benefits from config
        $categoryInfo = config('shopping.client_categories', []);

        return view('admin.reports.client-distribution', compact('distribution', 'categoryInfo'));
    }

    /**
     * Generate most popular promotions report.
     */
    public function popularPromotions(Request $request)
    {
        $request->validate([
            'limit' => 'nullable|integer|min:5|max:50',
            'period_months' => 'nullable|integer|min:1|max:12',
        ]);

        $limit = $request->input('limit', 10);
        $periodMonths = $request->input('period_months', 1);

    $promotions = $this->reportService->getMostPopularPromotions($limit, $periodMonths);

        return view('admin.reports.popular-promotions', compact('promotions', 'limit', 'periodMonths'));
    }

    /**
     * Generate client activity report.
     */
    public function clientActivity(Request $request)
    {
        $request->validate([
            'period_months' => 'nullable|integer|min:1|max:12',
        ]);

        $periodMonths = $request->input('period_months', 3);

    $report = $this->reportService->getClientActivityReport($periodMonths);

        return view('admin.reports.client-activity', compact('report', 'periodMonths'));
    }

    /**
     * Generate pending approvals summary.
     */
    public function pendingApprovals()
    {
    $pending = $this->reportService->getPendingApprovalsCount();

        // Get detailed lists
        $pendingPromotions = \App\Models\Promotion::where('estado', 'pendiente')
            ->with(['store', 'store.owner'])
            ->orderBy('created_at')
            ->get();

        $pendingUsers = \App\Models\User::where('tipo_usuario', 'dueño de local')
            ->whereNull('approved_at')
            ->orderBy('created_at')
            ->get();

        return view('admin.reports.pending-approvals', compact('pending', 'pendingPromotions', 'pendingUsers'));
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
            'estado' => 'nullable|in:enviada,aceptada,rechazada',
        ]);

        $filters = $request->only(['start_date', 'end_date', 'store_id', 'estado']);

        $report = $this->reportService->getPromotionUsageReport(
            $filters['start_date'] ?? null,
            $filters['end_date'] ?? null,
            $filters['store_id'] ?? null,
            $filters['estado'] ?? null
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
                    $item->codigo_promocion,
                    $item->texto_promocion,
                    $item->store->nombre,
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
