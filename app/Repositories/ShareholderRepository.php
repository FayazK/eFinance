<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Shareholder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ShareholderRepository
{
    public function find(int $id): ?Shareholder
    {
        return Shareholder::with(['distributionLines'])->find($id);
    }

    public function create(array $data): Shareholder
    {
        return Shareholder::create($data);
    }

    public function update(int $id, array $data): Shareholder
    {
        $shareholder = Shareholder::findOrFail($id);
        $shareholder->update($data);

        return $shareholder->fresh();
    }

    public function delete(int $id): bool
    {
        $shareholder = Shareholder::findOrFail($id);

        return $shareholder->delete();
    }

    public function getAllActive(): Collection
    {
        return Shareholder::active()->orderBy('name')->get();
    }

    public function getHumanPartners(): Collection
    {
        return Shareholder::active()
            ->humanPartners()
            ->orderBy('name')
            ->get();
    }

    public function getOfficeReserve(): ?Shareholder
    {
        return Shareholder::where('is_office_reserve', true)->first();
    }

    public function getTotalEquityPercentage(): float
    {
        return (float) Shareholder::active()->sum('equity_percentage');
    }

    public function paginateShareholders(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'name',
        string $sortDirection = 'asc'
    ): LengthAwarePaginator {
        $query = Shareholder::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($filters) {
            if (isset($filters['is_active'])) {
                $query->where('is_active', $filters['is_active']);
            }
            if (isset($filters['is_office_reserve'])) {
                $query->where('is_office_reserve', $filters['is_office_reserve']);
            }
        }

        $allowedSortColumns = ['name', 'equity_percentage', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }
}
