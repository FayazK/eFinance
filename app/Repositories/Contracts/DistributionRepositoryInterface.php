<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Distribution;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DistributionRepositoryInterface
{
    public function find(int $id): ?Distribution;

    public function create(array $data): Distribution;

    public function update(int $id, array $data): Distribution;

    public function delete(int $id): bool;

    public function paginateDistributions(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'period_start',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;

    public function getUndistributedProfit(Carbon $asOfDate): int;
}
