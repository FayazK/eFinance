<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function find(int $id): ?Invoice;

    public function create(array $data): Invoice;

    public function update(int $id, array $data): Invoice;

    public function delete(int $id): bool;

    public function paginateInvoices(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'issue_date',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;

    public function getClientInvoices(int $clientId, int $perPage = 20): LengthAwarePaginator;

    public function getTotalReceivables(): int;

    public function getOverdueInvoices(): Collection;

    public function markOverdueInvoices(): int;
}
