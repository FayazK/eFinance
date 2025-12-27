<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionRepository
{
    public function find(int $id): ?Transaction
    {
        return Transaction::with(['account', 'category'])->find($id);
    }

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function update(int $id, array $data): Transaction
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($data);

        return $transaction->fresh(['account', 'category']);
    }

    public function delete(int $id): bool
    {
        $transaction = Transaction::findOrFail($id);

        return $transaction->delete();
    }

    public function paginateTransactions(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'date',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Transaction::query()->with(['account', 'category']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%");
            });
        }

        if ($filters) {
            foreach ($filters as $column => $value) {
                if ($value !== null && $value !== '') {
                    if ($column === 'account_id') {
                        $query->where($column, $value);
                    } elseif ($column === 'category_id') {
                        $query->where($column, $value);
                    } elseif ($column === 'type') {
                        $query->where($column, $value);
                    } elseif ($column === 'date' && is_array($value) && count($value) === 2) {
                        $query->whereBetween('date', $value);
                    }
                }
            }
        }

        $allowedSortColumns = ['date', 'amount', 'type', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    public function getAccountTransactions(int $accountId, int $perPage = 20): LengthAwarePaginator
    {
        return Transaction::with('category')
            ->where('account_id', $accountId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
