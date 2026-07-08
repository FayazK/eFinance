<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ClientRepositoryInterface
{
    public function find(int $id): ?Client;

    public function findWithTrashedProjects(int $id): ?Client;

    public function findByEmail(string $email): ?Client;

    public function create(array $data): Client;

    public function update(int $id, array $data): Client;

    public function delete(int $id): bool;

    public function existsByEmail(string $email, ?int $excludeId = null): bool;

    public function all(): Collection;

    public function paginateClients(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;
}
