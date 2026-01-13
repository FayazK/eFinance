<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\DistributionService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DistributionService $distributionService,
        private readonly DashboardService $dashboardService
    ) {}

    public function index(): Response
    {
        return Inertia::render('dashboard', [
            // Existing metrics
            'distributableProfit' => $this->distributionService->getDistributableProfit(),
            'runway' => $this->distributionService->calculateRunway(),

            // New metrics
            'financialOverview' => $this->dashboardService->getFinancialOverview(),
            'revenueMetrics' => $this->dashboardService->getRevenueMetrics(),
            'payrollSummary' => $this->dashboardService->getPayrollSummary(),
            'cashFlowTrend' => $this->dashboardService->getCashFlowTrend(6),
            'invoiceStatusBreakdown' => $this->dashboardService->getInvoiceStatusBreakdown(),
            'recentTransactions' => $this->dashboardService->getRecentTransactions(10),
        ]);
    }
}
