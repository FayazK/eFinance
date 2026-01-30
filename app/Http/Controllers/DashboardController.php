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
        $user = auth()->user();
        $canViewAccounts = $user->hasPermission('accounts.read');

        return Inertia::render('dashboard', [
            // Financial data - only for users with accounts.read permission
            'distributableProfit' => $canViewAccounts ? $this->distributionService->getDistributableProfit() : null,
            'runway' => $canViewAccounts ? $this->distributionService->calculateRunway() : null,
            'financialOverview' => $canViewAccounts ? $this->dashboardService->getFinancialOverview() : null,
            'cashFlowTrend' => $canViewAccounts ? $this->dashboardService->getCashFlowTrend(6) : null,
            'recentTransactions' => $canViewAccounts ? $this->dashboardService->getRecentTransactions(10) : [],

            // Non-sensitive metrics - always visible
            'revenueMetrics' => $this->dashboardService->getRevenueMetrics(),
            'payrollSummary' => $this->dashboardService->getPayrollSummary(),
            'invoiceStatusBreakdown' => $this->dashboardService->getInvoiceStatusBreakdown(),

            // Pass permission flag to frontend
            'canViewAccounts' => $canViewAccounts,
        ]);
    }
}
