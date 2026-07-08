<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Shareholder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ShareholderRepositoryInterface
{
    public function find(int $id): ?Shareholder;

    public function create(array $data): Shareholder;

    public function update(int $id, array $data): Shareholder;

    public function delete(int $id): bool;

    public function getAllActive(): Collection;

    public function getHumanPartners(): Collection;

    public function getOfficeReserve(): ?Shareholder;

    public function getTotalEquityPercentage(): float;

    public function paginateShareholders(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'name',
        string $sortDirection = 'asc'
    ): LengthAwarePaginator;
}
