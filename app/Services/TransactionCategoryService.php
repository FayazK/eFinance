<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TransactionCategory;
use App\Repositories\TransactionCategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class TransactionCategoryService
{
    public function __construct(
        private TransactionCategoryRepository $categoryRepository
    ) {}

    public function createCategory(array $data): TransactionCategory
    {
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $categoryId, array $data): TransactionCategory
    {
        $allowedFields = ['name', 'type', 'color'];

        $updateData = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        return $this->categoryRepository->update($categoryId, $updateData);
    }

    public function deleteCategory(int $categoryId): bool
    {
        return $this->categoryRepository->delete($categoryId);
    }

    public function findCategory(int $id): ?TransactionCategory
    {
        return $this->categoryRepository->find($id);
    }

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function getCategoriesByType(string $type): Collection
    {
        return $this->categoryRepository->getByType($type);
    }
}
