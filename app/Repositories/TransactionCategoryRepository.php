<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Collection;

class TransactionCategoryRepository
{
    public function find(int $id): ?TransactionCategory
    {
        return TransactionCategory::find($id);
    }

    public function create(array $data): TransactionCategory
    {
        return TransactionCategory::create($data);
    }

    public function update(int $id, array $data): TransactionCategory
    {
        $category = TransactionCategory::findOrFail($id);
        $category->update($data);

        return $category->fresh();
    }

    public function delete(int $id): bool
    {
        $category = TransactionCategory::findOrFail($id);

        return $category->delete();
    }

    public function all(): Collection
    {
        return TransactionCategory::orderBy('name')->get();
    }

    public function getByType(string $type): Collection
    {
        return TransactionCategory::where('type', $type)
            ->orderBy('name')
            ->get();
    }
}
