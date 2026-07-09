<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\CurrencyHelper;
use App\Models\Account;
use App\Repositories\Contracts\AccountRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AccountService
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository
    ) {}

    public function createAccount(array $data): Account
    {
        // Convert balance from major units (dollars) to minor units (cents)
        if (isset($data['current_balance'])) {
            $data['current_balance'] = CurrencyHelper::toMinor($data['current_balance']);
        } else {
            $data['current_balance'] = 0;
        }

        return $this->accountRepository->create($data);
    }

    public function updateAccount(int $accountId, array $data): Account
    {
        $allowedFields = [
            'name',
            'type',
            'currency_code',
            'current_balance',
            'account_number',
            'bank_name',
            'is_active',
        ];

        $updateData = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                if ($field === 'current_balance') {
                    $updateData[$field] = CurrencyHelper::toMinor($data[$field]);
                } else {
                    $updateData[$field] = $data[$field];
                }
            }
        }

        return $this->accountRepository->update($accountId, $updateData);
    }

    public function deleteAccount(int $accountId): bool
    {
        return $this->accountRepository->delete($accountId);
    }

    public function findAccount(int $id): ?Account
    {
        return $this->accountRepository->find($id);
    }

    public function getAllAccounts(): Collection
    {
        return $this->accountRepository->all();
    }

    public function getActiveAccounts(): Collection
    {
        return $this->accountRepository->getActiveAccounts();
    }

    public function getPaginatedAccounts(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->accountRepository->paginateAccounts(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    public function calculateTotalNetWorth(): array
    {
        $accounts = $this->getAllAccounts();
        $rates = config('currency.default_rates');
        $totalPkr = 0;
        $currencyTotals = [];

        foreach ($accounts as $account) {
            $balanceInMajor = $account->current_balance / 100;

            // Convert to PKR using the configured rate; currencies without a
            // configured rate contribute 0 rather than a fabricated amount.
            $pkrValue = $balanceInMajor * (float) ($rates[$account->currency_code] ?? 0);

            $totalPkr += $pkrValue;

            // Group by currency
            if (! isset($currencyTotals[$account->currency_code])) {
                $currencyTotals[$account->currency_code] = [
                    'currency_code' => $account->currency_code,
                    'currency_symbol' => CurrencyHelper::getSymbol($account->currency_code),
                    'currency_name' => CurrencyHelper::getName($account->currency_code),
                    'balance' => 0,
                    'pkr_value' => 0,
                ];
            }

            $currencyTotals[$account->currency_code]['balance'] += $balanceInMajor;
            $currencyTotals[$account->currency_code]['pkr_value'] += $pkrValue;
        }

        // Format the currency totals
        foreach ($currencyTotals as $code => &$total) {
            $total['formatted_balance'] = CurrencyHelper::format($total['balance'], $code);
            $total['formatted_pkr_value'] = CurrencyHelper::format($total['pkr_value'], 'PKR');
        }

        return [
            'total_pkr' => $totalPkr,
            'formatted_total_pkr' => CurrencyHelper::format($totalPkr, 'PKR'),
            'currency_breakdown' => array_values($currencyTotals),
        ];
    }
}
