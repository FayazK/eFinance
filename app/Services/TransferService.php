<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Transfer;
use App\Repositories\AccountRepository;
use App\Repositories\TransferRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransferService
{
    public function __construct(
        private TransferRepository $transferRepository,
        private TransactionService $transactionService,
        private AccountRepository $accountRepository
    ) {}

    /**
     * Create a transfer between two accounts atomically
     *
     * @param  array{
     *     source_account_id: int,
     *     destination_account_id: int,
     *     source_amount: float,
     *     destination_amount: float,
     *     description: string|null,
     *     date: string
     * }  $data
     */
    public function createTransfer(array $data): Transfer
    {
        // Validation
        $this->validateTransferData($data);

        return DB::transaction(function () use ($data) {
            $sourceAccount = $this->accountRepository->find($data['source_account_id']);
            $destinationAccount = $this->accountRepository->find($data['destination_account_id']);

            // Calculate exchange rate: destination_amount / source_amount
            $exchangeRate = $data['source_amount'] > 0
                ? $data['destination_amount'] / $data['source_amount']
                : 1.0;

            // Create withdrawal transaction (debit from source) - TransactionService handles balance update
            $withdrawalTransaction = $this->transactionService->createTransaction([
                'account_id' => $data['source_account_id'],
                'type' => 'debit',
                'amount' => $data['source_amount'],
                'description' => $data['description'] ?? 'Transfer to '.$destinationAccount->name,
                'date' => $data['date'],
            ]);

            // Create deposit transaction (credit to destination) - TransactionService handles balance update
            $depositTransaction = $this->transactionService->createTransaction([
                'account_id' => $data['destination_account_id'],
                'type' => 'credit',
                'amount' => $data['destination_amount'],
                'description' => $data['description'] ?? 'Transfer from '.$sourceAccount->name,
                'date' => $data['date'],
            ]);

            // Create transfer record linking both transactions
            $transfer = $this->transferRepository->create([
                'withdrawal_transaction_id' => $withdrawalTransaction->id,
                'deposit_transaction_id' => $depositTransaction->id,
                'exchange_rate' => $exchangeRate,
            ]);

            return $transfer->load([
                'withdrawalTransaction.account',
                'depositTransaction.account',
            ]);
        });
    }

    public function getPaginatedTransfers(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->transferRepository->paginateTransfers(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    public function findTransfer(int $id): ?Transfer
    {
        return $this->transferRepository->find($id);
    }

    /**
     * Validate transfer data before processing
     */
    private function validateTransferData(array $data): void
    {
        // Check source and destination are different
        if ($data['source_account_id'] === $data['destination_account_id']) {
            throw new InvalidArgumentException('Source and destination accounts must be different');
        }

        // Check accounts exist
        $sourceAccount = $this->accountRepository->find($data['source_account_id']);
        $destinationAccount = $this->accountRepository->find($data['destination_account_id']);

        if (! $sourceAccount || ! $destinationAccount) {
            throw new InvalidArgumentException('Invalid account specified');
        }

        // Check amounts are positive
        if ($data['source_amount'] <= 0 || $data['destination_amount'] <= 0) {
            throw new InvalidArgumentException('Transfer amounts must be positive');
        }
    }
}
