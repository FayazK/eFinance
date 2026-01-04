<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Distribution;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DistributionRepository
{
    public function find(int $id): ?Distribution
    {
        return Distribution::with(['lines.shareholder', 'lines.transaction'])
            ->find($id);
    }

    public function create(array $data): Distribution
    {
        return Distribution::create($data);
    }

    public function update(int $id, array $data): Distribution
    {
        $distribution = Distribution::findOrFail($id);
        $distribution->update($data);

        return $distribution->fresh(['lines.shareholder']);
    }

    public function delete(int $id): bool
    {
        $distribution = Distribution::findOrFail($id);

        return $distribution->delete();
    }

    public function paginateDistributions(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'period_start',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Distribution::query()->with(['lines']);

        if ($search) {
            $query->where('distribution_number', 'like', "%{$search}%");
        }

        if ($filters) {
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            if (isset($filters['period_start'])) {
                $query->where('period_start', '>=', $filters['period_start']);
            }
            if (isset($filters['period_end'])) {
                $query->where('period_end', '<=', $filters['period_end']);
            }
        }

        $allowedSortColumns = ['distribution_number', 'period_start', 'period_end', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    public function getUndistributedProfit(Carbon $asOfDate): int
    {
        // Calculate total profit that hasn't been distributed yet
        $totalDistributed = Distribution::processed()
            ->where('period_end', '<=', $asOfDate)
            ->sum('distributed_amount_pkr');

        // Get all-time totals from transactions
        $totalRevenue = Transaction::where('type', 'credit')
            ->where('date', '<=', $asOfDate)
            ->sum('reporting_amount_pkr');

        $totalExpenses = Transaction::where('type', 'debit')
            ->where('date', '<=', $asOfDate)
            ->sum('reporting_amount_pkr');

        $allTimeProfit = $totalRevenue - $totalExpenses;

        return $allTimeProfit - $totalDistributed;
    }
}
