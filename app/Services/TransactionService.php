<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private AccountRepository $accountRepository
    ) {}

    /**
     * Create a transaction and update account balance atomically
     */
    public function createTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            // Convert amount from major to minor units
            $amountInMinor = (int) ($data['amount'] * 100);
            $data['amount'] = $amountInMinor;

            // Create transaction
            $transaction = $this->transactionRepository->create($data);

            // Update account balance
            $account = $this->accountRepository->find($transaction->account_id);
            if ($account) {
                $newBalance = $transaction->type === 'credit'
                    ? $account->current_balance + $amountInMinor
                    : $account->current_balance - $amountInMinor;

                $this->accountRepository->updateBalance($account->id, $newBalance);
            }

            return $transaction;
        });
    }

    public function getPaginatedTransactions(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'date',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->transactionRepository->paginateTransactions(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    public function getAccountTransactions(int $accountId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->transactionRepository->getAccountTransactions($accountId, $perPage);
    }

    public function findTransaction(int $id): ?Transaction
    {
        return $this->transactionRepository->find($id);
    }

    // Phase 3: Void transaction by creating reversing entry
    // public function voidTransaction(int $transactionId): Transaction { ... }
}
