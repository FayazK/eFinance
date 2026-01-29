<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository
{
    public function find(int $id): ?Company
    {
        return Company::find($id);
    }

    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function update(int $id, array $data): Company
    {
        $company = Company::findOrFail($id);
        $company->update($data);

        return $company->fresh();
    }

    public function delete(int $id): bool
    {
        $company = Company::findOrFail($id);

        return $company->delete();
    }

    public function all(): Collection
    {
        return Company::orderBy('name')->get();
    }

    public function paginateCompanies(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Company::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($filters) {
            foreach ($filters as $column => $value) {
                if ($value !== null && $value !== '') {
                    if ($column === 'created_at' && is_array($value) && count($value) === 2) {
                        $query->whereBetween('created_at', $value);
                    }
                }
            }
        }

        $allowedSortColumns = ['name', 'email', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }
}
