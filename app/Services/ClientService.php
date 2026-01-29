<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Client;
use App\Repositories\ClientRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ClientService
{
    public function __construct(
        private ClientRepository $clientRepository
    ) {}

    public function createClient(array $data): Client
    {
        return $this->clientRepository->create($data);
    }

    public function updateClient(int $clientId, array $data): Client
    {
        $allowedFields = [
            'name',
            'email',
            'country_id',
            'state_id',
            'city_id',
            'currency_id',
            'address',
            'phone',
            'company',
            'tax_id',
            'website',
            'notes',
        ];

        $updateData = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        return $this->clientRepository->update($clientId, $updateData);
    }

    public function deleteClient(int $clientId): bool
    {
        return $this->clientRepository->delete($clientId);
    }

    public function findClient(int $id): ?Client
    {
        return $this->clientRepository->find($id);
    }

    public function findClientByEmail(string $email): ?Client
    {
        return $this->clientRepository->findByEmail($email);
    }

    public function getAllClients(): Collection
    {
        return $this->clientRepository->all();
    }

    public function getPaginatedClients(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->clientRepository->paginateClients(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    public function isEmailTaken(string $email, ?int $excludeId = null): bool
    {
        return $this->clientRepository->existsByEmail($email, $excludeId);
    }
}
