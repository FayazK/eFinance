<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Collection;

interface TransactionCategoryRepositoryInterface
{
    public function find(int $id): ?TransactionCategory;

    public function create(array $data): TransactionCategory;

    public function update(int $id, array $data): TransactionCategory;

    public function delete(int $id): bool;

    public function all(): Collection;

    public function getByType(string $type): Collection;
}
