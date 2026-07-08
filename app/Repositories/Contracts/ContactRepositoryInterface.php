<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ContactRepositoryInterface
{
    public function find(int $id): ?Contact;

    public function findByEmail(string $email): ?Contact;

    public function create(array $data): Contact;

    public function update(int $id, array $data): Contact;

    public function delete(int $id): bool;

    public function existsByEmail(string $email, ?int $excludeId = null): bool;

    public function all(): Collection;

    public function getByClient(int $clientId): Collection;

    public function paginateContacts(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator;
}
