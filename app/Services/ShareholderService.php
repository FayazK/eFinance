<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\EquityHelper;
use App\Models\Shareholder;
use App\Repositories\Contracts\ShareholderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class ShareholderService
{
    public function __construct(
        private readonly ShareholderRepositoryInterface $shareholderRepository
    ) {}

    public function createShareholder(array $data): Shareholder
    {
        // Validate total doesn't exceed 100%
        $currentTotal = $this->shareholderRepository->getTotalEquityPercentage();
        $newTotal = $currentTotal + $data['equity_percentage'];

        if ($newTotal > 100) {
            throw new InvalidArgumentException(
                "Total equity cannot exceed 100%. Current: {$currentTotal}%, Adding: {$data['equity_percentage']}%"
            );
        }

        // Only one Office Reserve allowed
        if (($data['is_office_reserve'] ?? false)) {
            $existing = $this->shareholderRepository->getOfficeReserve();
            if ($existing) {
                throw new InvalidArgumentException('Office Reserve entity already exists');
            }
        }

        return $this->shareholderRepository->create($data);
    }

    public function updateShareholder(int $id, array $data): Shareholder
    {
        $shareholder = $this->shareholderRepository->find($id);

        if (! $shareholder) {
            throw new InvalidArgumentException('Shareholder not found');
        }

        // Validate the projected active-equity total whenever this edit could raise it:
        // either the percentage changes or the shareholder is being reactivated.
        $oldEquity = (float) $shareholder->equity_percentage;
        $newEquity = isset($data['equity_percentage']) ? (float) $data['equity_percentage'] : $oldEquity;

        $wasActive = $shareholder->is_active;
        $willBeActive = array_key_exists('is_active', $data) ? (bool) $data['is_active'] : $wasActive;

        $equityChanged = $newEquity !== $oldEquity;
        $reactivating = $willBeActive && ! $wasActive;

        if ($willBeActive && ($equityChanged || $reactivating)) {
            $currentTotal = $this->shareholderRepository->getTotalEquityPercentage();
            // Only subtract the old percentage if it currently counts toward the active total.
            $newTotal = $currentTotal - ($wasActive ? $oldEquity : 0.0) + $newEquity;

            if ($newTotal > 100) {
                throw new InvalidArgumentException(
                    "Total equity cannot exceed 100%. Current: {$currentTotal}%, New total would be: {$newTotal}%"
                );
            }
        }

        // Only one Office Reserve allowed
        if (($data['is_office_reserve'] ?? false)) {
            $existing = $this->shareholderRepository->getOfficeReserve();
            if ($existing && $existing->id !== $shareholder->id) {
                throw new InvalidArgumentException('Office Reserve entity already exists');
            }
        }

        return $this->shareholderRepository->update($id, $data);
    }

    public function deleteShareholder(int $id): bool
    {
        $shareholder = $this->shareholderRepository->find($id);

        if (! $shareholder) {
            throw new InvalidArgumentException('Shareholder not found');
        }

        // Prevent deletion if they have distribution history
        if ($shareholder->distributionLines()->count() > 0) {
            throw new InvalidArgumentException(
                'Cannot delete shareholder with distribution history. Consider deactivating instead.'
            );
        }

        return $this->shareholderRepository->delete($id);
    }

    public function validateEquityTotal(): array
    {
        $total = $this->shareholderRepository->getTotalEquityPercentage();
        $isValid = EquityHelper::isComplete($total);

        return [
            'total' => $total,
            'is_valid' => $isValid,
            'message' => $isValid
                ? 'Equity distribution is valid'
                : "Equity total is {$total}%. Must equal 100%.",
        ];
    }

    public function getPaginatedShareholders(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'name',
        string $sortDirection = 'asc'
    ): LengthAwarePaginator {
        return $this->shareholderRepository->paginateShareholders(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    public function findShareholder(int $id): ?Shareholder
    {
        return $this->shareholderRepository->find($id);
    }

    public function getAllActiveShareholders(): Collection
    {
        return $this->shareholderRepository->getAllActive();
    }
}
