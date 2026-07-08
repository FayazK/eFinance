<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Account;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AccountRepositoryInterface
{
    public function find(int $id): ?Account;

    public function create(array $data): Account;

    public function update(int $id, array $data): Account;

    public function delete(int $id): bool;

    public function all(): Collection;

    public function getActiveAccounts(): Collection;

    public function paginateAccounts(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;

    public function updateBalance(int $accountId, int $newBalance): void;
}
