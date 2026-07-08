<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TransactionRepositoryInterface
{
    public function find(int $id): ?Transaction;

    public function create(array $data): Transaction;

    public function update(int $id, array $data): Transaction;

    public function delete(int $id): bool;

    public function paginateTransactions(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'date',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;

    public function getAccountTransactions(int $accountId, int $perPage = 20): LengthAwarePaginator;

    public function getRecentTransactions(int $limit = 10): \Illuminate\Support\Collection;
}
