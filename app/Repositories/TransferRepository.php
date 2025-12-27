<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Transfer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransferRepository
{
    public function find(int $id): ?Transfer
    {
        return Transfer::with([
            'withdrawalTransaction.account',
            'depositTransaction.account',
        ])->find($id);
    }

    public function create(array $data): Transfer
    {
        return Transfer::create($data);
    }

    public function paginateTransfers(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $query = Transfer::query()
            ->with([
                'withdrawalTransaction.account',
                'depositTransaction.account',
            ]);

        // Search in transaction descriptions or account names
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('withdrawalTransaction', function ($wq) use ($search) {
                    $wq->where('description', 'like', "%{$search}%")
                        ->orWhereHas('account', fn ($aq) => $aq->where('name', 'like', "%{$search}%"));
                })
                    ->orWhereHas('depositTransaction', function ($dq) use ($search) {
                        $dq->where('description', 'like', "%{$search}%")
                            ->orWhereHas('account', fn ($aq) => $aq->where('name', 'like', "%{$search}%"));
                    });
            });
        }

        // Filter by source or destination account
        if ($filters) {
            if (isset($filters['account_id'])) {
                $accountId = $filters['account_id'];
                $query->where(function ($q) use ($accountId) {
                    $q->whereHas('withdrawalTransaction', fn ($wq) => $wq->where('account_id', $accountId))
                        ->orWhereHas('depositTransaction', fn ($dq) => $dq->where('account_id', $accountId));
                });
            }

            if (isset($filters['date']) && is_array($filters['date']) && count($filters['date']) === 2) {
                $query->whereHas('withdrawalTransaction', function ($wq) use ($filters) {
                    $wq->whereBetween('date', $filters['date']);
                });
            }
        }

        $allowedSortColumns = ['created_at', 'exchange_rate'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }
}
