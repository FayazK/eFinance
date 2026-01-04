<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DistributionService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DistributionService $distributionService
    ) {}

    public function index(): Response
    {
        $distributableProfit = $this->distributionService->getDistributableProfit();
        $runway = $this->distributionService->calculateRunway();

        return Inertia::render('dashboard', [
            'distributableProfit' => $distributableProfit,
            'runway' => $runway,
        ]);
    }
}
