<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use App\Repositories\Contracts\ContactRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ContactService
{
    public function __construct(
        private ContactRepositoryInterface $contactRepository
    ) {}

    public function createContact(array $data): Contact
    {
        // Ensure JSON fields are properly formatted
        $data['additional_phones'] = $data['additional_phones'] ?? [];
        $data['additional_emails'] = $data['additional_emails'] ?? [];

        return $this->contactRepository->create($data);
    }

    public function updateContact(int $contactId, array $data): Contact
    {
        // Ensure JSON fields are properly formatted
        if (array_key_exists('additional_phones', $data)) {
            $data['additional_phones'] = $data['additional_phones'] ?? [];
        }
        if (array_key_exists('additional_emails', $data)) {
            $data['additional_emails'] = $data['additional_emails'] ?? [];
        }

        return $this->contactRepository->update($contactId, $data);
    }

    public function deleteContact(int $contactId): bool
    {
        return $this->contactRepository->delete($contactId);
    }

    public function findContact(int $id): ?Contact
    {
        return $this->contactRepository->find($id);
    }

    public function findContactByEmail(string $email): ?Contact
    {
        return $this->contactRepository->findByEmail($email);
    }

    public function getAllContacts(): Collection
    {
        return $this->contactRepository->all();
    }

    public function getContactsByClient(int $clientId): Collection
    {
        return $this->contactRepository->getByClient($clientId);
    }

    public function getPaginatedContacts(
        int $perPage = 15,
        ?string $search = null,
        ?array $filters = null,
        ?string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->contactRepository->paginateContacts(
            $perPage,
            $search,
            $filters,
            $sortBy,
            $sortDirection
        );
    }

    public function isEmailTaken(string $email, ?int $excludeId = null): bool
    {
        return $this->contactRepository->existsByEmail($email, $excludeId);
    }
}
