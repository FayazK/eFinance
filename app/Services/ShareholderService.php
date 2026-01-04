<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Shareholder;
use App\Repositories\ShareholderRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class ShareholderService
{
    public function __construct(
        private readonly ShareholderRepository $shareholderRepository
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

        // Validate total equity if percentage is being changed
        if (isset($data['equity_percentage']) && $data['equity_percentage'] !== (float) $shareholder->equity_percentage) {
            $currentTotal = $this->shareholderRepository->getTotalEquityPercentage();
            $newTotal = $currentTotal - (float) $shareholder->equity_percentage + $data['equity_percentage'];

            if ($newTotal > 100) {
                throw new InvalidArgumentException(
                    "Total equity cannot exceed 100%. Current: {$currentTotal}%, New total would be: {$newTotal}%"
                );
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

        return [
            'total' => $total,
            'is_valid' => $total === 100.0,
            'message' => $total === 100.0
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
