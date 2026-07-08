<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Transfer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TransferRepositoryInterface
{
    public function find(int $id): ?Transfer;

    public function create(array $data): Transfer;

    public function paginateTransfers(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;
}
