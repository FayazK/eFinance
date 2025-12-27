<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Account;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AccountRepository
{
    public function find(int $id): ?Account
    {
        return Account::find($id);
    }

    public function create(array $data): Account
    {
        return Account::create($data);
    }

    public function update(int $id, array $data): Account
    {
        $account = Account::findOrFail($id);
        $account->update($data);

        return $account->fresh();
    }

    public function delete(int $id): bool
    {
        $account = Account::findOrFail($id);

        return $account->delete();
    }

    public function all(): Collection
    {
        return Account::query()
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();
    }

    public function getActiveAccounts(): Collection
    {
        return Account::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function paginateAccounts(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Account::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('bank_name', 'like', "%{$search}%");
            });
        }

        if ($filters) {
            foreach ($filters as $column => $value) {
                if ($value !== null && $value !== '') {
                    if ($column === 'currency_code') {
                        $query->where($column, $value);
                    } elseif ($column === 'type') {
                        $query->where($column, $value);
                    } elseif ($column === 'is_active') {
                        $query->where($column, (bool) $value);
                    }
                }
            }
        }

        $allowedSortColumns = ['name', 'type', 'currency_code', 'current_balance', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    public function updateBalance(int $accountId, int $newBalance): void
    {
        Account::where('id', $accountId)->update(['current_balance' => $newBalance]);
    }
}
